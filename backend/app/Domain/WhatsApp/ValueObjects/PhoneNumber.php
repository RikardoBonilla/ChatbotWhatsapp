<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\ValueObjects;

use InvalidArgumentException;

/**
 * PhoneNumber Value Object
 *
 * Represents a validated phone number for WhatsApp messaging.
 * Ensures type safety and proper formatting for Twilio API.
 */
final readonly class PhoneNumber
{
    private string $value;

    public function __construct(string $phoneNumber)
    {
        $this->value = $this->validate($phoneNumber);
    }

    /**
     * Validate and normalize phone number
     * Accepts formats: +57123456789, 573001234567, 3001234567
     * Always returns format: +573001234567
     */
    private function validate(string $phoneNumber): string
    {
        $cleaned = preg_replace('/[^\d+]/', '', $phoneNumber);

        if (empty($cleaned)) {
            throw new InvalidArgumentException('Phone number cannot be empty');
        }

        if (str_starts_with($cleaned, '+57')) {
            $normalized = $cleaned;
        } elseif (str_starts_with($cleaned, '57') && strlen($cleaned) === 12) {
            $normalized = '+' . $cleaned;
        } elseif (str_starts_with($cleaned, '3') && strlen($cleaned) === 10) {
            $normalized = '+57' . $cleaned;
        } else {
            throw new InvalidArgumentException(
                'Invalid phone number format. Expected Colombian mobile number.'
            );
        }

        if (strlen($normalized) !== 13) {
            throw new InvalidArgumentException('Invalid phone number length');
        }

        if (!preg_match('/^\+57(3[0-9]{2}|31[0-9])\d{7}$/', $normalized)) {
            throw new InvalidArgumentException(
                'Invalid Colombian mobile number. Must start with 30x, 31x, 32x, etc.'
            );
        }

        return $normalized;
    }

    /**
     * Get the phone number in international format
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Get the phone number in WhatsApp format for Twilio
     */
    public function getWhatsAppFormat(): string
    {
        return 'whatsapp:' . $this->value;
    }

    /**
     * Get the phone number for display purposes
     */
    public function getDisplayFormat(): string
    {
        return '+57 ' . substr($this->value, 3, 3) . ' ' .
               substr($this->value, 6, 3) . ' ' .
               substr($this->value, 9, 4);
    }

    /**
     * Check equality with another PhoneNumber
     */
    public function equals(PhoneNumber $other): bool
    {
        return $this->value === $other->value;
    }

    /**
     * String representation
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Create from string (factory method)
     */
    public static function fromString(string $phoneNumber): self
    {
        return new self($phoneNumber);
    }
}