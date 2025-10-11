<?php

declare(strict_types=1);

namespace App\Infrastructure\WhatsApp\Repositories;

use App\Domain\WhatsApp\Entities\Conversation;
use App\Domain\WhatsApp\Repositories\ConversationRepositoryInterface;
use App\Domain\WhatsApp\ValueObjects\MessageId;
use App\Domain\WhatsApp\ValueObjects\PhoneNumber;
use App\Infrastructure\WhatsApp\Models\ConversationModel;

final readonly class EloquentConversationRepository implements ConversationRepositoryInterface
{
    public function save(Conversation $conversation): void
    {
        ConversationModel::updateOrCreate(
            ['id' => $conversation->getId()->getValue()],
            [
                'phone_number' => $conversation->getPhoneNumber()->getValue(),
                'current_state' => $conversation->getCurrentState(),
                'context' => $conversation->getContext(),
                'last_message_at' => $conversation->getLastMessageAt(),
            ]
        );
    }

    public function findById(MessageId $id): ?Conversation
    {
        $model = ConversationModel::find($id->getValue());

        return $model ? $this->toDomainEntity($model) : null;
    }

    public function findByPhoneNumber(PhoneNumber $phoneNumber): ?Conversation
    {
        $model = ConversationModel::where('phone_number', $phoneNumber->getValue())->first();

        return $model ? $this->toDomainEntity($model) : null;
    }

    public function findActiveConversations(): array
    {
        $models = ConversationModel::where('current_state', '!=', 'idle')
            ->orderBy('last_message_at', 'desc')
            ->get();

        return $models->map(fn($model) => $this->toDomainEntity($model))->toArray();
    }

    public function findByState(string $state): array
    {
        $models = ConversationModel::where('current_state', $state)
            ->orderBy('last_message_at', 'desc')
            ->get();

        return $models->map(fn($model) => $this->toDomainEntity($model))->toArray();
    }

    public function delete(MessageId $id): void
    {
        ConversationModel::where('id', $id->getValue())->delete();
    }

    public function deleteOldConversations(\DateTimeImmutable $before): int
    {
        return ConversationModel::where('last_message_at', '<', $before->format('Y-m-d H:i:s'))
            ->where('current_state', 'idle')
            ->delete();
    }

    private function toDomainEntity(ConversationModel $model): Conversation
    {
        $conversation = Conversation::create(
            MessageId::fromString($model->id),
            PhoneNumber::fromString($model->phone_number),
            $model->current_state,
            $model->context ?? []
        );

        return $conversation;
    }
}