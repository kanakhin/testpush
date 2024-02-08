<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase;

use Kanakhin\Push\Application\UseCase\Request\SubscribeRequest;
use Kanakhin\Push\Application\UseCase\Response\SubscribeResponse;
use Kanakhin\Push\Domain\Repository\SiteRepositoryInterface;
use Kanakhin\Push\Domain\Repository\SubscriberRepositoryInterface;

class SubscribeUseCase
{
    private SiteRepositoryInterface $siteRepository;
    private SubscriberRepositoryInterface $subscriberRepository;

    /**
     * @param SiteRepositoryInterface $siteRepository
     * @param SubscriberRepositoryInterface $subscriberRepository
     */
    public function __construct(SiteRepositoryInterface $siteRepository, SubscriberRepositoryInterface $subscriberRepository)
    {
        $this->siteRepository = $siteRepository;
        $this->subscriberRepository = $subscriberRepository;
    }

    public function __invoke(SubscribeRequest $request): SubscribeResponse
    {
        if (!empty($request->keyPublic)) {
            $site = $this->siteRepository->findByKeyPublic($request->keyPublic);
        } else {
            $site = $this->siteRepository->findByHost($request->host);
        }

        if (is_null($site)) {
            return new SubscribeResponse(false);
        }

        $state = $this->subscriberRepository->subscribe(
            $site,
            $request->endpoint,
            $request->key,
            $request->token,
            $request->ip,
            $request->useragent
        );

        return new SubscribeResponse($state);
    }

}