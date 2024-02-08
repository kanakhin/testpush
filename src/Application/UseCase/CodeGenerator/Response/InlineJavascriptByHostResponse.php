<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\CodeGenerator\Response;

class InlineJavascriptByHostResponse
{
    public function __construct(
        public readonly string $code
    )
    {
    }
}