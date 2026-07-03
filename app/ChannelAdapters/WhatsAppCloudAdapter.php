<?php

namespace App\ChannelAdapters;

use App\Models\ChannelConnection;
use App\DTOs\SendResult;
use App\DTOs\IncomingMessageData;
use Illuminate\Support\Facades\Http;
use Exception;

class WhatsAppCloudAdapter implements ChannelAdapter
{
    public function sendTextMessage(ChannelConnection $connection, string $to, string $text): SendResult
    {
        $phoneNumberId = $connection->external_phone_number_id;
        $accessToken = $connection->access_token;

        if (!$phoneNumberId || !$accessToken) {
            return SendResult::failure('Missing WhatsApp credentials (external_phone_number_id or access_token).');
        }

        try {
            $response = Http::withToken($accessToken)
                ->post("https://graph.facebook.com/v20.0/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $to,
                    'type' => 'text',
                    'text' => [
                        'preview_url' => false,
                        'body' => $text,
                    ],
                ]);

            $json = $response->json();

            if ($response->successful() && isset($json['messages'][0]['id'])) {
                return SendResult::success($json['messages'][0]['id'], $json);
            }

            $errorMsg = $json['error']['message'] ?? 'Unknown API Error';
            return SendResult::failure($errorMsg, $json);
        } catch (Exception $e) {
            return SendResult::failure($e->getMessage());
        }
    }

    public function sendTemplateMessage(
        ChannelConnection $connection,
        string $to,
        string $templateName,
        string $languageCode,
        array $components = []
    ): SendResult {
        $phoneNumberId = $connection->external_phone_number_id;
        $accessToken = $connection->access_token;

        if (!$phoneNumberId || !$accessToken) {
            return SendResult::failure('Missing WhatsApp credentials (external_phone_number_id or access_token).');
        }

        try {
            $response = Http::withToken($accessToken)
                ->post("https://graph.facebook.com/v20.0/{$phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'recipient_type' => 'individual',
                    'to' => $to,
                    'type' => 'template',
                    'template' => [
                        'name' => $templateName,
                        'language' => [
                            'code' => $languageCode,
                        ],
                        'components' => $components,
                    ],
                ]);

            $json = $response->json();

            if ($response->successful() && isset($json['messages'][0]['id'])) {
                return SendResult::success($json['messages'][0]['id'], $json);
            }

            $errorMsg = $json['error']['message'] ?? 'Unknown API Error';
            return SendResult::failure($errorMsg, $json);
        } catch (Exception $e) {
            return SendResult::failure($e->getMessage());
        }
    }

    public function normalizeIncomingWebhook(array $payload): array
    {
        $normalizedMessages = [];

        if (empty($payload['entry'])) {
            return $normalizedMessages;
        }

        foreach ($payload['entry'] as $entry) {
            if (empty($entry['changes'])) {
                continue;
            }

            foreach ($entry['changes'] as $change) {
                $value = $change['value'] ?? [];
                if (empty($value['messages'])) {
                    continue;
                }

                // Map wa_id to sender name
                $contactsMap = [];
                if (!empty($value['contacts'])) {
                    foreach ($value['contacts'] as $contact) {
                        $contactsMap[$contact['wa_id']] = $contact['profile']['name'] ?? 'WhatsApp Contact';
                    }
                }

                foreach ($value['messages'] as $message) {
                    // Only handle text messages for now
                    if (($message['type'] ?? '') !== 'text') {
                        continue;
                    }

                    $senderPhone = $message['from'] ?? '';
                    $senderName = $contactsMap[$senderPhone] ?? $senderPhone;
                    $messageText = $message['text']['body'] ?? '';
                    $externalMessageId = $message['id'] ?? '';
                    $timestamp = (int) ($message['timestamp'] ?? time());

                    $normalizedMessages[] = new IncomingMessageData(
                        senderPhone: $senderPhone,
                        senderName: $senderName,
                        messageText: $messageText,
                        externalMessageId: $externalMessageId,
                        timestamp: $timestamp,
                        rawPayload: $message,
                        metadata: [
                            'display_phone_number' => $value['metadata']['display_phone_number'] ?? null,
                            'phone_number_id' => $value['metadata']['phone_number_id'] ?? null,
                            'waba_id' => $entry['id'] ?? null,
                        ]
                    );
                }
            }
        }

        return $normalizedMessages;
    }
}
