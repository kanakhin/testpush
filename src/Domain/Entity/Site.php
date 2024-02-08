<?php

declare(strict_types=1);

/**
 * Класс для работы с сайтом
 */

namespace Kanakhin\Push\Domain\Entity;

class Site
{
    private int $id;
    private string $hash_id;
    private string $host;
    private string $vapid_key_public;
    private string $vapid_key_private;

    /**
     * @param string $hash_id
     * @param string $host
     * @param string $vapid_key_public
     * @param string $vapid_key_private
     */
    public function __construct(string $hash_id, string $host, string $vapid_key_public, string $vapid_key_private)
    {
        $this->hash_id = $hash_id;
        $this->host = $host;
        $this->vapid_key_public = $vapid_key_public;
        $this->vapid_key_private = $vapid_key_private;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getHashId(): string
    {
        return $this->hash_id;
    }

    public function setHashId(string $hash_id): void
    {
        $this->hash_id = $hash_id;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    public function getVapidKeyPublic(): string
    {
        return $this->vapid_key_public;
    }

    public function setVapidKeyPublic(string $vapid_key_public): void
    {
        $this->vapid_key_public = $vapid_key_public;
    }

    public function getVapidKeyPrivate(): string
    {
        return $this->vapid_key_private;
    }

    public function setVapidKeyPrivate(string $vapid_key_private): void
    {
        $this->vapid_key_private = $vapid_key_private;
    }

}
