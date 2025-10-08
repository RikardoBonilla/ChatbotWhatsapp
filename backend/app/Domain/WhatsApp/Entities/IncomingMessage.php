<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Entities;

use App\Domain\WhatsApp\ValueObjects\MessageId;
use App\Domain\WhatsApp\ValueObjects\PhoneNumber;
use App\Domain\WhatsApp\ValueObjects\TwilioSid;
use DateTimeImmutable;

final class IncomingMessage
{
    private function __construct(
        private readonly MessageId $id,
        private readonly PhoneNumber $fromPhone,
        private readonly string $content,
        private readonly TwilioSid $twilioSid,
        private bool $processed = false,
        private ?MessageId $responseMessageId = null,
        private readonly DateTimeImmutable $createdAt = new DateTimeImmutable(),
    ) {
        $this->validateContent($content);
    }

    public static function create(
        MessageId $id,
        PhoneNumber $fromPhone,
        string $content,
        TwilioSid $twilioSid
    ): self {
        return new self($id, $fromPhone, $content, $twilioSid);
    }

    public function getId(): MessageId
    {
        return $this->id;
    }

    public function getFromPhone(): PhoneNumber
    {
        return $this->fromPhone;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getTwilioSid(): TwilioSid
    {
        return $this->twilioSid;
    }

    public function isProcessed(): bool
    {
        return $this->processed;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function markAsProcessed(): void
    {
        $this->processed = true;
    }

    public function getResponseMessageId(): ?MessageId
    {
        return $this->responseMessageId;
    }

    public function setResponseMessageId(MessageId $responseMessageId): void
    {
        $this->responseMessageId = $responseMessageId;
    }

    public function getKeywords(): array
    {
        $content = strtolower(trim($this->content));
        $words = explode(' ', $content);

        return array_filter($words, function (string $word): bool {
            return strlen($word) > 2;
        });
    }

    private function validateContent(string $content): void
    {
        if (empty(trim($content))) {
            throw new \InvalidArgumentException('Message content cannot be empty');
        }

        if (strlen($content) > 1600) {
            throw new \InvalidArgumentException('Message content cannot exceed 1600 characters');
        }
    }
}