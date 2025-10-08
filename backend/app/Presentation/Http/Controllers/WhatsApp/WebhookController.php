<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\WhatsApp;

use App\Application\WhatsApp\DTOs\IncomingMessageDTO;
use App\Application\WhatsApp\UseCases\HandleIncomingMessageUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

final readonly class WebhookController
{
    public function __construct(
        private HandleIncomingMessageUseCase $handleIncomingMessage
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            Log::info('Webhook received', $request->all());

            $dto = IncomingMessageDTO::fromWebhookData($request->all());

            $result = $this->handleIncomingMessage->execute($dto);

            if ($result->isSuccess()) {
                return response()->json([
                    'status' => 'received',
                    'message' => 'Message processed successfully'
                ]);
            }

            Log::error('Failed to process incoming message', [
                'error' => $result->getErrorMessage(),
                'webhook_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $result->getErrorMessage()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'webhook_data' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Internal server error'
            ], 500);
        }
    }
}