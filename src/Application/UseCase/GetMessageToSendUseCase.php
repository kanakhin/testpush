<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase;

use Kanakhin\Push\Application\UseCase\Request\GetMessageToSendRequest;
use Kanakhin\Push\Application\UseCase\Response\GetMessageToSendResponse;
use Kanakhin\Push\Domain\Repository\MessageRepositoryInterface;

class GetMessageToSendUseCase
{
    private MessageRepositoryInterface $messageRepository;

    /**
     * @param MessageRepositoryInterface $messageRepository
     */
    public function __construct(MessageRepositoryInterface $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    public function __invoke(GetMessageToSendRequest $request): GetMessageToSendResponse
    {
        $message = $this->messageRepository->getMessageToSend();

        if (is_null($message)) {
            return new GetMessageToSendResponse(null);
        }

        return new GetMessageToSendResponse($message->getId());
    }
}