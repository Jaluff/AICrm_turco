<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Support\Tenant;
use App\Models\Company;
use App\Models\Contact;
use App\Models\ContactIdentity;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\ChannelConnection;
use App\Models\WebhookEvent;
use App\ChannelAdapters\WhatsAppCloudAdapter;
use Illuminate\Support\Carbon;
use Illuminate\Queue\SerializesModels;
use Exception;

class ProcessIncomingWebhookJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public WebhookEvent $event
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if (!$this->event->company_id) {
                throw new Exception("No se pudo resolver la empresa para este evento de webhook.");
            }

            // Establecer el tenant activo para esta tarea asíncrona
            $company = Company::find($this->event->company_id);
            if (!$company) {
                throw new Exception("La empresa ID {$this->event->company_id} no existe.");
            }
            Tenant::set($company);

            if ($this->event->channel_type === 'whatsapp_cloud') {
                $adapter = new WhatsAppCloudAdapter();
                $normalizedMessages = $adapter->normalizeIncomingWebhook($this->event->payload);

                foreach ($normalizedMessages as $normalizedMessage) {
                    // 1. Evitar duplicados por external_message_id
                    if ($normalizedMessage->externalMessageId) {
                        $exists = Message::where('external_message_id', $normalizedMessage->externalMessageId)->exists();
                        if ($exists) {
                            continue;
                        }
                    }

                    // 2. Buscar o crear el Contacto e Identidad
                    $identity = ContactIdentity::where('channel_type', 'whatsapp_cloud')
                        ->where('external_id', $normalizedMessage->senderPhone)
                        ->first();

                    if ($identity) {
                        $contact = $identity->contact;
                    } else {
                        $contact = Contact::create([
                            'name' => $normalizedMessage->senderName,
                            'phone' => $normalizedMessage->senderPhone,
                            'opt_in' => true,
                            'opt_out' => false,
                        ]);

                        $identity = ContactIdentity::create([
                            'contact_id' => $contact->id,
                            'channel_type' => 'whatsapp_cloud',
                            'external_id' => $normalizedMessage->senderPhone,
                        ]);
                    }

                    // 3. Resolver la conexión del canal
                    $phoneNumberId = $normalizedMessage->metadata['phone_number_id'] ?? null;
                    $channelConnection = ChannelConnection::where('external_phone_number_id', $phoneNumberId)->first();
                    if (!$channelConnection) {
                        throw new Exception("No se encontró la conexión del canal para el phone_number_id: {$phoneNumberId}");
                    }

                    // 4. Buscar o crear la Conversación activa
                    $conversation = Conversation::where('contact_id', $contact->id)
                        ->where('channel_connection_id', $channelConnection->id)
                        ->where('status', '!=', 'closed')
                        ->first();

                    if (!$conversation) {
                        $conversation = Conversation::create([
                            'contact_id' => $contact->id,
                            'channel_connection_id' => $channelConnection->id,
                            'status' => 'open',
                            'handler_type' => 'none',
                            'last_message_at' => Carbon::createFromTimestamp($normalizedMessage->timestamp),
                        ]);
                    } else {
                        $conversation->update([
                            'last_message_at' => Carbon::createFromTimestamp($normalizedMessage->timestamp),
                        ]);
                    }

                    // 5. Crear el Mensaje inbound
                    Message::create([
                        'conversation_id' => $conversation->id,
                        'contact_id' => $contact->id,
                        'channel_connection_id' => $channelConnection->id,
                        'sender_type' => 'contact',
                        'external_message_id' => $normalizedMessage->externalMessageId,
                        'direction' => 'inbound',
                        'type' => 'text',
                        'body' => $normalizedMessage->messageText,
                        'status' => 'sent',
                        'sent_at' => Carbon::createFromTimestamp($normalizedMessage->timestamp),
                        'metadata' => $normalizedMessage->rawPayload,
                    ]);

                    // 6. Verificar horario de atención
                    $businessHoursService = new \App\Services\BusinessHoursService();
                    $isOpen = true;
                    $messageTime = Carbon::createFromTimestamp($normalizedMessage->timestamp);

                    if ($conversation->department_id) {
                        $isOpen = $businessHoursService->isOpenForDepartment(
                            $conversation->department ?: $conversation->load('department')->department,
                            $messageTime
                        );
                    } else {
                        $isOpen = $businessHoursService->isOpenForCompany($company, $messageTime);
                    }

                    if (!$isOpen) {
                        // Obtener mensaje de ausencia
                        $awayMessage = null;
                        if ($conversation->department_id) {
                            $awayMessage = $conversation->department->away_message;
                        }
                        if (!$awayMessage) {
                            $awayMessage = $company->away_message;
                        }

                        if ($awayMessage) {
                            $sendMessageAction = new \App\Actions\SendMessageAction();
                            $sendMessageAction->execute(
                                conversation: $conversation,
                                body: $awayMessage,
                                senderType: 'system'
                            );
                        }

                        $conversation->update([
                            'status' => 'pending_human',
                            'handler_type' => 'none',
                        ]);
                    }
                }
            }

            $this->event->update([
                'status' => 'processed',
            ]);
        } catch (Exception $e) {
            $this->event->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            throw $e;
        } finally {
            Tenant::clear();
        }
    }
}
