<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\CodeGenerator\Request;

class CodeGeneratorRequest
{
    public function __construct(
        public readonly string $public_vapid_key,
        public readonly string $host,
        public readonly string $service_host
    ) {
    }
}