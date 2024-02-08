<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\Response;

class CreateSiteResponse
{
    public function __construct(
        public readonly ?int $id
    ) {
    }
}