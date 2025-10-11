<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Entities;

use App\Domain\WhatsApp\ValueObjects\MessageId;
use App\Domain\WhatsApp\ValueObjects\PhoneNumber;
use DateTimeImmutable;

final class Conversation
{
    private function __construct(
        private readonly MessageId $id,
        private readonly PhoneNumber $phoneNumber,
        private string $currentState,
        private array $context,
        private DateTimeImmutable $lastMessageAt,
        private readonly DateTimeImmutable $createdAt = new DateTimeImmutable(),
    ) {
        $this->validateState($currentState);
    }

    public static function create(
        MessageId $id,
        PhoneNumber $phoneNumber,
        string $state = 'idle',
        array $context = []
    ): self {
        return new self(
            $id,
            $phoneNumber,
            $state,
            $context,
            new DateTimeImmutable()
        );
    }

    public function getId(): MessageId
    {
        return $this->id;
    }

    public function getPhoneNumber(): PhoneNumber
    {
        return $this->phoneNumber;
    }

    public function getCurrentState(): string
    {
        return $this->currentState;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getLastMessageAt(): DateTimeImmutable
    {
        return $this->lastMessageAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setState(string $state): void
    {
        $this->validateState($state);
        $this->currentState = $state;
        $this->lastMessageAt = new DateTimeImmutable();
    }

    public function updateContext(array $newContext): void
    {
        $this->context = array_merge($this->context, $newContext);
        $this->lastMessageAt = new DateTimeImmutable();
    }

    public function getContextValue(string $key): mixed
    {
        return $this->context[$key] ?? null;
    }

    public function setContextValue(string $key, mixed $value): void
    {
        $this->context[$key] = $value;
        $this->lastMessageAt = new DateTimeImmutable();
    }

    public function reset(): void
    {
        $this->currentState = 'idle';
        $this->context = [];
        $this->lastMessageAt = new DateTimeImmutable();
    }

    public function isIdle(): bool
    {
        return $this->currentState === 'idle';
    }

    public function isActive(): bool
    {
        return !$this->isIdle();
    }

    private function validateState(string $state): void
    {
        $validStates = [
            'idle',
            'waiting_for_name',
            'waiting_for_date',
            'waiting_for_time',
            'waiting_for_service',
            'processing_order',
            'confirmed',
            'cancelled'
        ];

        if (!in_array($state, $validStates)) {
            throw new \InvalidArgumentException('Invalid conversation state: ' . $state);
        }
    }
}