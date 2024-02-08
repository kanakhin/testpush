<?php

declare(strict_types=1);

namespace Kanakhin\Push\Domain\Repository;

use Kanakhin\Push\Domain\Entity\Site;

interface SiteRepositoryInterface
{
    public function findOneById(int $id): ?Site;
    /**
     * @return Site[]
     */
    public function findAll(): array;
    public function findByHash(string $hash): ?Site;
    public function findByHost(string $host): ?Site;
    public function findByKeyPublic(string $public_key): ?Site;
    public function save(Site $entity): ?int;
    public function delete(Site $entity): void;
    public function getUrl(Site $site): string;
    public function getServiceHost(bool $with_protocol = false): string;
    public function checkIntegration(Site $site): bool;
}