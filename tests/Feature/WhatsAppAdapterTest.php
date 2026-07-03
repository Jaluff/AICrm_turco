<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\ChannelConnection;
use App\ChannelAdapters\WhatsAppCloudAdapter;
use App\DTOs\IncomingMessageData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsAppAdapterTest extends TestCase
{
    use RefreshDatabase;

    private WhatsAppCloudAdapter $adapter;
    private ChannelConnection $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adapter = new WhatsAppCloudAdapter();
        
        $company = Company::factory()->create();
        $this->connection = ChannelConnection::factory()->create([
            'company_id' => $company->id,
            'external_phone_number_id' => 'phone_id_123',
            'access_token' => 'my_secret_token',
        ]);
    }

    public function test_send_text_message_success(): void
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/phone_id_123/messages' => Http::response([
                'messaging_product' => 'whatsapp',
                'contacts' => [
                    ['input' => '5491122334455', 'wa_id' => '5491122334455']
                ],
                'messages' => [
                    ['id' => 'wamid.HBgLNTQ5MTEy']
                ]
            ], 200)
        ]);

        $result = $this->adapter->sendTextMessage($this->connection, '5491122334455', 'Hola!');

        $this->assertTrue($result->success);
        $this->assertEquals('wamid.HBgLNTQ5MTEy', $result->externalMessageId);
        $this->assertNull($result->errorMessage);
    }

    public function test_send_text_message_failure(): void
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/phone_id_123/messages' => Http::response([
                'error' => [
                    'message' => 'Invalid OAuth access token.',
                    'type' => 'OAuthException',
                    'code' => 190,
                ]
            ], 401)
        ]);

        $result = $this->adapter->sendTextMessage($this->connection, '5491122334455', 'Hola!');

        $this->assertFalse($result->success);
        $this->assertEquals('Invalid OAuth access token.', $result->errorMessage);
        $this->assertNull($result->externalMessageId);
    }

    public function test_send_template_message_success(): void
    {
        Http::fake([
            'https://graph.facebook.com/v20.0/phone_id_123/messages' => Http::response([
                'messaging_product' => 'whatsapp',
                'contacts' => [
                    ['input' => '5491122334455', 'wa_id' => '5491122334455']
                ],
                'messages' => [
                    ['id' => 'wamid.HBgLNTQ5MTEy']
                ]
            ], 200)
        ]);

        $result = $this->adapter->sendTemplateMessage(
            $this->connection,
            '5491122334455',
            'hello_world',
            'en_US'
        );

        $this->assertTrue($result->success);
        $this->assertEquals('wamid.HBgLNTQ5MTEy', $result->externalMessageId);
    }

    public function test_normalize_incoming_webhook(): void
    {
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
                                        'profile' => [
                                            'name' => 'Diego Maradona',
                                        ],
                                        'wa_id' => '5491122334455',
                                    ],
                                ],
                                'messages' => [
                                    [
                                        'from' => '5491122334455',
                                        'id' => 'wamid.1234567890',
                                        'timestamp' => '1672531199',
                                        'text' => [
                                            'body' => 'Gooool!',
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

        $normalized = $this->adapter->normalizeIncomingWebhook($payload);

        $this->assertCount(1, $normalized);
        
        $msgData = $normalized[0];
        $this->assertInstanceOf(IncomingMessageData::class, $msgData);
        $this->assertEquals('5491122334455', $msgData->senderPhone);
        $this->assertEquals('Diego Maradona', $msgData->senderName);
        $this->assertEquals('Gooool!', $msgData->messageText);
        $this->assertEquals('wamid.1234567890', $msgData->externalMessageId);
        $this->assertEquals(1672531199, $msgData->timestamp);
        $this->assertEquals('phone_id_123', $msgData->metadata['phone_number_id']);
    }
}
