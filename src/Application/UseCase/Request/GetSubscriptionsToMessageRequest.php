<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\Request;

class GetSubscriptionsToMessageRequest
{
    public function __construct(
        public readonly int $message_id
    )
    {
    }
}