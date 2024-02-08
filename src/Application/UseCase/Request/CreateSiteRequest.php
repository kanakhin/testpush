<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\Request;

class CreateSiteRequest
{
    public function __construct(
        public readonly string $hash_id,
        public readonly string $host,
        public readonly string $vapid_key_public,
        public readonly string $vapid_key_private
    ) {
    }
}