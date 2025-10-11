<?php

declare(strict_types=1);

namespace App\Infrastructure\WhatsApp\Providers;

use App\Domain\WhatsApp\Repositories\BusinessHoursRepositoryInterface;
use App\Domain\WhatsApp\Repositories\ConversationRepositoryInterface;
use App\Domain\WhatsApp\Repositories\IncomingMessageRepositoryInterface;
use App\Domain\WhatsApp\Repositories\KeywordRuleRepositoryInterface;
use App\Domain\WhatsApp\Repositories\MessageAnalyticsRepositoryInterface;
use App\Domain\WhatsApp\Repositories\MessageRepositoryInterface;
use App\Domain\WhatsApp\Services\AnalyticsServiceInterface;
use App\Domain\WhatsApp\Services\BusinessHoursCheckerInterface;
use App\Domain\WhatsApp\Services\ConversationTrackerInterface;
use App\Domain\WhatsApp\Services\FuzzyKeywordMatcherInterface;
use App\Domain\WhatsApp\Services\KeywordMatcherInterface;
use App\Domain\WhatsApp\Services\TemplateEngineInterface;
use App\Domain\WhatsApp\Services\WhatsAppServiceInterface;
use App\Infrastructure\WhatsApp\Repositories\EloquentBusinessHoursRepository;
use App\Infrastructure\WhatsApp\Repositories\EloquentConversationRepository;
use App\Infrastructure\WhatsApp\Repositories\EloquentIncomingMessageRepository;
use App\Infrastructure\WhatsApp\Repositories\EloquentKeywordRuleRepository;
use App\Infrastructure\WhatsApp\Repositories\EloquentMessageAnalyticsRepository;
use App\Infrastructure\WhatsApp\Repositories\EloquentMessageRepository;
use App\Infrastructure\WhatsApp\Services\AnalyticsService;
use App\Infrastructure\WhatsApp\Services\BusinessHoursChecker;
use App\Infrastructure\WhatsApp\Services\ConversationTracker;
use App\Infrastructure\WhatsApp\Services\FuzzyKeywordMatcher;
use App\Infrastructure\WhatsApp\Services\SimpleKeywordMatcher;
use App\Infrastructure\WhatsApp\Services\TemplateEngine;
use App\Infrastructure\WhatsApp\Services\TwilioWhatsAppService;
use Illuminate\Support\ServiceProvider;
use Twilio\Rest\Client;

/**
 * WhatsAppServiceProvider
 *
 * Registers WhatsApp service implementations and dependencies.
 * Configures Twilio client and enables dependency injection.
 */
final class WhatsAppServiceProvider extends ServiceProvider
{
    /**
     * Register services
     */
    public function register(): void
    {
        $this->app->singleton(Client::class, function () {
            return new Client(
                config('services.twilio.sid'),
                config('services.twilio.token')
            );
        });

        $this->app->bind(WhatsAppServiceInterface::class, function ($app) {
            return new TwilioWhatsAppService(
                $app->make(Client::class),
                config('services.twilio.whatsapp_from')
            );
        });

        $this->app->bind(
            MessageRepositoryInterface::class,
            EloquentMessageRepository::class
        );

        $this->app->bind(
            IncomingMessageRepositoryInterface::class,
            EloquentIncomingMessageRepository::class
        );

        $this->app->bind(
            KeywordRuleRepositoryInterface::class,
            EloquentKeywordRuleRepository::class
        );

        $this->app->bind(
            ConversationRepositoryInterface::class,
            EloquentConversationRepository::class
        );

        $this->app->bind(
            BusinessHoursRepositoryInterface::class,
            EloquentBusinessHoursRepository::class
        );

        $this->app->bind(
            MessageAnalyticsRepositoryInterface::class,
            EloquentMessageAnalyticsRepository::class
        );

        $this->app->bind(
            FuzzyKeywordMatcherInterface::class,
            FuzzyKeywordMatcher::class
        );

        $this->app->bind(
            ConversationTrackerInterface::class,
            ConversationTracker::class
        );

        $this->app->bind(
            BusinessHoursCheckerInterface::class,
            BusinessHoursChecker::class
        );

        $this->app->bind(
            TemplateEngineInterface::class,
            TemplateEngine::class
        );

        $this->app->bind(
            AnalyticsServiceInterface::class,
            AnalyticsService::class
        );

        $this->app->bind(
            KeywordMatcherInterface::class,
            SimpleKeywordMatcher::class
        );
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
    }
}