<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\Request;

class SubscribeRequest
{
    public function __construct(
        public readonly string $host,
        public readonly string $endpoint,
        public readonly string $key,
        public readonly string $token,
        public readonly string $keyPublic,
        public readonly string $ip,
        public readonly string $useragent
    )
    {
    }
}