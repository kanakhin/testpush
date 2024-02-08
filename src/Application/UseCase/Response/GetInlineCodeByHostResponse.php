<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\Response;

class GetInlineCodeByHostResponse
{
    public function __construct(
        public readonly string $inline_code
    ) {
    }
}