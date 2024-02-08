<?php

declare(strict_types=1);

namespace Kanakhin\Push\Domain\Repository;

use Kanakhin\Push\Domain\Entity\Site;

interface SubscriberRepositoryInterface
{
    public function findSubscribers(array $filters): ?array;
    public function subscribe(Site $site, string $endpoint, string $key, string $token, string $ip='', string $useragent=''): bool;
}