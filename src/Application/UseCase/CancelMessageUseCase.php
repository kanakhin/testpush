<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase;

use Kanakhin\Push\Application\UseCase\Request\CancelMessageRequest;
use Kanakhin\Push\Application\UseCase\Response\CancelMessageResponse;
use Kanakhin\Push\Domain\Repository\MessageRepositoryInterface;

class CancelMessageUseCase
{
    private MessageRepositoryInterface $messageRepository;

    /**
     * @param MessageRepositoryInterface $messageRepository
     */
    public function __construct(MessageRepositoryInterface $messageRepository)
    {
        $this->messageRepository = $messageRepository;
    }

    public function __invoke(CancelMessageRequest $request): CancelMessageResponse
    {
        $canceled = $this->messageRepository->cancelMessage($request->id);

        return new CancelMessageResponse($canceled);
    }

}