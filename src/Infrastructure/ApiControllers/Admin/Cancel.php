<?php

declare(strict_types=1);

/**
 * Отмена еще не отправленных сообщений по id
 */

namespace Kanakhin\Push\Infrastructure\ApiControllers\Admin;

use Kanakhin\Push\Application\UseCase\CancelMessageUseCase;
use Kanakhin\Push\Application\UseCase\Request\CancelMessageRequest;
use Kanakhin\Push\Domain\Messages\Message;
use Kanakhin\Push\Domain\Messages\MessageManager;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\NotFoundException;

class Cancel
{
    /** @var CancelMessageUseCase */
    private $useCase;

    public function __construct(CancelMessageUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function __invoke($request)
    {
        $id = (int) $request->getAttribute('id');

        if (empty($id)) {
            throw new BadRequestException();
        }

        $cancel_request = new CancelMessageRequest($id);
        $cancelled = ($this->useCase)($cancel_request)->canceled;
        if ($cancelled) {
            return [
                'success' => true,
                'description' => 'Отправка отменена.',
            ];
        }

        return [
            'success' => false,
            'description' => 'Отправка не может быть отменена.',
        ];
    }
}
