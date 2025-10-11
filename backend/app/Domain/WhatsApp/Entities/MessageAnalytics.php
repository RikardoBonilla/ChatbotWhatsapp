<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Entities;

use App\Domain\WhatsApp\ValueObjects\MessageId;
use App\Domain\WhatsApp\ValueObjects\PhoneNumber;
use DateTimeImmutable;

final class MessageAnalytics
{
    private function __construct(
        private readonly int $id,
        private readonly DateTimeImmutable $date,
        private readonly ?MessageId $keywordRuleId,
        private readonly ?PhoneNumber $phoneNumber,
        private int $incomingMessages,
        private int $outgoingMessages,
        private int $successfulMatches,
        private int $failedMatches,
        private float $avgResponseTimeMs,
        private array $peakHours,
        private readonly DateTimeImmutable $createdAt = new DateTimeImmutable(),
    ) {}

    public static function create(
        int $id,
        DateTimeImmutable $date,
        ?MessageId $keywordRuleId = null,
        ?PhoneNumber $phoneNumber = null
    ): self {
        return new self(
            $id,
            $date,
            $keywordRuleId,
            $phoneNumber,
            0,
            0,
            0,
            0,
            0.0,
            []
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getKeywordRuleId(): ?MessageId
    {
        return $this->keywordRuleId;
    }

    public function getPhoneNumber(): ?PhoneNumber
    {
        return $this->phoneNumber;
    }

    public function getIncomingMessages(): int
    {
        return $this->incomingMessages;
    }

    public function getOutgoingMessages(): int
    {
        return $this->outgoingMessages;
    }

    public function getSuccessfulMatches(): int
    {
        return $this->successfulMatches;
    }

    public function getFailedMatches(): int
    {
        return $this->failedMatches;
    }

    public function getAvgResponseTimeMs(): float
    {
        return $this->avgResponseTimeMs;
    }

    public function getPeakHours(): array
    {
        return $this->peakHours;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function incrementIncomingMessages(int $count = 1): void
    {
        $this->incomingMessages += $count;
    }

    public function incrementOutgoingMessages(int $count = 1): void
    {
        $this->outgoingMessages += $count;
    }

    public function incrementSuccessfulMatches(int $count = 1): void
    {
        $this->successfulMatches += $count;
    }

    public function incrementFailedMatches(int $count = 1): void
    {
        $this->failedMatches += $count;
    }

    public function updateResponseTime(float $responseTimeMs): void
    {
        if ($this->outgoingMessages === 0) {
            $this->avgResponseTimeMs = $responseTimeMs;
        } else {
            $this->avgResponseTimeMs = (($this->avgResponseTimeMs * ($this->outgoingMessages - 1)) + $responseTimeMs) / $this->outgoingMessages;
        }
    }

    public function addPeakHour(int $hour): void
    {
        if (!isset($this->peakHours[$hour])) {
            $this->peakHours[$hour] = 0;
        }
        $this->peakHours[$hour]++;
    }

    public function getSuccessRate(): float
    {
        $totalMessages = $this->successfulMatches + $this->failedMatches;

        if ($totalMessages === 0) {
            return 0.0;
        }

        return ($this->successfulMatches / $totalMessages) * 100;
    }

    public function getResponseRate(): float
    {
        if ($this->incomingMessages === 0) {
            return 0.0;
        }

        return ($this->outgoingMessages / $this->incomingMessages) * 100;
    }

    public function getMostActiveHour(): ?int
    {
        if (empty($this->peakHours)) {
            return null;
        }

        return array_key_first(array_slice(arsort($this->peakHours), 0, 1, true));
    }
}