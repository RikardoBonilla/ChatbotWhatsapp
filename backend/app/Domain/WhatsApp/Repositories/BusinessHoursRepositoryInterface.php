<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Repositories;

use App\Domain\WhatsApp\Entities\BusinessHours;
use App\Domain\WhatsApp\ValueObjects\MessageId;

interface BusinessHoursRepositoryInterface
{
    public function save(BusinessHours $businessHours): void;

    public function findById(MessageId $id): ?BusinessHours;

    public function findByDayOfWeek(string $dayOfWeek): ?BusinessHours;

    public function findAll(): array;

    public function delete(MessageId $id): void;
}