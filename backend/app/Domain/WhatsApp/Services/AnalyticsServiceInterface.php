<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Services;

use App\Domain\WhatsApp\ValueObjects\MessageId;
use App\Domain\WhatsApp\ValueObjects\PhoneNumber;
use DateTimeImmutable;

interface AnalyticsServiceInterface
{
    public function trackIncomingMessage(PhoneNumber $phoneNumber, DateTimeImmutable $timestamp): void;

    public function trackOutgoingMessage(PhoneNumber $phoneNumber, ?MessageId $keywordRuleId, float $responseTimeMs, DateTimeImmutable $timestamp): void;

    public function trackSuccessfulMatch(PhoneNumber $phoneNumber, MessageId $keywordRuleId, DateTimeImmutable $timestamp): void;

    public function trackFailedMatch(PhoneNumber $phoneNumber, DateTimeImmutable $timestamp): void;

    public function getDashboardData(?DateTimeImmutable $startDate = null, ?DateTimeImmutable $endDate = null): array;

    public function getPopularKeywords(int $limit = 10): array;

    public function getMessageStatistics(DateTimeImmutable $date): array;

    public function getPeakHours(DateTimeImmutable $date): array;

    public function getActiveUsers(DateTimeImmutable $date): array;
}