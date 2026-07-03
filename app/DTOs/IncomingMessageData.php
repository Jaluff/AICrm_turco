<?php

namespace App\DTOs;

class IncomingMessageData
{
    public function __construct(
        public string $senderPhone,
        public string $senderName,
        public string $messageText,
        public string $externalMessageId,
        public int $timestamp,
        public array $rawPayload,
        public array $metadata = []
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            senderPhone: $data['senderPhone'],
            senderName: $data['senderName'],
            messageText: $data['messageText'],
            externalMessageId: $data['externalMessageId'],
            timestamp: $data['timestamp'],
            rawPayload: $data['rawPayload'],
            metadata: $data['metadata'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'senderPhone' => $this->senderPhone,
            'senderName' => $this->senderName,
            'messageText' => $this->messageText,
            'externalMessageId' => $this->externalMessageId,
            'timestamp' => $this->timestamp,
            'rawPayload' => $this->rawPayload,
            'metadata' => $this->metadata,
        ];
    }
}
