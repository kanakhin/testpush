<?php

declare(strict_types=1);

/*
 * Вывод списка всех сайтов
 */

namespace Kanakhin\Push\Infrastructure\ApiControllers\Admin;

use Kanakhin\Push\Application\UseCase\Request\SitesListRequest;
use Kanakhin\Push\Application\UseCase\SitesListUseCase;

class SitesList
{
    /** @var SitesListUseCase */
    private SitesListUseCase $useCase;

    public function __construct(SitesListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function __invoke($request): array
    {
        $list_request = new SitesListRequest();

        return ($this->useCase)($list_request)->sites_list;
    }
}
