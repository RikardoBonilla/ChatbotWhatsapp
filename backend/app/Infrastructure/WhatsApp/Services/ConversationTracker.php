<?php

declare(strict_types=1);

namespace App\Infrastructure\WhatsApp\Services;

use App\Domain\WhatsApp\Entities\Conversation;
use App\Domain\WhatsApp\Repositories\ConversationRepositoryInterface;
use App\Domain\WhatsApp\Services\ConversationTrackerInterface;
use App\Domain\WhatsApp\ValueObjects\MessageId;
use App\Domain\WhatsApp\ValueObjects\PhoneNumber;

final readonly class ConversationTracker implements ConversationTrackerInterface
{
    public function __construct(
        private ConversationRepositoryInterface $conversationRepository
    ) {}

    public function getOrCreateConversation(PhoneNumber $phoneNumber): Conversation
    {
        $conversation = $this->conversationRepository->findByPhoneNumber($phoneNumber);

        if ($conversation === null) {
            $conversation = Conversation::create(
                MessageId::generate(),
                $phoneNumber
            );
            $this->conversationRepository->save($conversation);
        }

        return $conversation;
    }

    public function setState(PhoneNumber $phoneNumber, string $state, array $context = []): void
    {
        $conversation = $this->getOrCreateConversation($phoneNumber);
        $conversation->setState($state);

        if (!empty($context)) {
            $conversation->updateContext($context);
        }

        $this->conversationRepository->save($conversation);
    }

    public function updateContext(PhoneNumber $phoneNumber, array $context): void
    {
        $conversation = $this->getOrCreateConversation($phoneNumber);
        $conversation->updateContext($context);
        $this->conversationRepository->save($conversation);
    }

    public function setContextValue(PhoneNumber $phoneNumber, string $key, mixed $value): void
    {
        $conversation = $this->getOrCreateConversation($phoneNumber);
        $conversation->setContextValue($key, $value);
        $this->conversationRepository->save($conversation);
    }

    public function getContextValue(PhoneNumber $phoneNumber, string $key): mixed
    {
        $conversation = $this->conversationRepository->findByPhoneNumber($phoneNumber);

        return $conversation?->getContextValue($key);
    }

    public function getCurrentState(PhoneNumber $phoneNumber): string
    {
        $conversation = $this->conversationRepository->findByPhoneNumber($phoneNumber);

        return $conversation?->getCurrentState() ?? 'idle';
    }

    public function reset(PhoneNumber $phoneNumber): void
    {
        $conversation = $this->conversationRepository->findByPhoneNumber($phoneNumber);

        if ($conversation !== null) {
            $conversation->reset();
            $this->conversationRepository->save($conversation);
        }
    }

    public function isInState(PhoneNumber $phoneNumber, string $state): bool
    {
        return $this->getCurrentState($phoneNumber) === $state;
    }

    public function hasActiveConversation(PhoneNumber $phoneNumber): bool
    {
        $currentState = $this->getCurrentState($phoneNumber);

        return $currentState !== 'idle';
    }
}