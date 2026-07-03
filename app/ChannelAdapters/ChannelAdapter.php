<?php

namespace App\ChannelAdapters;

use App\Models\ChannelConnection;
use App\DTOs\SendResult;

interface ChannelAdapter
{
    public function sendTextMessage(ChannelConnection $connection, string $to, string $text): SendResult;

    public function sendTemplateMessage(
        ChannelConnection $connection,
        string $to,
        string $templateName,
        string $languageCode,
        array $components = []
    ): SendResult;

    /**
     * Normaliza un payload crudo recibido por webhook.
     * 
     * @param array $payload
     * @return array<\App\DTOs\IncomingMessageData>
     */
    public function normalizeIncomingWebhook(array $payload): array;
}
