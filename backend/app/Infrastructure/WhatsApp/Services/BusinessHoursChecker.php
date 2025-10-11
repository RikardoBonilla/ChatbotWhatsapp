<?php

declare(strict_types=1);

namespace App\Infrastructure\WhatsApp\Services;

use App\Domain\WhatsApp\Repositories\BusinessHoursRepositoryInterface;
use App\Domain\WhatsApp\Services\BusinessHoursCheckerInterface;
use DateTimeImmutable;
use DateTimeZone;

final readonly class BusinessHoursChecker implements BusinessHoursCheckerInterface
{
    public function __construct(
        private BusinessHoursRepositoryInterface $businessHoursRepository
    ) {}

    public function isBusinessOpen(?DateTimeImmutable $dateTime = null): bool
    {
        $dateTime = $dateTime ?? new DateTimeImmutable();
        $dayOfWeek = strtolower($dateTime->format('l'));

        $businessHours = $this->businessHoursRepository->findByDayOfWeek($dayOfWeek);

        if ($businessHours === null) {
            return false;
        }

        return $businessHours->isOpenAt($dateTime);
    }

    public function getCurrentBusinessStatus(): array
    {
        $now = new DateTimeImmutable();
        $isOpen = $this->isBusinessOpen($now);

        return [
            'is_open' => $isOpen,
            'current_time' => $now->format('g:i A'),
            'current_day' => $now->format('l'),
            'status_message' => $isOpen ? 'Abierto' : 'Cerrado',
            'next_open' => $isOpen ? null : $this->getNextOpenTime(),
        ];
    }

    public function getNextOpenTime(): ?DateTimeImmutable
    {
        $now = new DateTimeImmutable();
        $currentDay = strtolower($now->format('l'));

        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $currentDayIndex = array_search($currentDay, $daysOfWeek);

        for ($i = 0; $i < 7; $i++) {
            $dayIndex = ($currentDayIndex + $i) % 7;
            $day = $daysOfWeek[$dayIndex];

            $businessHours = $this->businessHoursRepository->findByDayOfWeek($day);

            if ($businessHours === null || $businessHours->isClosed()) {
                continue;
            }

            $openTime = $businessHours->getOpenTime();
            if ($openTime === null) {
                continue;
            }

            $nextDate = $now->modify("+{$i} days")->setTime(
                (int)$openTime->format('H'),
                (int)$openTime->format('i'),
                (int)$openTime->format('s')
            );

            if ($i === 0 && $nextDate <= $now) {
                continue;
            }

            return $nextDate;
        }

        return null;
    }

    public function getFormattedBusinessHours(string $dayOfWeek): string
    {
        $businessHours = $this->businessHoursRepository->findByDayOfWeek(strtolower($dayOfWeek));

        if ($businessHours === null) {
            return 'Horario no definido';
        }

        return $businessHours->getFormattedHours();
    }

    public function getAllBusinessHours(): array
    {
        $allHours = $this->businessHoursRepository->findAll();
        $result = [];

        foreach ($allHours as $hours) {
            $result[$hours->getDayOfWeek()] = [
                'day' => ucfirst($hours->getDayOfWeek()),
                'hours' => $hours->getFormattedHours(),
                'is_closed' => $hours->isClosed(),
            ];
        }

        return $result;
    }

    public function getBusinessHoursForToday(): ?array
    {
        $today = strtolower((new DateTimeImmutable())->format('l'));
        $businessHours = $this->businessHoursRepository->findByDayOfWeek($today);

        if ($businessHours === null) {
            return null;
        }

        return [
            'day' => ucfirst($today),
            'hours' => $businessHours->getFormattedHours(),
            'is_closed' => $businessHours->isClosed(),
            'is_open_now' => $this->isBusinessOpen(),
        ];
    }
}