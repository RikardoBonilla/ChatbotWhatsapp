<?php

declare(strict_types=1);

namespace App\Application\WhatsApp\UseCases;

use App\Application\WhatsApp\DTOs\SendMessageDTO;
use App\Domain\WhatsApp\Entities\Message;
use App\Domain\WhatsApp\Repositories\MessageRepositoryInterface;
use App\Domain\WhatsApp\Services\WhatsAppServiceInterface;
use App\Domain\WhatsApp\ValueObjects\PhoneNumber;
use InvalidArgumentException;
use Exception;

/**
 * SendWhatsAppMessageUseCase
 *
 * Orchestrates the process of sending a WhatsApp message.
 * Coordinates domain objects without knowing implementation details.
 */
final class SendWhatsAppMessageUseCase
{
    public function __construct(
        private readonly WhatsAppServiceInterface $whatsAppService,
        private readonly MessageRepositoryInterface $messageRepository
    ) {
    }

    /**
     * Execute the send message operation
     */
    public function execute(SendMessageDTO $dto): SendMessageResult
    {
        try {
            $phoneNumber = PhoneNumber::fromString($dto->phoneNumber);
            $message = new Message($phoneNumber, $dto->content);

            $this->messageRepository->save($message);

            $response = $this->whatsAppService->sendMessage($phoneNumber, $dto->content);

            if ($response->isSuccess()) {
                $message->markAsSent($response->getExternalId());
                $this->messageRepository->save($message);

                return SendMessageResult::success($message->getId()->toString());
            } else {
                $message->markAsFailed();
                $this->messageRepository->save($message);

                return SendMessageResult::failure($response->getErrorMessage());
            }

        } catch (InvalidArgumentException $e) {
            return SendMessageResult::failure($e->getMessage());
        } catch (Exception $e) {
            return SendMessageResult::failure('Failed to send message: ' . $e->getMessage());
        }
    }
}