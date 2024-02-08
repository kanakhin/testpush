<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase;

use Kanakhin\Push\Application\UseCase\Request\RemoveSiteByIdRequest;
use Kanakhin\Push\Application\UseCase\Response\RemoveSiteByIdResponse;
use Kanakhin\Push\Domain\Repository\SiteRepositoryInterface;

class RemoveSiteByIdUseCase
{
    private SiteRepositoryInterface $siteRepository;

    /**
     * @param SiteRepositoryInterface $siteRepository
     */
    public function __construct(SiteRepositoryInterface $siteRepository)
    {
        $this->siteRepository = $siteRepository;
    }

    public function __invoke(RemoveSiteByIdRequest $request): RemoveSiteByIdResponse
    {
        $site = $this->siteRepository->findOneById($request->id);

        if (is_null($site)) {
            return new RemoveSiteByIdResponse(false);
        }

        $this->siteRepository->delete($site);
        return new RemoveSiteByIdResponse(true);
    }

}