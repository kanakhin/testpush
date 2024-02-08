<?php

declare(strict_types=1);

/**
 * Изменение настроек для сайта
 */

namespace Kanakhin\Push\Infrastructure\ApiControllers\Admin;

use Kanakhin\Push\Application\UseCase\RemoveSiteByIdUseCase;
use Kanakhin\Push\Application\UseCase\Request\RemoveSiteByIdRequest;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\NotFoundException;

class SiteRemove
{
    /** @var RemoveSiteByIdUseCase */
    private RemoveSiteByIdUseCase $useCase;

    public function __construct(RemoveSiteByIdUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * Загрузка новых настроек для сайта
     */
    public function __invoke($request): ?array
    {
        $id = (int) $request->getAttribute('id');

        if (empty($id)) {
            throw new BadRequestException();
        }

        $remove_request = new RemoveSiteByIdRequest($id);
        $removed = ($this->useCase)($remove_request)->removed;

        if (!$removed) {
            throw new NotFoundException();
        }

        return [
            'success' => true,
            'description' => 'Сайт удален.',
        ];
    }
}
