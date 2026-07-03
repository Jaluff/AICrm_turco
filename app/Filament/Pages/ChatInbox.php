<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Models\Department;
use App\Models\Tag;
use App\Models\QuickReply;
use App\Support\Tenant;
use App\Actions\SendMessageAction;
use App\Actions\AcceptConversationAction;
use App\Actions\ResolveConversationAction;
use App\Actions\TransferConversationAction;
use App\Actions\SnoozeConversationAction;
use App\Actions\ReturnConversationToQueueAction;
use Carbon\Carbon;

use Livewire\Attributes\Url;

class ChatInbox extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static string $view = 'filament.pages.chat-inbox';
    
    protected static ?string $navigationLabel = 'Bandeja de Entrada';
    
    protected static ?string $title = '';

    public function getHeader(): ?\Illuminate\Contracts\View\View
    {
        return null;
    }

    public function getMaxContentWidth(): ?string
    {
        return 'full';
    }

    public function getBreadcrumbs(): array
    {
        return [];
    }

    // Filtros
    #[Url(except: '')]
    public string $search = '';

    #[Url(except: 'mine')]
    public string $filterScope = 'mine'; // mine, unassigned, all

    #[Url(except: 'open')]
    public string $filterStatus = 'open'; // open, snoozed, closed

    public ?int $filterDepartmentId = null;
    public ?int $filterUserId = null;
    public ?int $filterTagId = null;

    // Estado activo
    public ?int $activeConversationId = null;
    public string $newMessageBody = '';

    // Modales y sus inputs
    public bool $showSnoozeModal = false;
    public int $snoozeHours = 2;

    public $showTransferModal = false;
    public $transferUserId = null;
    public $transferDepartmentId = null;
    public string $transferReason = '';

    // Resolve Modal
    public $showResolveModal = false;
    public $resolveReasonId = null;
    public $resolveWithoutGoodbye = false;

    // Schedule Modal
    public bool $showScheduleModal = false;
    public string $scheduleSendAt = '';
    public ?int $scheduleTemplateId = null;
    public string $scheduleBody = '';
    public array $scheduleVariables = [];
    public array $availableTemplates = [];

    public function openScheduleModal(): void
    {
        $conversation = Conversation::find($this->activeConversationId);
        if (!$conversation) {
            return;
        }

        $this->scheduleSendAt = now()->addHour()->format('Y-m-d\TH:i');
        $this->scheduleTemplateId = null;
        $this->scheduleBody = '';
        $this->scheduleVariables = [];

        // Load templates for this channel connection
        $this->availableTemplates = \App\Models\MessageTemplate::where('channel_connection_id', $conversation->channel_connection_id)
            ->where('status', 'APPROVED')
            ->get()
            ->toArray();

        $this->showScheduleModal = true;
    }

    public function closeScheduleModal(): void
    {
        $this->showScheduleModal = false;
    }

    public function updatedScheduleTemplateId($value): void
    {
        $this->scheduleVariables = [];
        $this->scheduleBody = '';

        if ($value) {
            $template = \App\Models\MessageTemplate::find($value);
            if ($template && is_array($template->variables)) {
                foreach ($template->variables as $var) {
                    $this->scheduleVariables[] = '';
                }
            }
        }
    }

    public function saveScheduleMessage(): void
    {
        $this->validate([
            'scheduleSendAt' => 'required',
            'scheduleTemplateId' => 'nullable|integer',
            'scheduleBody' => 'required_without:scheduleTemplateId|string',
        ]);

        $conversation = Conversation::find($this->activeConversationId);
        if (!$conversation) {
            return;
        }

        $sendAt = Carbon::parse($this->scheduleSendAt);
        if ($sendAt->isPast()) {
            $this->addError('scheduleSendAt', 'La fecha de envío debe ser en el futuro.');
            return;
        }

        \App\Models\ScheduledMessage::create([
            'company_id' => $conversation->company_id,
            'contact_id' => $conversation->contact_id,
            'conversation_id' => $conversation->id,
            'channel_connection_id' => $conversation->channel_connection_id,
            'message_template_id' => $this->scheduleTemplateId ?: null,
            'body' => $this->scheduleTemplateId ? null : $this->scheduleBody,
            'variables' => $this->scheduleTemplateId ? $this->scheduleVariables : null,
            'send_at' => $sendAt,
            'status' => 'pending',
            'created_by' => auth()->id(),
        ]);

        $this->closeScheduleModal();
        
        $this->dispatch('scheduled-message-created');
    }

    /**
     * Listeners para eventos en tiempo real con Laravel Echo/Reverb.
     */
    public function getListeners(): array
    {
        $companyId = auth()->user()?->company_id;
        $listeners = [];

        if ($companyId) {
            $listeners["echo-private:company.{$companyId},ConversationUpdated"] = 'refreshList';
            $listeners["echo-private:company.{$companyId},.ConversationUpdated"] = 'refreshList';
        }

        if ($this->activeConversationId) {
            $listeners["echo-private:conversation.{$this->activeConversationId},MessageSent"] = 'refreshActiveConversation';
            $listeners["echo-private:conversation.{$this->activeConversationId},.MessageSent"] = 'refreshActiveConversation';
        }

        return $listeners;
    }

    public function mount(): void
    {
        // Forzar inicialización de Tenant en base al usuario logueado
        if (auth()->check() && auth()->user()->company) {
            Tenant::set(auth()->user()->company);
        }
    }

    public function refreshList(): void
    {
        // Refresca el componente
    }

    public function setScope(string $scope): void
    {
        $this->filterScope = $scope;
    }

    public function resetFilters(): void
    {
        $this->filterDepartmentId = null;
        $this->filterUserId = null;
        $this->filterStatus = 'open';
        $this->filterTagId = null;
    }

    public function refreshActiveConversation(): void
    {
        // Refresca el componente para actualizar mensajes
    }

    public function selectConversation(int $id): void
    {
        $this->activeConversationId = $id;
        $this->newMessageBody = '';
    }

    public function selectAndAccept(int $id, AcceptConversationAction $action): void
    {
        $this->selectConversation($id);
        $this->accept($action);
    }

    public function selectAndResolve(int $id): void
    {
        $this->selectConversation($id);
        $this->openResolveModal();
    }

    public function selectAndReturnToQueue(int $id, ReturnConversationToQueueAction $action): void
    {
        $this->selectConversation($id);
        $this->returnToQueue($action);
    }

    public function selectAndSnooze(int $id): void
    {
        $this->selectConversation($id);
        $this->openSnoozeModal();
    }

    public function selectAndTransfer(int $id): void
    {
        $this->selectConversation($id);
        $this->openTransferModal();
    }

    public function sendMessage(SendMessageAction $action): void
    {
        $this->validate([
            'newMessageBody' => 'required|string|min:1',
        ]);

        $conversation = Conversation::find($this->activeConversationId);
        if (!$conversation) {
            return;
        }

        $action->execute(
            conversation: $conversation,
            body: $this->newMessageBody,
            senderType: 'human',
            senderUserId: auth()->id()
        );

        $this->newMessageBody = '';
        $this->dispatch('message-sent');
    }

    public function accept(AcceptConversationAction $action): void
    {
        $conversation = Conversation::find($this->activeConversationId);
        if ($conversation) {
            $action->execute($conversation, auth()->id());
        }
    }

    public function openResolveModal(): void
    {
        $this->showResolveModal = true;
    }

    public function closeResolveModal(): void
    {
        $this->showResolveModal = false;
        $this->resolveReasonId = null;
        $this->resolveWithoutGoodbye = false;
    }

    public function confirmResolve(ResolveConversationAction $action): void
    {
        $conversation = Conversation::find($this->activeConversationId);
        if ($conversation) {
            // Assign reason if provided
            if ($this->resolveReasonId) {
                // Actually need to create a ConversationReasonAssignment, but for now we might just need to attach the reason. Let's assume there is a relationship or an action for it.
                // According to DB, there is a `conversation_reason_assignments` table.
                \App\Models\ConversationReasonAssignment::create([
                    'conversation_id' => $conversation->id,
                    'contact_reason_id' => $this->resolveReasonId,
                    'assigned_by_user_id' => auth()->id(),
                ]);
            }

            // In the future, resolveWithoutGoodbye flag could be passed to the action to prevent a generic message
            $action->execute($conversation, auth()->id());
            $this->activeConversationId = null;
        }
        $this->closeResolveModal();
    }

    public function keepWithMe(): void
    {
        $conversation = Conversation::find($this->activeConversationId);
        if ($conversation) {
            $conversation->update([
                'assigned_user_id' => auth()->id(),
                'status' => 'open', // ensure it's open and assigned to me
            ]);
        }
    }

    public function returnToQueue(ReturnConversationToQueueAction $action): void
    {
        $conversation = Conversation::find($this->activeConversationId);
        if ($conversation) {
            $action->execute($conversation, auth()->id());
            $this->activeConversationId = null;
        }
    }

    public function openSnoozeModal(): void
    {
        $this->showSnoozeModal = true;
    }

    public function closeSnoozeModal(): void
    {
        $this->showSnoozeModal = false;
    }

    public function snooze(SnoozeConversationAction $action): void
    {
        $conversation = Conversation::find($this->activeConversationId);
        if ($conversation) {
            $until = now()->addHours($this->snoozeHours);
            $action->execute($conversation, auth()->id(), $until);
            $this->activeConversationId = null;
        }
        $this->closeSnoozeModal();
    }

    public function openTransferModal(): void
    {
        $conversation = Conversation::find($this->activeConversationId);
        if ($conversation) {
            $this->transferUserId = $conversation->assigned_user_id;
            $this->transferDepartmentId = $conversation->department_id;
        }
        $this->transferReason = '';
        $this->showTransferModal = true;
    }

    public function closeTransferModal(): void
    {
        $this->showTransferModal = false;
        $this->transferReason = '';
    }

    public function transfer(TransferConversationAction $action): void
    {
        $conversation = Conversation::find($this->activeConversationId);
        if ($conversation) {
            $action->execute(
                $conversation,
                auth()->id(),
                $this->transferUserId ? (int) $this->transferUserId : null,
                $this->transferDepartmentId ? (int) $this->transferDepartmentId : null,
                $this->transferReason ?: null
            );
            
            if ($conversation->fresh()->assigned_user_id !== auth()->id()) {
                $this->activeConversationId = null;
            }
        }
        $this->closeTransferModal();
    }

    protected function getViewData(): array
    {
        // 1. Obtener conversaciones aplicando filtros
        $query = Conversation::with(['contact', 'channelConnection', 'assignedUser'])
            ->orderBy('last_message_at', 'desc');

        // Filtro de estado
        if ($this->filterStatus === 'open') {
            $query->whereIn('status', ['open', 'pending_human']);
        } elseif ($this->filterStatus === 'snoozed') {
            $query->where('status', 'snoozed');
        } elseif ($this->filterStatus === 'closed') {
            $query->where('status', 'closed');
        }

        // Filtro de alcance/asignación
        if ($this->filterScope === 'mine') {
            $query->where('assigned_user_id', auth()->id());
        } elseif ($this->filterScope === 'unassigned') {
            $query->whereNull('assigned_user_id');
        }

        // Filtro por departamento
        if ($this->filterDepartmentId) {
            $query->where('department_id', $this->filterDepartmentId);
        }

        // Filtro por agente
        if ($this->filterUserId) {
            $query->where('assigned_user_id', $this->filterUserId);
        }

        // Filtro por etiqueta
        if ($this->filterTagId) {
            $query->whereHas('tags', fn ($q) => $q->where('tags.id', $this->filterTagId));
        }

        // Filtro de búsqueda
        if ($this->search !== '') {
            $query->whereHas('contact', function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%");
            });
        }

        $conversations = $query->get();

        // 2. Obtener conversación activa y sus mensajes
        $activeConversation = null;
        $messages = collect();

        if ($this->activeConversationId) {
            $activeConversation = Conversation::with(['contact', 'channelConnection', 'assignedUser', 'department'])
                ->find($this->activeConversationId);
            
            if ($activeConversation) {
                $messages = Message::where('conversation_id', $activeConversation->id)
                    ->orderBy('created_at', 'asc')
                    ->get();
            }
        }

        // 3. Catálogos para filtros y modales
        $agents = User::orderBy('name')->get();
        $departments = Department::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();
        $contactReasons = \App\Models\ContactReason::where('active', true)->orderBy('name')->get();
        $quickReplies = QuickReply::where('is_active', true)
            ->orderBy('shortcut')
            ->get(['id', 'shortcut', 'title', 'body', 'department_id']);

        // 4. Contadores para los globos (Whaticket)
        $mineCount = Conversation::where('assigned_user_id', auth()->id())
            ->whereIn('status', ['open', 'pending_human'])
            ->count();

        $unassignedCount = Conversation::whereNull('assigned_user_id')
            ->whereIn('status', ['open', 'pending_human'])
            ->count();

        $allCount = Conversation::whereIn('status', ['open', 'pending_human'])
            ->count();

        return [
            'conversations'     => $conversations,
            'activeConversation' => $activeConversation,
            'messages'          => $messages,
            'agents'            => $agents,
            'departments'       => $departments,
            'tags'              => $tags,
            'contactReasons'    => $contactReasons,
            'quickReplies'      => $quickReplies,
            'mineCount'         => $mineCount,
            'unassignedCount'   => $unassignedCount,
            'allCount'          => $allCount,
        ];
    }
}
