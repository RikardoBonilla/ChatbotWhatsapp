<?php

declare(strict_types=1);

namespace App\Application\WhatsApp\UseCases;

use App\Application\WhatsApp\DTOs\IncomingMessageDTO;
use App\Application\WhatsApp\DTOs\SendMessageDTO;
use App\Application\WhatsApp\Results\HandleIncomingMessageResult;
use App\Domain\WhatsApp\Entities\IncomingMessage;
use App\Domain\WhatsApp\Repositories\IncomingMessageRepositoryInterface;
use App\Domain\WhatsApp\Repositories\KeywordRuleRepositoryInterface;
use App\Domain\WhatsApp\Services\KeywordMatcherInterface;
use App\Domain\WhatsApp\ValueObjects\MessageId;
use App\Domain\WhatsApp\ValueObjects\PhoneNumber;
use App\Domain\WhatsApp\ValueObjects\TwilioSid;

final readonly class HandleIncomingMessageUseCase
{
    public function __construct(
        private IncomingMessageRepositoryInterface $incomingRepository,
        private KeywordRuleRepositoryInterface $keywordRepository,
        private KeywordMatcherInterface $keywordMatcher,
        private SendWhatsAppMessageUseCase $sendMessageUseCase,
    ) {}

    public function execute(IncomingMessageDTO $dto): HandleIncomingMessageResult
    {
        try {
            $twilioSid = TwilioSid::fromString($dto->twilioSid);

            $existingMessage = $this->incomingRepository->findByTwilioSid($twilioSid);
            if ($existingMessage !== null) {
                return HandleIncomingMessageResult::success($existingMessage);
            }

            $incomingMessage = IncomingMessage::create(
                MessageId::generate(),
                PhoneNumber::fromString($dto->fromPhone),
                $dto->content,
                $twilioSid
            );

            $this->incomingRepository->save($incomingMessage);

            $matchingRules = $this->keywordMatcher->findMatches($dto->content);

            if (!empty($matchingRules)) {
                $bestRule = $this->selectBestRule($matchingRules);

                $sendResult = $this->sendMessageUseCase->execute(
                    new SendMessageDTO(
                        $dto->fromPhone,
                        $bestRule->getResponseTemplate()
                    )
                );

                if ($sendResult->isSuccess()) {
                    $incomingMessage->markAsProcessed();
                    $incomingMessage->setResponseMessageId(MessageId::fromString($sendResult->getMessageId()));
                    $this->incomingRepository->save($incomingMessage);
                }
            }

            return HandleIncomingMessageResult::success($incomingMessage);

        } catch (\Exception $e) {
            return HandleIncomingMessageResult::failure($e->getMessage());
        }
    }

    private function selectBestRule(array $rules): ?\App\Domain\WhatsApp\Entities\KeywordRule
    {
        if (empty($rules)) {
            return null;
        }

        usort($rules, function ($a, $b) {
            return $b->getPriority() <=> $a->getPriority();
        });

        return $rules[0];
    }
}