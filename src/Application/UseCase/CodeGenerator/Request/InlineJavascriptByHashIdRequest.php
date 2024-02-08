<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\CodeGenerator\Request;

class InlineJavascriptByHashIdRequest
{
    public function __construct(
        public readonly string $hash_id,
        public readonly string $service_host
    )
    {
    }
}