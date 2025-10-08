<?php

declare(strict_types=1);

namespace App\Application\WhatsApp\Results;

use App\Domain\WhatsApp\Entities\IncomingMessage;

final readonly class HandleIncomingMessageResult
{
    private function __construct(
        private bool $success,
        private ?IncomingMessage $message = null,
        private ?string $errorMessage = null,
    ) {}

    public static function success(IncomingMessage $message): self
    {
        return new self(true, $message);
    }

    public static function failure(string $errorMessage): self
    {
        return new self(false, null, $errorMessage);
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    public function getMessage(): ?IncomingMessage
    {
        return $this->message;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }
}