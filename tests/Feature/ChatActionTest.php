<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Conversation;
use App\Models\ConversationEvent;
use App\Models\User;
use App\Models\Department;
use App\Models\ContactReason;
use App\Support\Tenant;
use App\Actions\AcceptConversationAction;
use App\Actions\ResolveConversationAction;
use App\Actions\TransferConversationAction;
use App\Actions\SnoozeConversationAction;
use App\Actions\ReturnConversationToQueueAction;
use App\Events\ConversationUpdated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ChatActionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Tenant::clear();
    }

    public function test_accept_conversation_action(): void
    {
        Event::fake();

        $company = Company::factory()->create();
        Tenant::set($company);

        $user = User::factory()->create(['company_id' => $company->id]);
        $conversation = Conversation::factory()->create(['company_id' => $company->id]);

        $action = new AcceptConversationAction();
        $action->execute($conversation, $user->id);

        $conversation->refresh();
        $this->assertEquals($user->id, $conversation->assigned_user_id);
        $this->assertEquals('open', $conversation->status);
        $this->assertEquals('human', $conversation->handler_type);
        $this->assertEquals($user->id, $conversation->handler_id);

        $this->assertDatabaseHas('conversation_events', [
            'company_id' => $company->id,
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'event_type' => 'assigned',
        ]);

        Event::assertDispatched(ConversationUpdated::class, function ($event) use ($conversation) {
            return $event->conversation->id === $conversation->id;
        });
    }

    public function test_resolve_conversation_action(): void
    {
        Event::fake();

        $company = Company::factory()->create();
        Tenant::set($company);

        $user = User::factory()->create(['company_id' => $company->id]);
        $conversation = Conversation::factory()->create([
            'company_id' => $company->id,
            'status' => 'open',
            'assigned_user_id' => $user->id,
        ]);

        $action = new ResolveConversationAction();
        $action->execute($conversation, $user->id);

        $conversation->refresh();
        $this->assertEquals('closed', $conversation->status);
        $this->assertEquals('none', $conversation->handler_type);
        $this->assertNull($conversation->handler_id);
        $this->assertNotNull($conversation->resolved_at);

        $this->assertDatabaseHas('conversation_events', [
            'company_id' => $company->id,
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'event_type' => 'status_changed',
        ]);

        Event::assertDispatched(ConversationUpdated::class);
    }

    public function test_transfer_conversation_action(): void
    {
        Event::fake();

        $company = Company::factory()->create();
        Tenant::set($company);

        $user = User::factory()->create(['company_id' => $company->id]);
        $targetUser = User::factory()->create(['company_id' => $company->id]);
        $targetDept = Department::create(['company_id' => $company->id, 'name' => 'Ventas']);
        $conversation = Conversation::factory()->create([
            'company_id' => $company->id,
            'assigned_user_id' => $user->id,
        ]);

        $action = new TransferConversationAction();
        $action->execute($conversation, $user->id, $targetUser->id, $targetDept->id);

        $conversation->refresh();
        $this->assertEquals($targetUser->id, $conversation->assigned_user_id);
        $this->assertEquals($targetDept->id, $conversation->department_id);
        $this->assertEquals('human', $conversation->handler_type);

        $this->assertDatabaseHas('conversation_events', [
            'company_id' => $company->id,
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'event_type' => 'transferred',
        ]);

        Event::assertDispatched(ConversationUpdated::class);
    }

    public function test_snooze_conversation_action(): void
    {
        Event::fake();

        $company = Company::factory()->create();
        Tenant::set($company);

        $user = User::factory()->create(['company_id' => $company->id]);
        $conversation = Conversation::factory()->create([
            'company_id' => $company->id,
            'status' => 'open',
        ]);

        $until = now()->addHours(2);
        $action = new SnoozeConversationAction();
        $action->execute($conversation, $user->id, $until);

        $conversation->refresh();
        $this->assertEquals('snoozed', $conversation->status);
        $this->assertEquals('none', $conversation->handler_type);
        $this->assertEquals($until->toDateTimeString(), $conversation->snoozed_until->toDateTimeString());

        $this->assertDatabaseHas('conversation_events', [
            'company_id' => $company->id,
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'event_type' => 'snoozed',
        ]);

        Event::assertDispatched(ConversationUpdated::class);
    }

    public function test_return_conversation_to_queue_action(): void
    {
        Event::fake();

        $company = Company::factory()->create();
        Tenant::set($company);

        $user = User::factory()->create(['company_id' => $company->id]);
        $conversation = Conversation::factory()->create([
            'company_id' => $company->id,
            'assigned_user_id' => $user->id,
            'status' => 'open',
            'handler_type' => 'human',
            'handler_id' => $user->id,
        ]);

        $action = new ReturnConversationToQueueAction();
        $action->execute($conversation, $user->id);

        $conversation->refresh();
        $this->assertNull($conversation->assigned_user_id);
        $this->assertEquals('open', $conversation->status);
        $this->assertEquals('none', $conversation->handler_type);
        $this->assertNull($conversation->handler_id);

        $this->assertDatabaseHas('conversation_events', [
            'company_id' => $company->id,
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'event_type' => 'returned_to_queue',
        ]);

        Event::assertDispatched(ConversationUpdated::class);
    }
}
