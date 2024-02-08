<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\CodeGenerator\Response;

class CodeGeneratorResponse
{
    public function __construct(
        public readonly string $js_code
    ) {
    }
}