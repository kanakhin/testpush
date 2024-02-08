<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\Response;

class CreateMessageResponse
{
    public function __construct(
        public readonly ?int $id
    ) {
    }
}