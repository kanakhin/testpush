<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\Request;

class MarkMessageAsSendedRequest
{
    public function __construct(
        public readonly int $id
    ) {
    }
}