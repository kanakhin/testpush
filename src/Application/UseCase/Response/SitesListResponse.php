<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\Response;

class SitesListResponse
{
    public function __construct(
        public readonly array $sites_list
    )
    {
    }
}