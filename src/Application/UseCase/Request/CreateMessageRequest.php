<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase\Request;

class CreateMessageRequest
{
    public function __construct(
        public readonly \DateTimeImmutable $send_time,
        public readonly string $title,
        public readonly string $text,
        public readonly string $link,
        public readonly array $sites,
        public readonly \DateTimeImmutable $subscribe_from,
        public readonly \DateTimeImmutable $subscribe_to
    ) {
    }
}