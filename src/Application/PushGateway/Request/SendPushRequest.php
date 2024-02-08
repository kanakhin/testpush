<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\PushGateway\Request;

class SendPushRequest
{
    /**
     * @param string $public_vapid_key
     * @param string $private_vapid_key
     * @param string $title
     * @param int|null $ttl
     * @param string $body
     * @param string $url
     * @param string $endpoint
     * @param string $key
     * @param string $token
     */
    public function __construct(
        public readonly string $public_vapid_key,
        public readonly string $private_vapid_key,
        public readonly string $title,
        public readonly ?int   $ttl,
        public readonly string $body,
        public readonly string $url,
        public readonly string $endpoint,
        public readonly string $key,
        public readonly string $token
    )
    {
    }
}