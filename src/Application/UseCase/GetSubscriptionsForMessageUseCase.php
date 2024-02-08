<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase;

use Kanakhin\Push\Application\UseCase\Request\GetSubscriptionsToMessageRequest;
use Kanakhin\Push\Application\UseCase\Response\GetSubscriptionsToMessageResponse;
use Kanakhin\Push\Domain\Repository\MessageRepositoryInterface;
use Kanakhin\Push\Domain\Repository\SubscriberRepositoryInterface;

class GetSubscriptionsForMessageUseCase
{
    private MessageRepositoryInterface $messageRepository;
    private SubscriberRepositoryInterface $subscriberRepository;

    /**
     * @param SubscriberRepositoryInterface $subscriberRepository
     * @param MessageRepositoryInterface $messageRepository
     */
    public function __construct(SubscriberRepositoryInterface $subscriberRepository, MessageRepositoryInterface $messageRepository)
    {
        $this->subscriberRepository = $subscriberRepository;
        $this->messageRepository = $messageRepository;
    }

    public function __invoke(GetSubscriptionsToMessageRequest $request): GetSubscriptionsToMessageResponse
    {
        $subscriptions = [];

        $message = $this->messageRepository->findOneById($request->message_id);

        if (!is_null($message)) {
            $filters = json_decode($message->getFilters(), true);
            $subscriptions = $this->subscriberRepository->findSubscribers($filters);
            foreach ($subscriptions AS &$subscription) {
                $subscription['title'] = $message->getTitle();
                $subscription['text'] = $message->getText();
                $subscription['link'] = $message->getLink();
            }
        }

        return new GetSubscriptionsToMessageResponse($subscriptions);
    }

}