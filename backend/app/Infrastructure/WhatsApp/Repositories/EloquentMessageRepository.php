<?php

declare(strict_types=1);

namespace App\Infrastructure\WhatsApp\Repositories;

use App\Domain\WhatsApp\Entities\Message;
use App\Domain\WhatsApp\Repositories\MessageRepositoryInterface;
use App\Domain\WhatsApp\ValueObjects\PhoneNumber;
use App\Models\WhatsAppMessage;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * EloquentMessageRepository
 *
 * Eloquent implementation of message persistence.
 * Translates between domain entities and database models.
 */
final class EloquentMessageRepository implements MessageRepositoryInterface
{
    public function save(Message $message): void
    {
        WhatsAppMessage::updateOrCreate(
            ['id' => $message->getId()->toString()],
            [
                'to_phone' => $message->getTo()->getValue(),
                'content' => $message->getContent(),
                'status' => $message->getStatus(),
                'twilio_sid' => $message->getTwilioSid(),
                'created_at' => $message->getCreatedAt(),
                'sent_at' => $message->getSentAt(),
            ]
        );
    }

    public function findById(UuidInterface $id): ?Message
    {
        $model = WhatsAppMessage::find($id->toString());
        return $model ? $this->toDomainEntity($model) : null;
    }

    public function findByPhoneNumber(PhoneNumber $phoneNumber): array
    {
        $models = WhatsAppMessage::where('to_phone', $phoneNumber->getValue())
            ->orderBy('created_at', 'desc')
            ->get();

        return $models->map(fn($model) => $this->toDomainEntity($model))->toArray();
    }

    public function findByStatus(string $status): array
    {
        $models = WhatsAppMessage::where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();

        return $models->map(fn($model) => $this->toDomainEntity($model))->toArray();
    }

    public function findRecent(int $limit = 10): array
    {
        $models = WhatsAppMessage::orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $models->map(fn($model) => $this->toDomainEntity($model))->toArray();
    }

    public function count(): int
    {
        return WhatsAppMessage::count();
    }

    public function exists(UuidInterface $id): bool
    {
        return WhatsAppMessage::where('id', $id->toString())->exists();
    }

    /**
     * Convert Eloquent model to domain entity
     */
    private function toDomainEntity(WhatsAppMessage $model): Message
    {
        $message = new Message(
            PhoneNumber::fromString($model->to_phone),
            $model->content,
            Uuid::fromString($model->id)
        );

        if ($model->status === 'sent' && $model->twilio_sid) {
            $message->markAsSent($model->twilio_sid);
        } elseif ($model->status === 'failed') {
            $message->markAsFailed();
        }

        return $message;
    }
}