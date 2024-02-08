<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\Request;

class GetSiteInfoRequest
{
    public function __construct(
        public readonly int $id
    ) {
    }
}