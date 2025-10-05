<?php

declare(strict_types=1);

namespace App\Infrastructure\WhatsApp\Providers;

use App\Domain\WhatsApp\Repositories\MessageRepositoryInterface;
use App\Domain\WhatsApp\Services\WhatsAppServiceInterface;
use App\Infrastructure\WhatsApp\Repositories\EloquentMessageRepository;
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
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
    }
}