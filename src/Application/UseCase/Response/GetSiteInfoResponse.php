<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\Response;

class GetSiteInfoResponse
{
    public function __construct(
        public readonly int $id,
        public readonly string $host,
        public readonly string $inline_code
    ) {
    }
}