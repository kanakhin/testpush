<?php

declare(strict_types=1);

namespace Kanakhin\Push\Domain\Repository;

use Kanakhin\Push\Domain\Entity\Message;

interface MessageRepositoryInterface
{
    public function findOneById(int $id): ?Message;
    /**
     * @return Message[]
     */
    public function findAll(): array;
    public function save(Message $entity): ?int;
    public function delete(Message $entity): bool;
    public function cancelMessage(int $id): bool;
    public function getMessageToSend(): ?Message;
    public function markMessageAsSended(int $id): void;
}