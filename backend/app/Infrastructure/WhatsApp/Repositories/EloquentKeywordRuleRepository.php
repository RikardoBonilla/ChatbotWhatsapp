<?php

declare(strict_types=1);

namespace App\Infrastructure\WhatsApp\Repositories;

use App\Domain\WhatsApp\Entities\KeywordRule;
use App\Domain\WhatsApp\Repositories\KeywordRuleRepositoryInterface;
use App\Domain\WhatsApp\ValueObjects\MessageId;
use App\Infrastructure\WhatsApp\Models\KeywordRuleModel;

final readonly class EloquentKeywordRuleRepository implements KeywordRuleRepositoryInterface
{
    public function save(KeywordRule $rule): void
    {
        KeywordRuleModel::updateOrCreate(
            ['id' => $rule->getId()->getValue()],
            [
                'keyword' => $rule->getKeyword(),
                'response_template' => $rule->getResponseTemplate(),
                'is_active' => $rule->isActive(),
                'priority' => $rule->getPriority(),
            ]
        );
    }

    public function findById(MessageId $id): ?KeywordRule
    {
        $model = KeywordRuleModel::find($id->getValue());

        return $model ? $this->toDomainEntity($model) : null;
    }

    public function findAll(): array
    {
        $models = KeywordRuleModel::orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return $models->map(fn($model) => $this->toDomainEntity($model))->toArray();
    }

    public function findActive(): array
    {
        $models = KeywordRuleModel::where('is_active', true)
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return $models->map(fn($model) => $this->toDomainEntity($model))->toArray();
    }

    public function findByKeyword(string $keyword): array
    {
        $models = KeywordRuleModel::where('keyword', 'LIKE', "%{$keyword}%")
            ->orderBy('priority', 'desc')
            ->get();

        return $models->map(fn($model) => $this->toDomainEntity($model))->toArray();
    }

    public function delete(MessageId $id): void
    {
        KeywordRuleModel::where('id', $id->getValue())->delete();
    }

    private function toDomainEntity(KeywordRuleModel $model): KeywordRule
    {
        $rule = KeywordRule::create(
            MessageId::fromString($model->id),
            $model->keyword,
            $model->response_template,
            $model->priority
        );

        if (!$model->is_active) {
            $rule->deactivate();
        }

        return $rule;
    }
}