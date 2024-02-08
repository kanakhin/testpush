<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\PushGateway\Response;

class SendPushResponse
{
    public function __construct(
        public readonly string $response
    )
    {
    }
}