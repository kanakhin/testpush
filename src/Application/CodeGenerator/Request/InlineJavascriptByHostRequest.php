<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\CodeGenerator\Request;

class InlineJavascriptByHostRequest
{
    public function __construct(
        public readonly string $hash_id,
        public readonly string $service_host
    )
    {
    }
}