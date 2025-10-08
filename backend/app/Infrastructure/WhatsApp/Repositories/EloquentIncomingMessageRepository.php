<?php

declare(strict_types=1);

namespace App\Infrastructure\WhatsApp\Repositories;

use App\Domain\WhatsApp\Entities\IncomingMessage;
use App\Domain\WhatsApp\Repositories\IncomingMessageRepositoryInterface;
use App\Domain\WhatsApp\ValueObjects\MessageId;
use App\Domain\WhatsApp\ValueObjects\PhoneNumber;
use App\Domain\WhatsApp\ValueObjects\TwilioSid;
use App\Infrastructure\WhatsApp\Models\IncomingMessageModel;

final readonly class EloquentIncomingMessageRepository implements IncomingMessageRepositoryInterface
{
    public function save(IncomingMessage $message): void
    {
        IncomingMessageModel::updateOrCreate(
            ['twilio_sid' => $message->getTwilioSid()->getValue()],
            [
                'id' => $message->getId()->getValue(),
                'from_phone' => $message->getFromPhone()->getValue(),
                'content' => $message->getContent(),
                'processed' => $message->isProcessed(),
                'response_message_id' => $message->getResponseMessageId()?->getValue(),
            ]
        );
    }

    public function findById(MessageId $id): ?IncomingMessage
    {
        $model = IncomingMessageModel::find($id->getValue());

        return $model ? $this->toDomainEntity($model) : null;
    }

    public function findByTwilioSid(TwilioSid $twilioSid): ?IncomingMessage
    {
        $model = IncomingMessageModel::where('twilio_sid', $twilioSid->getValue())->first();

        return $model ? $this->toDomainEntity($model) : null;
    }

    public function findByPhoneNumber(PhoneNumber $phoneNumber, int $limit = 50): array
    {
        $models = IncomingMessageModel::where('from_phone', $phoneNumber->getValue())
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return $models->map(fn($model) => $this->toDomainEntity($model))->toArray();
    }

    public function findUnprocessed(int $limit = 100): array
    {
        $models = IncomingMessageModel::where('processed', false)
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();

        return $models->map(fn($model) => $this->toDomainEntity($model))->toArray();
    }

    public function exists(TwilioSid $twilioSid): bool
    {
        return IncomingMessageModel::where('twilio_sid', $twilioSid->getValue())->exists();
    }

    private function toDomainEntity(IncomingMessageModel $model): IncomingMessage
    {
        $message = IncomingMessage::create(
            MessageId::fromString($model->id),
            PhoneNumber::fromString($model->from_phone),
            $model->content,
            TwilioSid::fromString($model->twilio_sid)
        );

        if ($model->processed) {
            $message->markAsProcessed();
        }

        if ($model->response_message_id) {
            $message->setResponseMessageId(MessageId::fromString($model->response_message_id));
        }

        return $message;
    }
}