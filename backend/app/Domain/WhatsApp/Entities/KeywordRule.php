<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Entities;

use App\Domain\WhatsApp\ValueObjects\MessageId;
use DateTimeImmutable;

final class KeywordRule
{
    private function __construct(
        private readonly MessageId $id,
        private readonly string $keyword,
        private readonly string $responseTemplate,
        private bool $isActive = true,
        private readonly int $priority = 0,
        private readonly DateTimeImmutable $createdAt = new DateTimeImmutable(),
    ) {
        $this->validateKeyword($keyword);
        $this->validateResponseTemplate($responseTemplate);
    }

    public static function create(
        MessageId $id,
        string $keyword,
        string $responseTemplate,
        int $priority = 0
    ): self {
        return new self($id, $keyword, $responseTemplate, true, $priority);
    }

    public function getId(): MessageId
    {
        return $this->id;
    }

    public function getKeyword(): string
    {
        return $this->keyword;
    }

    public function getResponseTemplate(): string
    {
        return $this->responseTemplate;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function activate(): void
    {
        $this->isActive = true;
    }

    public function deactivate(): void
    {
        $this->isActive = false;
    }

    public function matches(string $content): bool
    {
        if (!$this->isActive) {
            return false;
        }

        $normalizedContent = strtolower(trim($content));
        $normalizedKeyword = strtolower(trim($this->keyword));

        return str_contains($normalizedContent, $normalizedKeyword);
    }

    private function validateKeyword(string $keyword): void
    {
        if (empty(trim($keyword))) {
            throw new \InvalidArgumentException('Keyword cannot be empty');
        }

        if (strlen($keyword) > 100) {
            throw new \InvalidArgumentException('Keyword cannot exceed 100 characters');
        }
    }

    private function validateResponseTemplate(string $responseTemplate): void
    {
        if (empty(trim($responseTemplate))) {
            throw new \InvalidArgumentException('Response template cannot be empty');
        }

        if (strlen($responseTemplate) > 1600) {
            throw new \InvalidArgumentException('Response template cannot exceed 1600 characters');
        }
    }
}