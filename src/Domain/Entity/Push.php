<?php

declare(strict_types=1);

namespace Kanakhin\Push\Domain\Entity;

class Push
{
    private Site $site;
    private Subscription $subscription;
    private Message $message;
    private int $id;
    private \DateTimeImmutable $tstamp;
    private \DateTimeImmutable $update_tstamp;
    private int $status;
    private bool $processed;

    public function getSite(): Site
    {
        return $this->site;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setSite(Site $site): void
    {
        $this->site = $site;
    }

    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    public function setSubscription(Subscription $subscription): void
    {
        $this->subscription = $subscription;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

    public function setMessage(Message $message): void
    {
        $this->message = $message;
    }

    public function getTstamp(): \DateTimeImmutable
    {
        return $this->tstamp;
    }

    public function setTstamp(\DateTimeImmutable $tstamp): void
    {
        $this->tstamp = $tstamp;
    }

    public function getUpdateTstamp(): \DateTimeImmutable
    {
        return $this->update_tstamp;
    }

    public function setUpdateTstamp(\DateTimeImmutable $update_tstamp): void
    {
        $this->update_tstamp = $update_tstamp;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    public function isProcessed(): bool
    {
        return $this->processed;
    }

    public function setProcessed(bool $processed): void
    {
        $this->processed = $processed;
    }


}