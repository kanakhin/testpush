<?php

declare(strict_types=1);

namespace Kanakhin\Push\Infrastructure\Repository;

use Kanakhin\Push\Domain\Entity\Message;
use Kanakhin\Push\Domain\Repository\MessageRepositoryInterface;

class PdoMessageRepository implements MessageRepositoryInterface
{
    private \PDO $db;
    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    public function findOneById(int $id): ?Message
    {
        $query = $this->db->prepare("SELECT * FROM `messages` WHERE `id` = :id;");
        $query->execute([':id' => $id]);
        $message_data = $query->fetch();

        if (empty($message_data)) {
            return null;
        }

        $message = new Message(
            new \DateTimeImmutable($message_data['add_time']),
            new \DateTimeImmutable($message_data['send_time']),
            $message_data['filters'],
            $message_data['title'],
            $message_data['text'],
            $message_data['link'],
            $message_data['status']
        );

        $reflection_class = new \ReflectionClass($message);
        $reflection_class->getProperty('id')->setValue($message, $message_data['id']);

        return $message;
    }

    public function findAll(): array
    {
        $query = $this->db->query("SELECT * FROM `messages`;");
        $messages = $query->fetchAll();

        if (empty($messages)) {
            return [];
        }

        $result = [];
        foreach ($messages AS $message_data) {
            $message = new Message(
                new \DateTimeImmutable($message_data['add_time']),
                new \DateTimeImmutable($message_data['send_time']),
                $message_data['filters'],
                $message_data['title'],
                $message_data['text'],
                $message_data['link'],
                $message_data['status']
            );

            $reflection_class = new \ReflectionClass($message);
            $reflection_class->getProperty('id')->setValue($message, $message_data['id']);

            $result[] = $message;
        }

        return $result;
    }

    public function save(Message $entity): ?int
    {
        $query = $this->db->prepare("INSERT INTO `messages` (`add_time`, `send_time`, `filters`, `title`, `text`, `link`, `status`) 
        VALUES (NOW(), :send_time, :filters, :title, :text, :link, :status);");
        try {
            $query->execute([
                ':send_time' => $entity->getSendTime()->format("Y-m-d H:i:s"),
                ':filters' => $entity->getFilters(),
                ':title' => $entity->getTitle(),
                ':text' => $entity->getText(),
                ':link' => $entity->getLink(),
                ':status' => Message::PLANNED
            ]);
            return (int)$this->db->lastInsertId();
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function delete(Message $entity): bool
    {
        $query = $this->db->prepare("DELETE FROM `messages` WHERE `id` = :id;");
        $query->execute([':id' => $entity->getId()]);
    }

    public function cancelMessage(int $id): bool
    {
        $query = $this->db->prepare("UPDATE `messages` SET `status` = :cancelled WHERE `id` = :id AND `status` = :planned;");
        $query->execute([':id' => $id, ':cancelled' => Message::CANCEL, ':planned' => Message::PLANNED]);

        if ($query->rowCount() > 0) {
            return true;
        }

        return false;
    }

    public function getMessageToSend(): ?Message
    {
        $query = $this->db->prepare("SELECT * FROM `messages` WHERE `status` = :planned AND `send_time` <= NOW() LIMIT 1;");
        $query->execute([':planned' => Message::PLANNED]);
        $message_data = $query->fetch();

        if (empty($message_data)) {
            return null;
        }

        $message = new Message(
            new \DateTimeImmutable($message_data['add_time']),
            new \DateTimeImmutable($message_data['send_time']),
            $message_data['filters'],
            $message_data['title'],
            $message_data['text'],
            $message_data['link'],
            $message_data['status']
        );

        $reflection_class = new \ReflectionClass($message);
        $reflection_class->getProperty('id')->setValue($message, $message_data['id']);

        return $message;
    }

    public function markMessageAsSended(int $id): void
    {
        $query = $this->db->prepare("UPDATE `messages` SET `status` = :sended WHERE `id` = :id;");
        $query->execute([':id' => $id, ':sended' => Message::INPROGRESS]);
    }

}