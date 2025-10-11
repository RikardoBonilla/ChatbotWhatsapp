<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Entities;

use App\Domain\WhatsApp\ValueObjects\MessageId;
use DateTimeImmutable;

final class BusinessHours
{
    private function __construct(
        private readonly MessageId $id,
        private readonly string $dayOfWeek,
        private readonly ?DateTimeImmutable $openTime,
        private readonly ?DateTimeImmutable $closeTime,
        private readonly bool $isClosed,
        private readonly string $timezone,
        private readonly DateTimeImmutable $createdAt = new DateTimeImmutable(),
    ) {
        $this->validateDayOfWeek($dayOfWeek);
        $this->validateTimes($openTime, $closeTime, $isClosed);
    }

    public static function create(
        MessageId $id,
        string $dayOfWeek,
        ?DateTimeImmutable $openTime = null,
        ?DateTimeImmutable $closeTime = null,
        bool $isClosed = false,
        string $timezone = 'America/Bogota'
    ): self {
        return new self($id, $dayOfWeek, $openTime, $closeTime, $isClosed, $timezone);
    }

    public static function createClosed(MessageId $id, string $dayOfWeek, string $timezone = 'America/Bogota'): self
    {
        return new self($id, $dayOfWeek, null, null, true, $timezone);
    }

    public function getId(): MessageId
    {
        return $this->id;
    }

    public function getDayOfWeek(): string
    {
        return $this->dayOfWeek;
    }

    public function getOpenTime(): ?DateTimeImmutable
    {
        return $this->openTime;
    }

    public function getCloseTime(): ?DateTimeImmutable
    {
        return $this->closeTime;
    }

    public function isClosed(): bool
    {
        return $this->isClosed;
    }

    public function getTimezone(): string
    {
        return $this->timezone;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function isOpenAt(DateTimeImmutable $time): bool
    {
        if ($this->isClosed) {
            return false;
        }

        if ($this->openTime === null || $this->closeTime === null) {
            return false;
        }

        $timeOfDay = $time->format('H:i:s');
        $openTimeOfDay = $this->openTime->format('H:i:s');
        $closeTimeOfDay = $this->closeTime->format('H:i:s');

        return $timeOfDay >= $openTimeOfDay && $timeOfDay <= $closeTimeOfDay;
    }

    public function getFormattedHours(): string
    {
        if ($this->isClosed) {
            return 'Cerrado';
        }

        if ($this->openTime === null || $this->closeTime === null) {
            return 'Horario no definido';
        }

        return sprintf(
            '%s - %s',
            $this->openTime->format('g:i A'),
            $this->closeTime->format('g:i A')
        );
    }

    private function validateDayOfWeek(string $dayOfWeek): void
    {
        $validDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        if (!in_array(strtolower($dayOfWeek), $validDays)) {
            throw new \InvalidArgumentException('Invalid day of week: ' . $dayOfWeek);
        }
    }

    private function validateTimes(?DateTimeImmutable $openTime, ?DateTimeImmutable $closeTime, bool $isClosed): void
    {
        if ($isClosed) {
            return;
        }

        if ($openTime === null || $closeTime === null) {
            throw new \InvalidArgumentException('Open and close times are required when not closed');
        }

        if ($openTime >= $closeTime) {
            throw new \InvalidArgumentException('Open time must be before close time');
        }
    }
}