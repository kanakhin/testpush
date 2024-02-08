<?php

declare(strict_types=1);

/**
 * Загрузка новых сообщений рассылок в базу
 */

namespace Kanakhin\Push\Infrastructure\ApiControllers\Admin;

use Kanakhin\Push\Application\UseCase\CreateMessageUseCase;
use Kanakhin\Push\Application\UseCase\Request\CreateMessageRequest;

class MessageLoader
{
    /** @var CreateMessageUseCase */
    private $useCase;

    public function __construct(CreateMessageUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function __invoke($request)
    {
        $data = json_decode($request->getBody()->getContents());

        if (empty($data->title) || empty($data->body) || empty($data->link)) {
            return [
                'success' => false,
                'description' => 'Поля title, body, link должны быть заполнены.',
            ];
        }

        //Преобразование списка хостов в список ids
        $sites = (array) ($data->sites ?? []);

        $loader_request = new CreateMessageRequest(
            new \DateTimeImmutable(date('Y-m-d H:i:s', strtotime($data->send_time ?? 'now'))),
            $data->title,
            $data->body,
            $data->link,
            $sites,
            isset($data->subscribe_from) ? new \DateTimeImmutable(date('Y-m-d', strtotime($data->subscribe_from))) : null,
            isset($data->subscribe_to) ? new \DateTimeImmutable(date('Y-m-d', strtotime($data->subscribe_to))) : null
        );

        $message_id = ($this->useCase)($loader_request)->id;

        if (!empty($message_id)) {
            return [
                'success' => true,
                'description' => 'Сообщение сохранено для рассылки [' . $message_id . ']',
            ];
        } else {
            return [
                'success' => false,
                'description' => 'Ошибка при сохранении сообщения (на выбранное время уже существует сообщение)',
            ];
        }
    }
}
