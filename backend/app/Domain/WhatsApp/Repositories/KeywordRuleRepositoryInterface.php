<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Repositories;

use App\Domain\WhatsApp\Entities\KeywordRule;
use App\Domain\WhatsApp\ValueObjects\MessageId;

interface KeywordRuleRepositoryInterface
{
    public function save(KeywordRule $rule): void;

    public function findById(MessageId $id): ?KeywordRule;

    public function findAll(): array;

    public function findActive(): array;

    public function findByKeyword(string $keyword): array;

    public function delete(MessageId $id): void;
}