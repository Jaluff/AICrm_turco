<?php

namespace App\DTOs;

class SendResult
{
    public function __construct(
        public bool $success,
        public ?string $externalMessageId = null,
        public ?string $errorMessage = null,
        public ?array $rawResponse = null
    ) {}

    public static function success(string $externalMessageId, ?array $rawResponse = null): self
    {
        return new self(
            success: true,
            externalMessageId: $externalMessageId,
            rawResponse: $rawResponse
        );
    }

    public static function failure(string $errorMessage, ?array $rawResponse = null): self
    {
        return new self(
            success: false,
            errorMessage: $errorMessage,
            rawResponse: $rawResponse
        );
    }
}
