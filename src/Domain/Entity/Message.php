<?php

declare(strict_types=1);

/**
 * Объект Сообщения (messages)
 */

namespace Kanakhin\Push\Domain\Entity;

class Message
{
	/** статусы рассылки */
	public const PLANNED = 0;
	public const INPROGRESS = 1;
	public const CANCEL = 100;

    private int $id;
    private \DateTimeImmutable $add_time;
    private \DateTimeImmutable $send_time;
    private string $filters;
    private string $title;
    private string $text;
    private string $link;
    private int $status;

    /**
     * @param \DateTimeImmutable $add_time
     * @param \DateTimeImmutable $send_time
     * @param string $filters
     * @param string $title
     * @param string $text
     * @param string $link
     * @param int $status
     */
    public function __construct(\DateTimeImmutable $add_time, \DateTimeImmutable $send_time, string $filters, string $title, string $text, string $link, int $status)
    {
        $this->add_time = $add_time;
        $this->send_time = $send_time;
        $this->filters = $filters;
        $this->title = $title;
        $this->text = $text;
        $this->link = $link;
        $this->status = $status;
    }

    public function getAddTime(): \DateTimeImmutable
    {
        return $this->add_time;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setAddTime(\DateTimeImmutable $add_time): void
    {
        $this->add_time = $add_time;
    }

    public function getSendTime(): \DateTimeImmutable
    {
        return $this->send_time;
    }

    public function setSendTime(\DateTimeImmutable $send_time): void
    {
        $this->send_time = $send_time;
    }

    public function getFilters(): string
    {
        return $this->filters;
    }

    public function setFilters(string $filters): void
    {
        $this->filters = $filters;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): void
    {
        $this->status = $status;
    }


}
