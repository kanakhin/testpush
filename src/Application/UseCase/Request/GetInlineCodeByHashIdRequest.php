<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\Request;

class GetInlineCodeByHashIdRequest
{
    public function __construct(
        public readonly string $hash_id
    ) {
    }
}