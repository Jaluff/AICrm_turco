<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Contact;
use App\Models\ChannelConnection;
use App\Models\Department;
use App\Models\BusinessHour;
use App\Models\WebhookEvent;
use App\Models\Message;
use App\Models\Conversation;
use App\Jobs\ProcessIncomingWebhookJob;
use App\Services\BusinessHoursService;
use App\Support\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BusinessHoursTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Tenant::clear();
    }

    public function test_business_hours_service_open_close_evaluation(): void
    {
        $company = Company::factory()->create([
            'business_hours_enabled' => true,
            'timezone' => 'America/Argentina/Buenos_Aires', // UTC-3
        ]);

        Tenant::set($company);

        // Configurar Lunes como abierto de 09:00 a 18:00
        BusinessHour::create([
            'company_id' => $company->id,
            'department_id' => null,
            'day_of_week' => 1, // Lunes
            'enabled' => true,
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        $service = new BusinessHoursService();

        // 1. Probar un lunes a las 10:00 AM (Abierto)
        // 2026-07-06 es Lunes
        $mondayOpen = Carbon::parse('2026-07-06 10:00:00', 'America/Argentina/Buenos_Aires');
        $this->assertTrue($service->isOpenForCompany($company, $mondayOpen));

        // 2. Probar un lunes a las 08:00 AM (Cerrado)
        $mondayEarly = Carbon::parse('2026-07-06 08:00:00', 'America/Argentina/Buenos_Aires');
        $this->assertFalse($service->isOpenForCompany($company, $mondayEarly));

        // 3. Probar un lunes a las 19:00 PM (Cerrado)
        $mondayLate = Carbon::parse('2026-07-06 19:00:00', 'America/Argentina/Buenos_Aires');
        $this->assertFalse($service->isOpenForCompany($company, $mondayLate));

        // 4. Probar un martes (no configurado / deshabilitado, por ende cerrado)
        $tuesday = Carbon::parse('2026-07-07 10:00:00', 'America/Argentina/Buenos_Aires');
        $this->assertFalse($service->isOpenForCompany($company, $tuesday));
    }

    public function test_incoming_message_outside_hours_sends_away_message(): void
    {
        Http::fake([
            'https://graph.facebook.com/*' => Http::response([
                'messaging_product' => 'whatsapp',
                'messages' => [['id' => 'wamid.test_outgoing_away_message_123']]
            ], 200)
        ]);

        $company = Company::factory()->create([
            'business_hours_enabled' => true,
            'away_message' => 'Lo sentimos, estamos fuera de nuestro horario de atención.',
            'timezone' => 'America/Argentina/Buenos_Aires',
        ]);

        Tenant::set($company);

        // Lunes habilitado de 09:00 a 18:00
        BusinessHour::create([
            'company_id' => $company->id,
            'department_id' => null,
            'day_of_week' => 1,
            'enabled' => true,
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        $contact = Contact::factory()->create(['company_id' => $company->id, 'phone' => '5491122334455']);
        
        \App\Models\ContactIdentity::create([
            'company_id' => $company->id,
            'contact_id' => $contact->id,
            'channel_type' => 'whatsapp_cloud',
            'external_id' => $contact->phone,
        ]);

        $connection = ChannelConnection::factory()->create([
            'company_id' => $company->id,
            'type' => ChannelConnection::TYPE_WHATSAPP_CLOUD,
            'external_phone_number_id' => 'phone_id_123',
        ]);

        // Crear una conversación abierta
        $conversation = Conversation::factory()->create([
            'company_id' => $company->id,
            'contact_id' => $contact->id,
            'channel_connection_id' => $connection->id,
            'status' => 'open',
        ]);

        // Simular un mensaje entrante el Lunes a las 23:00 PM (fuera de horario)
        // timestamp: 2026-07-06 23:00:00 en Buenos Aires (UTC-3) -> UTC timestamp is 2026-07-07 02:00:00
        $timestamp = Carbon::parse('2026-07-06 23:00:00', 'America/Argentina/Buenos_Aires')->timestamp;

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
                                    'phone_number_id' => 'phone_id_123',
                                ],
                                'contacts' => [
                                    [
                                        'profile' => ['name' => 'Diego'],
                                        'wa_id' => '5491122334455',
                                    ],
                                ],
                                'messages' => [
                                    [
                                        'from' => '5491122334455',
                                        'id' => 'wamid.incoming_outside_hours_123',
                                        'timestamp' => (string) $timestamp,
                                        'text' => ['body' => 'Hola, hay alguien?'],
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

        $event = WebhookEvent::create([
            'company_id' => $company->id,
            'channel_type' => 'whatsapp_cloud',
            'payload' => $payload,
            'status' => 'pending',
        ]);

        Tenant::clear();

        // Ejecutar el Job de procesamiento del webhook
        (new ProcessIncomingWebhookJob($event))->handle();

        // 1. Confirmar que el webhook se procesó
        $event->refresh();
        $this->assertEquals('processed', $event->status);

        // 2. Confirmar que la conversación cambió su estado a pending_human
        $conversation->refresh();
        $this->assertEquals('pending_human', $conversation->status);

        // 3. Confirmar que se envió el away_message (mensaje saliente del sistema)
        $messages = Message::where('conversation_id', $conversation->id)->get();
        
        // Debe haber 2 mensajes: 1 inbound de Diego, y 1 outbound del sistema con el away_message
        $this->assertCount(2, $messages);

        $inbound = $messages->firstWhere('direction', 'inbound');
        $this->assertNotNull($inbound);
        $this->assertEquals('Hola, hay alguien?', $inbound->body);

        $outbound = $messages->firstWhere('direction', 'outbound');
        $this->assertNotNull($outbound);
        $this->assertEquals('Lo sentimos, estamos fuera de nuestro horario de atención.', $outbound->body);
        $this->assertEquals('system', $outbound->sender_type);
        $this->assertEquals('sent', $outbound->status);
    }

    public function test_incoming_message_outside_department_hours_sends_department_away_message(): void
    {
        Http::fake([
            'https://graph.facebook.com/*' => Http::response([
                'messaging_product' => 'whatsapp',
                'messages' => [['id' => 'wamid.test_outgoing_away_message_dept']]
            ], 200)
        ]);

        $company = Company::factory()->create([
            'business_hours_enabled' => false, // Deshabilitado a nivel empresa
        ]);

        Tenant::set($company);

        // Crear departamento con horario habilitado y propio
        $department = Department::factory()->create([
            'company_id' => $company->id,
            'business_hours_enabled' => true,
            'use_company_business_hours' => false,
            'away_message' => 'Ausente en Departamento de Soporte.',
        ]);

        // Lunes habilitado de 09:00 a 18:00 para el departamento
        BusinessHour::create([
            'company_id' => $company->id,
            'department_id' => $department->id,
            'day_of_week' => 1,
            'enabled' => true,
            'start_time' => '09:00:00',
            'end_time' => '18:00:00',
        ]);

        $contact = Contact::factory()->create(['company_id' => $company->id, 'phone' => '5491122334455']);
        \App\Models\ContactIdentity::create([
            'company_id' => $company->id,
            'contact_id' => $contact->id,
            'channel_type' => 'whatsapp_cloud',
            'external_id' => $contact->phone,
        ]);

        $connection = ChannelConnection::factory()->create([
            'company_id' => $company->id,
            'type' => ChannelConnection::TYPE_WHATSAPP_CLOUD,
            'external_phone_number_id' => 'phone_id_123',
        ]);

        // Crear una conversación abierta asignada al departamento
        $conversation = Conversation::factory()->create([
            'company_id' => $company->id,
            'contact_id' => $contact->id,
            'channel_connection_id' => $connection->id,
            'department_id' => $department->id,
            'status' => 'open',
        ]);

        $timestamp = Carbon::parse('2026-07-06 23:00:00', 'America/Argentina/Buenos_Aires')->timestamp;

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
                                    'phone_number_id' => 'phone_id_123',
                                ],
                                'contacts' => [
                                    [
                                        'profile' => ['name' => 'Diego'],
                                        'wa_id' => '5491122334455',
                                    ],
                                ],
                                'messages' => [
                                    [
                                        'from' => '5491122334455',
                                        'id' => 'wamid.incoming_outside_hours_dept_123',
                                        'timestamp' => (string) $timestamp,
                                        'text' => ['body' => 'Hola soporte?'],
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

        $event = WebhookEvent::create([
            'company_id' => $company->id,
            'channel_type' => 'whatsapp_cloud',
            'payload' => $payload,
            'status' => 'pending',
        ]);

        Tenant::clear();

        (new ProcessIncomingWebhookJob($event))->handle();

        $event->refresh();
        $this->assertEquals('processed', $event->status);

        $conversation->refresh();
        $this->assertEquals('pending_human', $conversation->status);

        $messages = Message::where('conversation_id', $conversation->id)->get();
        $this->assertCount(2, $messages);

        $outbound = $messages->firstWhere('direction', 'outbound');
        $this->assertNotNull($outbound);
        $this->assertEquals('Ausente en Departamento de Soporte.', $outbound->body);
        $this->assertEquals('system', $outbound->sender_type);
        $this->assertEquals('sent', $outbound->status);
    }
}
