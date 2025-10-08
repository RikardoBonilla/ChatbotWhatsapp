<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Repositories;

use App\Domain\WhatsApp\Entities\IncomingMessage;
use App\Domain\WhatsApp\ValueObjects\MessageId;
use App\Domain\WhatsApp\ValueObjects\PhoneNumber;
use App\Domain\WhatsApp\ValueObjects\TwilioSid;

interface IncomingMessageRepositoryInterface
{
    public function save(IncomingMessage $message): void;

    public function findById(MessageId $id): ?IncomingMessage;

    public function findByTwilioSid(TwilioSid $twilioSid): ?IncomingMessage;

    public function findByPhoneNumber(PhoneNumber $phoneNumber, int $limit = 50): array;

    public function findUnprocessed(int $limit = 100): array;

    public function exists(TwilioSid $twilioSid): bool;
}