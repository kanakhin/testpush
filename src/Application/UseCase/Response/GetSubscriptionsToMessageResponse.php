<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\Response;

use Kanakhin\Push\Domain\Entity\Subscription;

class GetSubscriptionsToMessageResponse
{
    /**
     * @param Subscription[] $subscriptions
     */
    public function __construct(
        public readonly array $subscriptions
    )
    {
    }
}