<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Contact;
use App\Models\ContactIdentity;
use App\Models\ChannelConnection;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\WebhookEvent;
use App\Jobs\ProcessIncomingWebhookJob;
use App\Actions\SendMessageAction;
use App\Jobs\SendOutgoingMessageJob;
use App\Support\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ConversationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Tenant::clear();
    }

    public function test_inbound_flow_creates_contact_identity_conversation_and_message(): void
    {
        $company = Company::factory()->create();
        
        // Registrar la conexión del canal de WhatsApp
        $connection = ChannelConnection::factory()->create([
            'company_id' => $company->id,
            'external_phone_number_id' => 'phone-12345',
        ]);

        $payload = [
            'object' => 'whatsapp_business_account',
            'entry' => [
                [
                    'id' => 'waba-id-123',
                    'changes' => [
                        [
                            'value' => [
                                'messaging_product' => 'whatsapp',
                                'metadata' => [
                                    'display_phone_number' => '15550199999',
                                    'phone_number_id' => 'phone-12345',
                                ],
                                'contacts' => [
                                    [
                                        'profile' => [
                                            'name' => 'Juan Perez',
                                        ],
                                        'wa_id' => '5491199999999',
                                    ],
                                ],
                                'messages' => [
                                    [
                                        'from' => '5491199999999',
                                        'id' => 'wamid.inbound_msg_01',
                                        'timestamp' => '1672531199',
                                        'text' => [
                                            'body' => 'Hola, necesito ayuda comercial.',
                                        ],
                                        'type' => 'text',
                                    ],
                                ],
                            ],
                            'field' => 'messages',
                        ],
                    ],
                ],
            ],
        ];

        // Crear el evento de webhook
        $event = WebhookEvent::create([
            'company_id' => $company->id,
            'channel_type' => 'whatsapp_cloud',
            'payload' => $payload,
            'status' => 'pending',
        ]);

        // Ejecutar el procesamiento síncronamente
        $job = new ProcessIncomingWebhookJob($event);
        $job->handle();

        // Establecer el Tenant para las aserciones del test
        Tenant::set($company);

        // Aserciones
        $this->assertEquals('processed', $event->fresh()->status);
        $this->assertDatabaseHas('contacts', [
            'company_id' => $company->id,
            'phone' => '5491199999999',
            'name' => 'Juan Perez',
        ]);

        $contact = Contact::where('phone', '5491199999999')->first();

        $this->assertDatabaseHas('contact_identities', [
            'company_id' => $company->id,
            'contact_id' => $contact->id,
            'channel_type' => 'whatsapp_cloud',
            'external_id' => '5491199999999',
        ]);

        $this->assertDatabaseHas('conversations', [
            'company_id' => $company->id,
            'contact_id' => $contact->id,
            'channel_connection_id' => $connection->id,
            'status' => 'open',
        ]);

        $conversation = Conversation::where('contact_id', $contact->id)->first();

        $this->assertDatabaseHas('messages', [
            'company_id' => $company->id,
            'conversation_id' => $conversation->id,
            'direction' => 'inbound',
            'body' => 'Hola, necesito ayuda comercial.',
            'external_message_id' => 'wamid.inbound_msg_01',
        ]);
        
        // Validar reutilización de conversación
        $secondPayload = $payload;
        $secondPayload['entry'][0]['changes'][0]['value']['messages'][0]['id'] = 'wamid.inbound_msg_02';
        $secondPayload['entry'][0]['changes'][0]['value']['messages'][0]['text']['body'] = '¿Siguen ahí?';

        Tenant::clear(); // Limpiar tenant para simular flujo de webhook anónimo
        $secondEvent = WebhookEvent::create([
            'company_id' => $company->id,
            'channel_type' => 'whatsapp_cloud',
            'payload' => $secondPayload,
            'status' => 'pending',
        ]);

        $secondJob = new ProcessIncomingWebhookJob($secondEvent);
        $secondJob->handle();

        Tenant::set($company);
        
        $this->assertCount(1, Contact::all());
        $this->assertCount(1, Conversation::all());
        $this->assertCount(2, Message::all());
    }

    public function test_outbound_flow_creates_message_and_sends_via_adapter(): void
    {
        Queue::fake();

        $company = Company::factory()->create();
        Tenant::set($company);

        $connection = ChannelConnection::factory()->create([
            'company_id' => $company->id,
            'external_phone_number_id' => 'phone_id_abc',
            'access_token' => 'token_secret_123',
        ]);

        $contact = Contact::factory()->create([
            'company_id' => $company->id,
            'phone' => '5491188888888',
        ]);

        $conversation = Conversation::factory()->create([
            'company_id' => $company->id,
            'contact_id' => $contact->id,
            'channel_connection_id' => $connection->id,
        ]);

        // Registrar envío
        $action = new SendMessageAction();
        $message = $action->execute(
            conversation: $conversation,
            body: '¡Hola! Te escribo de soporte.',
            senderType: 'human',
            senderUserId: null
        );

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'direction' => 'outbound',
            'status' => 'pending',
            'body' => '¡Hola! Te escribo de soporte.',
        ]);

        Queue::assertPushed(SendOutgoingMessageJob::class);

        // Mockear HTTP para Meta Graph API
        Http::fake([
            'https://graph.facebook.com/v20.0/phone_id_abc/messages' => Http::response([
                'messaging_product' => 'whatsapp',
                'contacts' => [
                    ['input' => '5491188888888', 'wa_id' => '5491188888888']
                ],
                'messages' => [
                    ['id' => 'wamid.outbound_msg_999']
                ]
            ], 200)
        ]);

        // Limpiar Tenant para simular entorno del worker de cola
        Tenant::clear();

        // Ejecutar el job de envío
        $job = new SendOutgoingMessageJob($message);
        $job->handle();

        // Validar cambio de estado
        Tenant::set($company);
        $this->assertEquals('sent', $message->fresh()->status);
        $this->assertEquals('wamid.outbound_msg_999', $message->fresh()->external_message_id);
        $this->assertNotNull($message->fresh()->sent_at);
    }
}
