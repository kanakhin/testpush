<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\CodeGenerator\Request;

class MinifyRequest
{
    public function __construct(
        public readonly string $code
    )
    {
    }
}