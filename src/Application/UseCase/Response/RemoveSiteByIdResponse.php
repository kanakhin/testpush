<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\Response;

class RemoveSiteByIdResponse
{
    public function __construct(
        public readonly bool $removed
    )
    {
    }
}