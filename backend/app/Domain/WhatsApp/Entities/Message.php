<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Entities;

use App\Domain\WhatsApp\ValueObjects\PhoneNumber;
use DateTimeImmutable;
use DomainException;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Message Entity
 *
 * Represents a WhatsApp message with business rules and behavior.
 * Contains identity, state management, and business validation.
 */
final class Message
{
    private UuidInterface $id;
    private PhoneNumber $to;
    private string $content;
    private string $status;
    private ?string $twilioSid;
    private DateTimeImmutable $createdAt;
    private ?DateTimeImmutable $sentAt;

    public function __construct(
        PhoneNumber $to,
        string $content,
        ?UuidInterface $id = null
    ) {
        $this->id = $id ?? Uuid::uuid4();
        $this->to = $to;
        $this->content = $this->validateContent($content);
        $this->status = 'pending';
        $this->twilioSid = null;
        $this->createdAt = new DateTimeImmutable();
        $this->sentAt = null;
    }

    /**
     * Validate message content according to business rules
     */
    private function validateContent(string $content): string
    {
        $trimmed = trim($content);

        if (empty($trimmed)) {
            throw new InvalidArgumentException('Message content cannot be empty');
        }

        if (strlen($trimmed) > 1600) {
            throw new InvalidArgumentException('Message content exceeds 1600 characters');
        }

        return $trimmed;
    }

    /**
     * Mark message as successfully sent
     */
    public function markAsSent(string $twilioSid): void
    {
        if ($this->status === 'sent') {
            throw new DomainException('Message already marked as sent');
        }

        $this->status = 'sent';
        $this->twilioSid = $twilioSid;
        $this->sentAt = new DateTimeImmutable();
    }

    /**
     * Mark message as failed
     */
    public function markAsFailed(): void
    {
        if ($this->status === 'sent') {
            throw new DomainException('Cannot mark sent message as failed');
        }

        $this->status = 'failed';
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function getTo(): PhoneNumber
    {
        return $this->to;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getTwilioSid(): ?string
    {
        return $this->twilioSid;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getSentAt(): ?DateTimeImmutable
    {
        return $this->sentAt;
    }

    /**
     * Check if message was successfully sent
     */
    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    /**
     * Check if message failed to send
     */
    public function hasFailed(): bool
    {
        return $this->status === 'failed';
    }
}