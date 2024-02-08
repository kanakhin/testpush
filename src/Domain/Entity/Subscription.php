<?php

declare(strict_types=1);

namespace Kanakhin\Push\Domain\Entity;

use Kanakhin\Push\Domain\Entity;

class Subscription
{
    private Site $site;
    private int $id;
    private \DateTimeImmutable $tstamp;
    private string $endpoint_id;
    private string $endpoint;
    private string $key;
    private string $token;
    private string $ip;
    private string $useragent;
    private \DateTimeImmutable $last_activity;
    private \DateTimeImmutable $last_delivery;

    public function getSite(): Entity\Site
    {
        return $this->site;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setSite(Entity\Site $site): void
    {
        $this->site = $site;
    }

    public function getTstamp(): \DateTimeImmutable
    {
        return $this->tstamp;
    }

    public function setTstamp(\DateTimeImmutable $tstamp): void
    {
        $this->tstamp = $tstamp;
    }

    public function getEndpointId(): string
    {
        return $this->endpoint_id;
    }

    public function setEndpointId(string $endpoint_id): void
    {
        $this->endpoint_id = $endpoint_id;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function setEndpoint(string $endpoint): void
    {
        $this->endpoint = $endpoint;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    public function getUseragent(): string
    {
        return $this->useragent;
    }

    public function setUseragent(string $useragent): void
    {
        $this->useragent = $useragent;
    }

    public function getLastActivity(): \DateTimeImmutable
    {
        return $this->last_activity;
    }

    public function setLastActivity(\DateTimeImmutable $last_activity): void
    {
        $this->last_activity = $last_activity;
    }

    public function getLastDelivery(): \DateTimeImmutable
    {
        return $this->last_delivery;
    }

    public function setLastDelivery(\DateTimeImmutable $last_delivery): void
    {
        $this->last_delivery = $last_delivery;
    }


}