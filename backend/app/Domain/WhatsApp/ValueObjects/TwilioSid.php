<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\ValueObjects;

final readonly class TwilioSid
{
    private function __construct(
        private string $value
    ) {
        $this->validate($value);
    }

    public static function fromString(string $sid): self
    {
        return new self($sid);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(TwilioSid $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private function validate(string $sid): void
    {
        if (empty(trim($sid))) {
            throw new \InvalidArgumentException('Twilio SID cannot be empty');
        }

        if (!preg_match('/^SM[a-f0-9]{32}$/i', $sid)) {
            throw new \InvalidArgumentException('Invalid Twilio SID format');
        }
    }
}