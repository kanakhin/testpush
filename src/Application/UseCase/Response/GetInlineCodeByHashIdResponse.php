<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\Response;

class GetInlineCodeByHashIdResponse
{
    public function __construct(
        public readonly string $inline_code
    ) {
    }
}