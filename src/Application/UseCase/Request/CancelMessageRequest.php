<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\Request;

class CancelMessageRequest
{
    public function __construct(
        public readonly int $id
    ) {
    }
}