<?php

declare(strict_types=1);

namespace App\Application\WhatsApp\DTOs;

/**
 * SendMessageDTO
 *
 * Data Transfer Object for sending WhatsApp messages.
 * Carries data between presentation and application layers.
 * Contains no business logic - just data.
 */
final readonly class SendMessageDTO
{
    public function __construct(
        public string $phoneNumber,
        public string $content
    ) {
    }

    /**
     * Create from HTTP request data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['phone_number'] ?? '',
            $data['content'] ?? ''
        );
    }

    /**
     * Convert to array (for serialization)
     */
    public function toArray(): array
    {
        return [
            'phone_number' => $this->phoneNumber,
            'content' => $this->content,
        ];
    }
}