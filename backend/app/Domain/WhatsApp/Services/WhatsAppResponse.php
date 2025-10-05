<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Services;

/**
 * WhatsAppResponse
 *
 * Represents the response from a WhatsApp service operation.
 * Contains success status and external service identifier.
 */
final readonly class WhatsAppResponse
{
    public function __construct(
        private bool $success,
        private ?string $externalId = null,
        private ?string $errorMessage = null
    ) {
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getExternalId(): ?string
    {
        return $this->externalId;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * Create successful response
     */
    public static function success(string $externalId): self
    {
        return new self(true, $externalId);
    }

    /**
     * Create failure response
     */
    public static function failure(string $errorMessage): self
    {
        return new self(false, null, $errorMessage);
    }
}