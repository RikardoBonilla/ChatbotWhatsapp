<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Services;

use App\Domain\WhatsApp\ValueObjects\PhoneNumber;

interface TemplateEngineInterface
{
    public function render(string $template, array $variables = []): string;

    public function renderWithContext(string $template, PhoneNumber $phoneNumber, array $additionalVariables = []): string;

    public function getAvailableVariables(): array;

    public function hasVariables(string $template): bool;

    public function extractVariables(string $template): array;
}