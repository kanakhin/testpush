<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase;

use Kanakhin\Push\Application\UseCase\Request\SitesListRequest;
use Kanakhin\Push\Application\UseCase\Response\SitesListResponse;
use Kanakhin\Push\Domain\Repository\SiteRepositoryInterface;

class SitesListUseCase
{
    private SiteRepositoryInterface $siteRepository;

    /**
     * @param SiteRepositoryInterface $siteRepository
     */
    public function __construct(SiteRepositoryInterface $siteRepository)
    {
        $this->siteRepository = $siteRepository;
    }

    public function __invoke(SitesListRequest $request): SitesListResponse
    {
        $sites = $this->siteRepository->findAll();

        $response = [];
        if (!empty($sites)) {
            foreach ($sites AS $site) {
                $response[] = [
                    'id' => $site->getId(),
                    'host' => $site->getHost(),
                    'hash_id' => $site->getHashId()
                ];
            }
        }

        return new SitesListResponse($response);
    }

}