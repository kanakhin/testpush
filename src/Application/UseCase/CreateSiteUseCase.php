<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase;

use Kanakhin\Push\Application\UseCase\Request\CreateSiteRequest;
use Kanakhin\Push\Application\UseCase\Response\CreateSiteResponse;
use Kanakhin\Push\Domain\Entity\Site;
use Kanakhin\Push\Domain\Repository\SiteRepositoryInterface;

class CreateSiteUseCase
{
    private SiteRepositoryInterface $siteRepository;

    /**
     * @param SiteRepositoryInterface $siteRepository
     */
    public function __construct(SiteRepositoryInterface $siteRepository)
    {
        $this->siteRepository = $siteRepository;
    }

    public function __invoke(CreateSiteRequest $request): CreateSiteResponse
    {
        $site = $this->siteRepository->findByHost($request->host);

        if (is_null($site)) {
            $site = new Site(
                $request->hash_id,
                $request->host,
                $request->vapid_key_public,
                $request->vapid_key_private
            );

            return new CreateSiteResponse($this->siteRepository->save($site));
        } else {
            return new CreateSiteResponse(null);
        }
    }

}