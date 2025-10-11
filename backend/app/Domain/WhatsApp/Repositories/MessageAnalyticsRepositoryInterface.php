<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Repositories;

use App\Domain\WhatsApp\Entities\MessageAnalytics;
use App\Domain\WhatsApp\ValueObjects\MessageId;
use App\Domain\WhatsApp\ValueObjects\PhoneNumber;
use DateTimeImmutable;

interface MessageAnalyticsRepositoryInterface
{
    public function save(MessageAnalytics $analytics): void;

    public function findById(int $id): ?MessageAnalytics;

    public function findByDate(DateTimeImmutable $date): array;

    public function findByDateRange(DateTimeImmutable $startDate, DateTimeImmutable $endDate): array;

    public function findByKeywordRule(MessageId $keywordRuleId, DateTimeImmutable $startDate, DateTimeImmutable $endDate): array;

    public function findByPhoneNumber(PhoneNumber $phoneNumber, DateTimeImmutable $startDate, DateTimeImmutable $endDate): array;

    public function getOrCreateDailyStats(DateTimeImmutable $date, ?MessageId $keywordRuleId = null, ?PhoneNumber $phoneNumber = null): MessageAnalytics;

    public function getPopularKeywords(DateTimeImmutable $startDate, DateTimeImmutable $endDate, int $limit = 10): array;

    public function getTotalMessagesInPeriod(DateTimeImmutable $startDate, DateTimeImmutable $endDate): array;

    public function deleteOldAnalytics(DateTimeImmutable $before): int;
}