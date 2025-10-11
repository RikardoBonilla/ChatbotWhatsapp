<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Services;

use DateTimeImmutable;

interface BusinessHoursCheckerInterface
{
    public function isBusinessOpen(?DateTimeImmutable $dateTime = null): bool;

    public function getCurrentBusinessStatus(): array;

    public function getNextOpenTime(): ?DateTimeImmutable;

    public function getFormattedBusinessHours(string $dayOfWeek): string;

    public function getAllBusinessHours(): array;

    public function getBusinessHoursForToday(): ?array;
}