<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\CodeGenerator\Response;

class InlineJavascriptByHashIdResponse
{
    public function __construct(
        public readonly string $code
    )
    {
    }
}