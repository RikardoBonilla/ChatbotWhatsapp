<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\ValueObjects;

use Ramsey\Uuid\Uuid;

final readonly class MessageId
{
    private function __construct(
        private string $value
    ) {
        $this->validate($value);
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function equals(MessageId $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private function validate(string $id): void
    {
        if (empty(trim($id))) {
            throw new \InvalidArgumentException('Message ID cannot be empty');
        }

        if (!Uuid::isValid($id)) {
            throw new \InvalidArgumentException('Invalid UUID format for Message ID');
        }
    }
}