<?php

declare(strict_types=1);

namespace App\Infrastructure\WhatsApp\Services;

use App\Domain\WhatsApp\Services\BusinessHoursCheckerInterface;
use App\Domain\WhatsApp\Services\ConversationTrackerInterface;
use App\Domain\WhatsApp\Services\TemplateEngineInterface;
use App\Domain\WhatsApp\ValueObjects\PhoneNumber;

final readonly class TemplateEngine implements TemplateEngineInterface
{
    public function __construct(
        private ConversationTrackerInterface $conversationTracker,
        private BusinessHoursCheckerInterface $businessHoursChecker
    ) {}

    public function render(string $template, array $variables = []): string
    {
        $allVariables = array_merge($this->getSystemVariables(), $variables);

        return $this->replaceVariables($template, $allVariables);
    }

    public function renderWithContext(string $template, PhoneNumber $phoneNumber, array $additionalVariables = []): string
    {
        $conversation = $this->conversationTracker->getOrCreateConversation($phoneNumber);
        $contextVariables = $conversation->getContext();

        $allVariables = array_merge(
            $this->getSystemVariables(),
            $contextVariables,
            $additionalVariables
        );

        return $this->replaceVariables($template, $allVariables);
    }

    public function getAvailableVariables(): array
    {
        return [
            'name' => 'User name from conversation context',
            'time' => 'Current time (e.g., 3:30 PM)',
            'date' => 'Current date (e.g., March 15, 2024)',
            'day' => 'Current day of week (e.g., Monday)',
            'phone' => 'User phone number',
            'business_name' => 'Your business name',
            'business_hours' => 'Business operating hours',
        ];
    }

    public function hasVariables(string $template): bool
    {
        return preg_match('/\{\{[^}]+\}\}/', $template) === 1;
    }

    public function extractVariables(string $template): array
    {
        preg_match_all('/\{\{([^}]+)\}\}/', $template, $matches);

        return array_unique($matches[1] ?? []);
    }

    private function replaceVariables(string $template, array $variables): string
    {
        $result = $template;

        foreach ($variables as $key => $value) {
            if (is_scalar($value) || $value === null) {
                $placeholder = '{{' . $key . '}}';
                $result = str_replace($placeholder, (string)$value, $result);
            }
        }

        $result = $this->handleConditionals($result, $variables);

        return $this->cleanUnusedVariables($result);
    }

    private function getSystemVariables(): array
    {
        $now = new \DateTimeImmutable();
        $businessStatus = $this->businessHoursChecker->getCurrentBusinessStatus();
        $todayHours = $this->businessHoursChecker->getBusinessHoursForToday();

        return [
            'time' => $now->format('g:i A'),
            'date' => $now->format('F j, Y'),
            'day' => $now->format('l'),
            'business_name' => 'Nuestro Negocio',
            'business_hours' => $todayHours['hours'] ?? 'Horario no definido',
            'business_status' => $businessStatus['status_message'],
            'is_business_open' => $businessStatus['is_open'] ? 'true' : 'false',
            'next_open_time' => $businessStatus['next_open']?->format('g:i A l') ?? 'No definido',
        ];
    }

    private function handleConditionals(string $template, array $variables): string
    {
        $pattern = '/\{\{if\s+([^}]+)\}\}(.*?)\{\{endif\}\}/s';

        return preg_replace_callback($pattern, function ($matches) use ($variables) {
            $condition = trim($matches[1]);
            $content = $matches[2];

            if (isset($variables[$condition]) && !empty($variables[$condition])) {
                return $content;
            }

            return '';
        }, $template);
    }

    private function cleanUnusedVariables(string $template): string
    {
        return preg_replace('/\{\{[^}]+\}\}/', '', $template);
    }
}