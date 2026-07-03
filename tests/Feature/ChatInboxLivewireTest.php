<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Models\Conversation;
use App\Models\Contact;
use App\Models\ChannelConnection;
use App\Filament\Pages\ChatInbox;
use App\Support\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ChatInboxLivewireTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Tenant::clear();
    }

    public function test_chats_route_redirects_unauthenticated_users(): void
    {
        // El panel de Filament redirige al login del panel si no está autenticado
        $response = $this->get('/admin/chat-inbox');
        $response->assertRedirect('/admin/login');
    }

    public function test_chat_inbox_renders_successfully_for_authenticated_users(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);

        $response = $this->actingAs($user)->get('/admin/chat-inbox');
        $response->assertStatus(200);

        Tenant::set($company);
        Livewire::actingAs($user)
            ->test(ChatInbox::class)
            ->assertStatus(200);
    }

    public function test_chat_inbox_lists_tenant_conversations(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        Tenant::set($company);

        $contact = Contact::factory()->create(['company_id' => $company->id, 'name' => 'Marcos Rojo']);
        $connection = ChannelConnection::factory()->create(['company_id' => $company->id]);
        $conversation = Conversation::factory()->create([
            'company_id' => $company->id,
            'contact_id' => $contact->id,
            'channel_connection_id' => $connection->id,
            'status' => 'open',
            'assigned_user_id' => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test(ChatInbox::class)
            ->assertSee('Marcos Rojo');
    }

    public function test_chat_inbox_can_accept_and_resolve_conversation(): void
    {
        $company = Company::factory()->create();
        $user = User::factory()->create(['company_id' => $company->id]);
        Tenant::set($company);

        $contact = Contact::factory()->create(['company_id' => $company->id, 'name' => 'Edinson Cavani']);
        $connection = ChannelConnection::factory()->create(['company_id' => $company->id]);
        $conversation = Conversation::factory()->create([
            'company_id' => $company->id,
            'contact_id' => $contact->id,
            'channel_connection_id' => $connection->id,
            'status' => 'open',
            'assigned_user_id' => null,
        ]);

        // 1. Aceptar la conversación desde Livewire
        Livewire::actingAs($user)
            ->test(ChatInbox::class)
            ->set('activeConversationId', $conversation->id)
            ->call('accept')
            ->assertSet('activeConversationId', $conversation->id);

        $this->assertEquals($user->id, $conversation->fresh()->assigned_user_id);
        $this->assertEquals('open', $conversation->fresh()->status);

        // 2. Resolver la conversación desde Livewire
        Livewire::actingAs($user)
            ->test(ChatInbox::class)
            ->set('activeConversationId', $conversation->id)
            ->call('confirmResolve')
            ->assertSet('activeConversationId', null);

        $this->assertEquals('closed', $conversation->fresh()->status);
    }
}
