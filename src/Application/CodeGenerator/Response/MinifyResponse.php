<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\CodeGenerator\Response;

class MinifyResponse
{
    public function __construct(
        public readonly string $code
    )
    {
    }
}