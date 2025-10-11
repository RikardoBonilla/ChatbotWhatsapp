<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Repositories;

use App\Domain\WhatsApp\Entities\Conversation;
use App\Domain\WhatsApp\ValueObjects\MessageId;
use App\Domain\WhatsApp\ValueObjects\PhoneNumber;

interface ConversationRepositoryInterface
{
    public function save(Conversation $conversation): void;

    public function findById(MessageId $id): ?Conversation;

    public function findByPhoneNumber(PhoneNumber $phoneNumber): ?Conversation;

    public function findActiveConversations(): array;

    public function findByState(string $state): array;

    public function delete(MessageId $id): void;

    public function deleteOldConversations(\DateTimeImmutable $before): int;
}