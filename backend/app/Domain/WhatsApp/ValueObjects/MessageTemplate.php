<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\ValueObjects;

final readonly class MessageTemplate
{
    private function __construct(
        private string $template,
        private array $variables = []
    ) {
        $this->validate($template);
    }

    public static function fromString(string $template, array $variables = []): self
    {
        return new self($template, $variables);
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }

    public function withVariables(array $variables): self
    {
        return new self($this->template, array_merge($this->variables, $variables));
    }

    public function hasVariables(): bool
    {
        return preg_match('/\{\{[^}]+\}\}/', $this->template) === 1;
    }

    public function extractVariableNames(): array
    {
        preg_match_all('/\{\{([^}]+)\}\}/', $this->template, $matches);

        return array_unique(array_map('trim', $matches[1] ?? []));
    }

    public function isValid(): bool
    {
        try {
            $this->validate($this->template);
            return true;
        } catch (\InvalidArgumentException) {
            return false;
        }
    }

    public function equals(MessageTemplate $other): bool
    {
        return $this->template === $other->template && $this->variables === $other->variables;
    }

    public function __toString(): string
    {
        return $this->template;
    }

    private function validate(string $template): void
    {
        if (empty(trim($template))) {
            throw new \InvalidArgumentException('Template cannot be empty');
        }

        if (strlen($template) > 2000) {
            throw new \InvalidArgumentException('Template cannot exceed 2000 characters');
        }

        if (preg_match('/\{\{(?![^}]*\}\})|(?<!\{\{[^{]*)\}\}/', $template)) {
            throw new \InvalidArgumentException('Template has malformed variable syntax');
        }
    }
}