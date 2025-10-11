<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Services;

use App\Domain\WhatsApp\Entities\Conversation;
use App\Domain\WhatsApp\ValueObjects\PhoneNumber;

interface ConversationTrackerInterface
{
    public function getOrCreateConversation(PhoneNumber $phoneNumber): Conversation;

    public function setState(PhoneNumber $phoneNumber, string $state, array $context = []): void;

    public function updateContext(PhoneNumber $phoneNumber, array $context): void;

    public function setContextValue(PhoneNumber $phoneNumber, string $key, mixed $value): void;

    public function getContextValue(PhoneNumber $phoneNumber, string $key): mixed;

    public function getCurrentState(PhoneNumber $phoneNumber): string;

    public function reset(PhoneNumber $phoneNumber): void;

    public function isInState(PhoneNumber $phoneNumber, string $state): bool;

    public function hasActiveConversation(PhoneNumber $phoneNumber): bool;
}