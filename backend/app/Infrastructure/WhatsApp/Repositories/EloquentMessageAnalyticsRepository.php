<?php

declare(strict_types=1);

namespace App\Infrastructure\WhatsApp\Repositories;

use App\Domain\WhatsApp\Entities\MessageAnalytics;
use App\Domain\WhatsApp\Repositories\MessageAnalyticsRepositoryInterface;
use App\Domain\WhatsApp\ValueObjects\MessageId;
use App\Domain\WhatsApp\ValueObjects\PhoneNumber;
use App\Infrastructure\WhatsApp\Models\MessageAnalyticsModel;
use DateTimeImmutable;

final readonly class EloquentMessageAnalyticsRepository implements MessageAnalyticsRepositoryInterface
{
    public function save(MessageAnalytics $analytics): void
    {
        MessageAnalyticsModel::updateOrCreate(
            [
                'id' => $analytics->getId() ?: null,
                'date' => $analytics->getDate()->format('Y-m-d'),
                'keyword_rule_id' => $analytics->getKeywordRuleId()?->getValue(),
                'phone_number' => $analytics->getPhoneNumber()?->getValue(),
            ],
            [
                'incoming_messages' => $analytics->getIncomingMessages(),
                'outgoing_messages' => $analytics->getOutgoingMessages(),
                'successful_matches' => $analytics->getSuccessfulMatches(),
                'failed_matches' => $analytics->getFailedMatches(),
                'avg_response_time_ms' => $analytics->getAvgResponseTimeMs(),
                'peak_hours' => $analytics->getPeakHours(),
            ]
        );
    }

    public function findById(int $id): ?MessageAnalytics
    {
        $model = MessageAnalyticsModel::find($id);

        return $model ? $this->toDomainEntity($model) : null;
    }

    public function findByDate(DateTimeImmutable $date): array
    {
        $models = MessageAnalyticsModel::where('date', $date->format('Y-m-d'))->get();

        return $models->map(fn($model) => $this->toDomainEntity($model))->toArray();
    }

    public function findByDateRange(DateTimeImmutable $startDate, DateTimeImmutable $endDate): array
    {
        $models = MessageAnalyticsModel::whereBetween('date', [
            $startDate->format('Y-m-d'),
            $endDate->format('Y-m-d')
        ])->orderBy('date', 'desc')->get();

        return $models->map(fn($model) => $this->toDomainEntity($model))->toArray();
    }

    public function findByKeywordRule(MessageId $keywordRuleId, DateTimeImmutable $startDate, DateTimeImmutable $endDate): array
    {
        $models = MessageAnalyticsModel::where('keyword_rule_id', $keywordRuleId->getValue())
            ->whereBetween('date', [
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d')
            ])
            ->orderBy('date', 'desc')
            ->get();

        return $models->map(fn($model) => $this->toDomainEntity($model))->toArray();
    }

    public function findByPhoneNumber(PhoneNumber $phoneNumber, DateTimeImmutable $startDate, DateTimeImmutable $endDate): array
    {
        $models = MessageAnalyticsModel::where('phone_number', $phoneNumber->getValue())
            ->whereBetween('date', [
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d')
            ])
            ->orderBy('date', 'desc')
            ->get();

        return $models->map(fn($model) => $this->toDomainEntity($model))->toArray();
    }

    public function getOrCreateDailyStats(DateTimeImmutable $date, ?MessageId $keywordRuleId = null, ?PhoneNumber $phoneNumber = null): MessageAnalytics
    {
        $model = MessageAnalyticsModel::firstOrCreate([
            'date' => $date->format('Y-m-d'),
            'keyword_rule_id' => $keywordRuleId?->getValue(),
            'phone_number' => $phoneNumber?->getValue(),
        ], [
            'incoming_messages' => 0,
            'outgoing_messages' => 0,
            'successful_matches' => 0,
            'failed_matches' => 0,
            'avg_response_time_ms' => 0,
            'peak_hours' => [],
        ]);

        return $this->toDomainEntity($model);
    }

    public function getPopularKeywords(DateTimeImmutable $startDate, DateTimeImmutable $endDate, int $limit = 10): array
    {
        return MessageAnalyticsModel::selectRaw('keyword_rule_id, SUM(successful_matches) as total_matches')
            ->whereNotNull('keyword_rule_id')
            ->whereBetween('date', [
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d')
            ])
            ->groupBy('keyword_rule_id')
            ->orderBy('total_matches', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getTotalMessagesInPeriod(DateTimeImmutable $startDate, DateTimeImmutable $endDate): array
    {
        return MessageAnalyticsModel::selectRaw('
                SUM(incoming_messages) as total_incoming,
                SUM(outgoing_messages) as total_outgoing,
                SUM(successful_matches) as total_successful,
                SUM(failed_matches) as total_failed,
                AVG(avg_response_time_ms) as avg_response_time
            ')
            ->whereBetween('date', [
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d')
            ])
            ->first()
            ->toArray();
    }

    public function deleteOldAnalytics(DateTimeImmutable $before): int
    {
        return MessageAnalyticsModel::where('date', '<', $before->format('Y-m-d'))->delete();
    }

    private function toDomainEntity(MessageAnalyticsModel $model): MessageAnalytics
    {
        $keywordRuleId = $model->keyword_rule_id ? MessageId::fromString($model->keyword_rule_id) : null;
        $phoneNumber = $model->phone_number ? PhoneNumber::fromString($model->phone_number) : null;

        $analytics = MessageAnalytics::create(
            $model->id,
            DateTimeImmutable::createFromFormat('Y-m-d', $model->date->format('Y-m-d')),
            $keywordRuleId,
            $phoneNumber
        );

        for ($i = 0; $i < $model->incoming_messages; $i++) {
            $analytics->incrementIncomingMessages();
        }

        for ($i = 0; $i < $model->outgoing_messages; $i++) {
            $analytics->incrementOutgoingMessages();
        }

        for ($i = 0; $i < $model->successful_matches; $i++) {
            $analytics->incrementSuccessfulMatches();
        }

        for ($i = 0; $i < $model->failed_matches; $i++) {
            $analytics->incrementFailedMatches();
        }

        if ($model->avg_response_time_ms > 0) {
            $analytics->updateResponseTime($model->avg_response_time_ms);
        }

        return $analytics;
    }
}