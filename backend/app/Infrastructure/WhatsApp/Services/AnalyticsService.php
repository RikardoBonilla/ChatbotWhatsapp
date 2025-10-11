<?php

declare(strict_types=1);

namespace App\Infrastructure\WhatsApp\Services;

use App\Domain\WhatsApp\Repositories\KeywordRuleRepositoryInterface;
use App\Domain\WhatsApp\Repositories\MessageAnalyticsRepositoryInterface;
use App\Domain\WhatsApp\Services\AnalyticsServiceInterface;
use App\Domain\WhatsApp\ValueObjects\MessageId;
use App\Domain\WhatsApp\ValueObjects\PhoneNumber;
use DateTimeImmutable;

final readonly class AnalyticsService implements AnalyticsServiceInterface
{
    public function __construct(
        private MessageAnalyticsRepositoryInterface $analyticsRepository,
        private KeywordRuleRepositoryInterface $keywordRuleRepository
    ) {}

    public function trackIncomingMessage(PhoneNumber $phoneNumber, DateTimeImmutable $timestamp): void
    {
        $dailyStats = $this->analyticsRepository->getOrCreateDailyStats($timestamp, null, $phoneNumber);
        $dailyStats->incrementIncomingMessages();
        $dailyStats->addPeakHour((int) $timestamp->format('H'));

        $this->analyticsRepository->save($dailyStats);
    }

    public function trackOutgoingMessage(PhoneNumber $phoneNumber, ?MessageId $keywordRuleId, float $responseTimeMs, DateTimeImmutable $timestamp): void
    {
        $dailyStats = $this->analyticsRepository->getOrCreateDailyStats($timestamp, $keywordRuleId, $phoneNumber);
        $dailyStats->incrementOutgoingMessages();
        $dailyStats->updateResponseTime($responseTimeMs);

        $this->analyticsRepository->save($dailyStats);
    }

    public function trackSuccessfulMatch(PhoneNumber $phoneNumber, MessageId $keywordRuleId, DateTimeImmutable $timestamp): void
    {
        $dailyStats = $this->analyticsRepository->getOrCreateDailyStats($timestamp, $keywordRuleId, $phoneNumber);
        $dailyStats->incrementSuccessfulMatches();

        $this->analyticsRepository->save($dailyStats);
    }

    public function trackFailedMatch(PhoneNumber $phoneNumber, DateTimeImmutable $timestamp): void
    {
        $dailyStats = $this->analyticsRepository->getOrCreateDailyStats($timestamp, null, $phoneNumber);
        $dailyStats->incrementFailedMatches();

        $this->analyticsRepository->save($dailyStats);
    }

    public function getDashboardData(?DateTimeImmutable $startDate = null, ?DateTimeImmutable $endDate = null): array
    {
        $endDate = $endDate ?? new DateTimeImmutable();
        $startDate = $startDate ?? $endDate->modify('-7 days');

        $totals = $this->analyticsRepository->getTotalMessagesInPeriod($startDate, $endDate);
        $popularKeywords = $this->getPopularKeywords(5);
        $todayStats = $this->getMessageStatistics(new DateTimeImmutable());
        $peakHours = $this->getPeakHours(new DateTimeImmutable());

        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'totals' => [
                'incoming_messages' => (int) ($totals['total_incoming'] ?? 0),
                'outgoing_messages' => (int) ($totals['total_outgoing'] ?? 0),
                'successful_matches' => (int) ($totals['total_successful'] ?? 0),
                'failed_matches' => (int) ($totals['total_failed'] ?? 0),
                'avg_response_time_ms' => round((float) ($totals['avg_response_time'] ?? 0), 2),
            ],
            'rates' => [
                'response_rate' => $this->calculateResponseRate($totals),
                'success_rate' => $this->calculateSuccessRate($totals),
            ],
            'popular_keywords' => $popularKeywords,
            'today' => $todayStats,
            'peak_hours' => $peakHours,
        ];
    }

    public function getPopularKeywords(int $limit = 10): array
    {
        $endDate = new DateTimeImmutable();
        $startDate = $endDate->modify('-30 days');

        $popularData = $this->analyticsRepository->getPopularKeywords($startDate, $endDate, $limit);
        $result = [];

        foreach ($popularData as $data) {
            if (!$data['keyword_rule_id']) {
                continue;
            }

            $keywordRule = $this->keywordRuleRepository->findById(
                MessageId::fromString($data['keyword_rule_id'])
            );

            if ($keywordRule) {
                $result[] = [
                    'keyword_rule_id' => $data['keyword_rule_id'],
                    'keywords' => $keywordRule->getKeywords(),
                    'total_matches' => (int) $data['total_matches'],
                    'priority' => $keywordRule->getPriority(),
                ];
            }
        }

        return $result;
    }

    public function getMessageStatistics(DateTimeImmutable $date): array
    {
        $analytics = $this->analyticsRepository->findByDate($date);

        $totals = [
            'incoming' => 0,
            'outgoing' => 0,
            'successful' => 0,
            'failed' => 0,
        ];

        foreach ($analytics as $analytic) {
            $totals['incoming'] += $analytic->getIncomingMessages();
            $totals['outgoing'] += $analytic->getOutgoingMessages();
            $totals['successful'] += $analytic->getSuccessfulMatches();
            $totals['failed'] += $analytic->getFailedMatches();
        }

        return [
            'date' => $date->format('Y-m-d'),
            'totals' => $totals,
            'response_rate' => $this->calculateResponseRateFromTotals($totals),
            'success_rate' => $this->calculateSuccessRateFromTotals($totals),
        ];
    }

    public function getPeakHours(DateTimeImmutable $date): array
    {
        $analytics = $this->analyticsRepository->findByDate($date);
        $hourlyStats = [];

        foreach ($analytics as $analytic) {
            foreach ($analytic->getPeakHours() as $hour => $count) {
                if (!isset($hourlyStats[$hour])) {
                    $hourlyStats[$hour] = 0;
                }
                $hourlyStats[$hour] += $count;
            }
        }

        arsort($hourlyStats);

        return array_slice($hourlyStats, 0, 5, true);
    }

    public function getActiveUsers(DateTimeImmutable $date): array
    {
        $analytics = $this->analyticsRepository->findByDate($date);
        $activeUsers = [];

        foreach ($analytics as $analytic) {
            $phoneNumber = $analytic->getPhoneNumber();
            if ($phoneNumber && $analytic->getIncomingMessages() > 0) {
                $activeUsers[] = [
                    'phone_number' => $phoneNumber->getValue(),
                    'incoming_messages' => $analytic->getIncomingMessages(),
                    'outgoing_messages' => $analytic->getOutgoingMessages(),
                ];
            }
        }

        return $activeUsers;
    }

    private function calculateResponseRate(array $totals): float
    {
        $incoming = (int) ($totals['total_incoming'] ?? 0);
        $outgoing = (int) ($totals['total_outgoing'] ?? 0);

        if ($incoming === 0) {
            return 0.0;
        }

        return round(($outgoing / $incoming) * 100, 2);
    }

    private function calculateSuccessRate(array $totals): float
    {
        $successful = (int) ($totals['total_successful'] ?? 0);
        $failed = (int) ($totals['total_failed'] ?? 0);
        $total = $successful + $failed;

        if ($total === 0) {
            return 0.0;
        }

        return round(($successful / $total) * 100, 2);
    }

    private function calculateResponseRateFromTotals(array $totals): float
    {
        if ($totals['incoming'] === 0) {
            return 0.0;
        }

        return round(($totals['outgoing'] / $totals['incoming']) * 100, 2);
    }

    private function calculateSuccessRateFromTotals(array $totals): float
    {
        $total = $totals['successful'] + $totals['failed'];

        if ($total === 0) {
            return 0.0;
        }

        return round(($totals['successful'] / $total) * 100, 2);
    }
}