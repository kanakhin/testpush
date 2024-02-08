<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase;

use Kanakhin\Push\Application\UseCase\Request\RemoveSiteByHostRequest;
use Kanakhin\Push\Application\UseCase\Response\RemoveSiteByHostResponse;
use Kanakhin\Push\Domain\Repository\SiteRepositoryInterface;

class RemoveSiteByHostUseCase
{
    private SiteRepositoryInterface $siteRepository;

    /**
     * @param SiteRepositoryInterface $siteRepository
     */
    public function __construct(SiteRepositoryInterface $siteRepository)
    {
        $this->siteRepository = $siteRepository;
    }

    public function __invoke(RemoveSiteByHostRequest $request): RemoveSiteByHostResponse
    {
        $site = $this->siteRepository->findByHost($request->host);

        if (is_null($site)) {
            return new RemoveSiteByHostResponse(false);
        }

        $this->siteRepository->delete($site);
        return new RemoveSiteByHostResponse(true);
    }

}