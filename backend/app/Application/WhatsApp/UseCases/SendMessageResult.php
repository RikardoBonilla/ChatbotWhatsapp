<?php

declare(strict_types=1);

namespace App\Application\WhatsApp\UseCases;

/**
 * SendMessageResult
 *
 * Result object for send message operation.
 * Contains success status and relevant data.
 */
final readonly class SendMessageResult
{
    public function __construct(
        private bool $success,
        private ?string $messageId = null,
        private ?string $errorMessage = null
    ) {
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessageId(): ?string
    {
        return $this->messageId;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * Create successful result
     */
    public static function success(string $messageId): self
    {
        return new self(true, $messageId);
    }

    /**
     * Create failure result
     */
    public static function failure(string $errorMessage): self
    {
        return new self(false, null, $errorMessage);
    }
}