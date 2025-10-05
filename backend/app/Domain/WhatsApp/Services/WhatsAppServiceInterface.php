<?php

declare(strict_types=1);

namespace App\Domain\WhatsApp\Services;

use App\Domain\WhatsApp\ValueObjects\PhoneNumber;

/**
 * WhatsAppServiceInterface
 *
 * Defines the contract for WhatsApp messaging operations.
 * Domain layer defines WHAT messaging operations we need.
 * Infrastructure layer implements HOW to send them (Twilio, etc.).
 */
interface WhatsAppServiceInterface
{
    /**
     * Send a WhatsApp message
     *
     * @return WhatsAppResponse Response containing success status and external ID
     */
    public function sendMessage(PhoneNumber $to, string $content): WhatsAppResponse;

    /**
     * Check if the service is available
     */
    public function isAvailable(): bool;
}