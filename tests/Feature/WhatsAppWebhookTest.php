<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\ChannelConnection;
use App\Models\WebhookEvent;
use App\Jobs\ProcessIncomingWebhookJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

use App\Support\Tenant;

class WhatsAppWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Tenant::clear();
    }

    public function test_verify_webhook_with_global_token(): void
    {
        // Set configuration temporarily
        config(['services.whatsapp.verify_token' => 'global_secret_123']);

        $response = $this->getJson('/api/v1/webhooks/whatsapp?hub_mode=subscribe&hub_verify_token=global_secret_123&hub_challenge=my_challenge');

        $response->assertStatus(200);
        $response->assertSee('my_challenge');
    }

    public function test_verify_webhook_with_connection_token(): void
    {
        $company = Company::factory()->create();
        ChannelConnection::factory()->create([
            'company_id' => $company->id,
            'verify_token' => 'connection_secret_456',
        ]);

        $response = $this->getJson('/api/v1/webhooks/whatsapp?hub_mode=subscribe&hub_verify_token=connection_secret_456&hub_challenge=my_challenge_2');

        $response->assertStatus(200);
        $response->assertSee('my_challenge_2');
    }

    public function test_verify_webhook_fails_with_invalid_token(): void
    {
        $response = $this->getJson('/api/v1/webhooks/whatsapp?hub_mode=subscribe&hub_verify_token=wrong_token&hub_challenge=my_challenge');

        $response->assertStatus(403);
    }

    public function test_receive_webhook_dispatches_job_and_saves_event_with_resolved_company(): void
    {
        Queue::fake();

        $company = Company::factory()->create();
        $connection = ChannelConnection::factory()->create([
            'company_id' => $company->id,
            'external_phone_number_id' => 'phone-id-12345',
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
                                    'phone_number_id' => 'phone-id-12345',
                                ],
                                'messages' => [
                                    [
                                        'from' => '5491122334455',
                                        'id' => 'wamid.HBgLNTQ5MTEyMjMzNDQ1NVEtM0E1QjE1RDkzNDg2QTQ=',
                                        'timestamp' => '1672531199',
                                        'text' => [
                                            'body' => 'Hola mundo',
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

        $response = $this->postJson('/api/v1/webhooks/whatsapp', $payload);

        $response->assertStatus(200);
        $response->assertSee('EVENT_RECEIVED');

        $this->assertDatabaseHas('webhook_events', [
            'company_id' => $company->id,
            'channel_type' => 'whatsapp_cloud',
            'status' => 'pending',
        ]);

        Queue::assertPushed(ProcessIncomingWebhookJob::class, function ($job) {
            return $job->event->company_id !== null;
        });
    }

    public function test_receive_webhook_saves_event_with_null_company_when_unknown(): void
    {
        Queue::fake();

        $payload = [
            'object' => 'whatsapp_business_account',
            'entry' => [
                [
                    'id' => 'waba-id-999',
                    'changes' => [
                        [
                            'value' => [
                                'messaging_product' => 'whatsapp',
                                'metadata' => [
                                    'display_phone_number' => '15550199999',
                                    'phone_number_id' => 'unknown-phone-id',
                                ],
                                'messages' => [
                                    [
                                        'from' => '5491122334455',
                                        'id' => 'wamid.HBgLNTQ5MTEyMjMzNDQ1NVEtM0E1QjE1RDkzNDg2QTQ=',
                                        'timestamp' => '1672531199',
                                        'text' => [
                                            'body' => 'Hola',
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

        $response = $this->postJson('/api/v1/webhooks/whatsapp', $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('webhook_events', [
            'company_id' => null,
            'channel_type' => 'whatsapp_cloud',
            'status' => 'pending',
        ]);

        Queue::assertPushed(ProcessIncomingWebhookJob::class);
    }
}
