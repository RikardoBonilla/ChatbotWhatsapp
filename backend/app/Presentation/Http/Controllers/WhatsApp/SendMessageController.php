<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\WhatsApp;

use App\Application\WhatsApp\DTOs\SendMessageDTO;
use App\Application\WhatsApp\UseCases\SendWhatsAppMessageUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * SendMessageController
 *
 * HTTP entry point for sending WhatsApp messages.
 * Thin controller that delegates to use cases.
 */
final class SendMessageController
{
    public function __construct(
        private readonly SendWhatsAppMessageUseCase $sendMessageUseCase
    ) {
    }

    /**
     * Send a WhatsApp message
     */
    public function __invoke(Request $request): JsonResponse
    {
        $dto = SendMessageDTO::fromArray($request->all());

        $result = $this->sendMessageUseCase->execute($dto);

        if ($result->isSuccess()) {
            return response()->json([
                'success' => true,
                'message_id' => $result->getMessageId(),
                'message' => 'Message sent successfully'
            ], 201);
        }

        return response()->json([
            'success' => false,
            'error' => $result->getErrorMessage()
        ], 400);
    }
}