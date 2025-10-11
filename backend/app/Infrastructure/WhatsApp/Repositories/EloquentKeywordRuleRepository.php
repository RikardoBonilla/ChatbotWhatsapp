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
                'keywords' => $rule->getKeywords(),
                'response_template' => $rule->getResponseTemplate(),
                'is_active' => $rule->isActive(),
                'priority' => $rule->getPriority(),
                'fuzzy_match' => $rule->getFuzzyMatch(),
                'trigger_type' => $rule->getTriggerType(),
                'variables' => $rule->getVariables(),
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
        $models = KeywordRuleModel::where('keywords', 'LIKE', "%{$keyword}%")
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
            $model->keywords ?? [$model->keyword ?? ''],
            $model->response_template,
            $model->priority,
            $model->fuzzy_match ?? false,
            $model->trigger_type ?? 'contains',
            $model->variables
        );

        if (!$model->is_active) {
            $rule->deactivate();
        }

        return $rule;
    }
}