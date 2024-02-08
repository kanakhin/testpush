<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase;

use Kanakhin\Push\Application\UseCase\Request\MarkMessageAsSendedRequest;
use Kanakhin\Push\Application\UseCase\Response\MarkMessageAsSendedResponse;
use Kanakhin\Push\Domain\Repository\MessageRepositoryInterface;

class MarkMessageAsSendedUseCase
{
    private MessageRepositoryInterface $messageRepository;

    /**
     * @param MessageRepositoryInterface $messageRepository
     */
    public function __construct(MessageRepositoryInterface $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    public function __invoke(MarkMessageAsSendedRequest $request): MarkMessageAsSendedResponse
    {
        $canceled = $this->messageRepository->markMessageAsSended($request->id);

        return new MarkMessageAsSendedResponse();
    }

}