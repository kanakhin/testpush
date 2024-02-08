<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\Request;

class RemoveSiteByHostRequest
{
    public function __construct(
        public readonly string $host
    ) {
    }
}