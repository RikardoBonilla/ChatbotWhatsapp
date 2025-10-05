<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Repositories;

use App\Domain\WhatsApp\Entities\Message;
use App\Domain\WhatsApp\ValueObjects\PhoneNumber;
use Ramsey\Uuid\UuidInterface;

/**
 * MessageRepositoryInterface
 *
 * Defines the contract for message persistence operations.
 * Domain layer defines WHAT operations we need.
 * Infrastructure layer implements HOW to do them.
 */
interface MessageRepositoryInterface
{
    /**
     * Save a message to storage
     */
    public function save(Message $message): void;

    /**
     * Find a message by its unique identifier
     */
    public function findById(UuidInterface $id): ?Message;

    /**
     * Find messages sent to a specific phone number
     */
    public function findByPhoneNumber(PhoneNumber $phoneNumber): array;

    /**
     * Find messages by status
     */
    public function findByStatus(string $status): array;

    /**
     * Find recent messages (last N messages)
     */
    public function findRecent(int $limit = 10): array;

    /**
     * Count total messages
     */
    public function count(): int;

    /**
     * Check if a message exists
     */
    public function exists(UuidInterface $id): bool;
}