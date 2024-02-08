<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\Response;

class RemoveSiteByHostResponse
{
    public function __construct(
        public readonly bool $removed
    )
    {
    }
}