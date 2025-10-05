<?php

declare(strict_types=1);

namespace App\Infrastructure\WhatsApp\Services;

use App\Domain\WhatsApp\Services\WhatsAppServiceInterface;
use App\Domain\WhatsApp\Services\WhatsAppResponse;
use App\Domain\WhatsApp\ValueObjects\PhoneNumber;
use Twilio\Rest\Client;
use Exception;

/**
 * TwilioWhatsAppService
 *
 * Twilio implementation of WhatsApp messaging service.
 * Handles actual API calls to Twilio WhatsApp API.
 */
final class TwilioWhatsAppService implements WhatsAppServiceInterface
{
    public function __construct(
        private readonly Client $twilioClient,
        private readonly string $fromNumber
    ) {
    }

    /**
     * Send a WhatsApp message via Twilio
     */
    public function sendMessage(PhoneNumber $to, string $content): WhatsAppResponse
    {
        try {
            $message = $this->twilioClient->messages->create(
                $to->getWhatsAppFormat(),
                [
                    'from' => $this->fromNumber,
                    'body' => $content
                ]
            );

            return WhatsAppResponse::success($message->sid);

        } catch (Exception $e) {
            return WhatsAppResponse::failure($e->getMessage());
        }
    }

    /**
     * Check if Twilio service is available
     */
    public function isAvailable(): bool
    {
        try {
            $this->twilioClient->api->v2010->account->fetch();
            return true;
        } catch (Exception) {
            return false;
        }
    }
}