<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\Response;

class GetJsCodeForSiteResponse
{
    public function __construct(
        public readonly string $js_code
    ) {
    }
}