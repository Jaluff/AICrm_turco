<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Contact;
use App\Models\ChannelConnection;
use App\Models\MessageTemplate;
use App\Models\ScheduledMessage;
use App\Models\Message;
use App\Models\Conversation;
use App\Support\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ScheduledMessageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Tenant::clear();
    }

    public function test_can_create_and_send_scheduled_text_message(): void
    {
        // 1. Setup company, contact, connection
        $company = Company::factory()->create();
        Tenant::set($company);

        $contact = Contact::factory()->create(['company_id' => $company->id, 'phone' => '5491122334455']);
        $connection = ChannelConnection::factory()->create([
            'company_id' => $company->id,
            'type' => ChannelConnection::TYPE_WHATSAPP_CLOUD,
            'external_phone_number_id' => 'phone_id_123',
            'access_token' => 'token_abc',
        ]);

        // 2. Create Scheduled Message (Simple Text)
        $scheduledMessage = ScheduledMessage::create([
            'company_id' => $company->id,
            'contact_id' => $contact->id,
            'channel_connection_id' => $connection->id,
            'body' => 'Hola, este es un recordatorio.',
            'send_at' => now()->subMinute(), // Past date so it is processed
            'status' => 'pending',
        ]);

        // 3. Fake HTTP for the real API call
        Http::fake([
            'https://graph.facebook.com/v20.0/phone_id_123/messages' => Http::response([
                'messaging_product' => 'whatsapp',
                'messages' => [['id' => 'wamid.test_text_message_123']]
            ], 200)
        ]);

        Tenant::clear(); // Clear tenant context so command can query across companies

        // 4. Run the Artisan command
        $this->artisan('app:send-scheduled-messages')
            ->expectsOutput('Iniciando procesamiento de mensajes programados...')
            ->expectsOutput('Procesando 1 mensaje(s) programado(s)...')
            ->expectsOutput('Mensaje programado ID ' . $scheduledMessage->id . ' procesado con éxito.')
            ->assertExitCode(0);

        // 5. Verify conversation was automatically created
        $conversation = Conversation::where('contact_id', $contact->id)
            ->where('channel_connection_id', $connection->id)
            ->first();
        $this->assertNotNull($conversation);

        // 6. Verify ScheduledMessage was updated with message reference
        $scheduledMessage->refresh();
        $this->assertNotNull($scheduledMessage->sent_message_id);

        // 7. Process outgoing job manually (since queue is sync in tests by default, the job runs inline during dispatch)
        // Verify Message was created and status updated to sent
        $message = Message::find($scheduledMessage->sent_message_id);
        $this->assertNotNull($message);
        $this->assertEquals('sent', $message->status);
        $this->assertEquals('wamid.test_text_message_123', $message->external_message_id);

        // Verify ScheduledMessage status is sent
        $this->assertEquals('sent', $scheduledMessage->status);
    }

    public function test_can_create_and_send_scheduled_template_message(): void
    {
        // 1. Setup company, contact, connection, template
        $company = Company::factory()->create();
        Tenant::set($company);

        $contact = Contact::factory()->create(['company_id' => $company->id, 'phone' => '5491122334455']);
        $connection = ChannelConnection::factory()->create([
            'company_id' => $company->id,
            'type' => ChannelConnection::TYPE_WHATSAPP_CLOUD,
            'external_phone_number_id' => 'phone_id_123',
            'access_token' => 'token_abc',
        ]);

        $template = MessageTemplate::create([
            'company_id' => $company->id,
            'channel_connection_id' => $connection->id,
            'name' => 'hello_world_template',
            'language' => 'en_US',
            'category' => 'UTILITY',
            'status' => 'APPROVED',
            'components' => [
                ['type' => 'BODY', 'text' => 'Hello {{1}}, welcome to our platform.']
            ],
            'variables' => ['customer_name'],
        ]);

        // 2. Create Scheduled Message (Template)
        $scheduledMessage = ScheduledMessage::create([
            'company_id' => $company->id,
            'contact_id' => $contact->id,
            'channel_connection_id' => $connection->id,
            'message_template_id' => $template->id,
            'variables' => ['Juan Perez'],
            'send_at' => now()->subMinute(),
            'status' => 'pending',
        ]);

        // 3. Fake HTTP for the template API call
        Http::fake([
            'https://graph.facebook.com/v20.0/phone_id_123/messages' => Http::response([
                'messaging_product' => 'whatsapp',
                'messages' => [['id' => 'wamid.test_template_message_456']]
            ], 200)
        ]);

        Tenant::clear();

        // 4. Run the Artisan command
        $this->artisan('app:send-scheduled-messages')->assertExitCode(0);

        // 5. Verify local body rendering and metadata
        $scheduledMessage->refresh();
        $message = Message::find($scheduledMessage->sent_message_id);
        $this->assertNotNull($message);
        $this->assertEquals('template', $message->type);
        $this->assertEquals('Hello Juan Perez, welcome to our platform.', $message->body);
        $this->assertEquals('hello_world_template', $message->metadata['template_name']);
        $this->assertEquals('en_US', $message->metadata['language_code']);
        
        // Verify parameters were built correctly
        $this->assertEquals('Juan Perez', $message->metadata['components'][0]['parameters'][0]['text']);

        // Verify sent status propagates to ScheduledMessage
        $this->assertEquals('sent', $message->status);
        $this->assertEquals('sent', $scheduledMessage->status);
    }
}
