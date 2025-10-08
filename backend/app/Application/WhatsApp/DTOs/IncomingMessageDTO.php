<?php

declare(strict_types=1);

namespace App\Application\WhatsApp\DTOs;

final readonly class IncomingMessageDTO
{
    public function __construct(
        public string $fromPhone,
        public string $content,
        public string $twilioSid,
    ) {}

    public static function fromWebhookData(array $data): self
    {
        return new self(
            fromPhone: $data['From'] ?? '',
            content: $data['Body'] ?? '',
            twilioSid: $data['MessageSid'] ?? '',
        );
    }
}