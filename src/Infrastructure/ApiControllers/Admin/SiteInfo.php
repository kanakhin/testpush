<?php

declare(strict_types=1);

/**
 * Общая информация о сайте
 */

namespace Kanakhin\Push\Infrastructure\ApiControllers\Admin;

use Kanakhin\Push\Application\UseCase\GetSiteInfoUseCase;
use Kanakhin\Push\Application\UseCase\Request\GetSiteInfoRequest;
use League\Route\Http\Exception\BadRequestException;

class SiteInfo
{
    /** @var GetSiteInfoUseCase */
    private GetSiteInfoUseCase $useCase;

    public function __construct(GetSiteInfoUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function __invoke($request): array
    {
        $id = (int) $request->getAttribute('id');

        if (empty($id)) {
            throw new BadRequestException();
        }

        $info_request = new GetSiteInfoRequest($id);
        $site_info = ($this->useCase)($info_request);

        if (!$site_info->id) {
            return [
                'success' => false,
                'description' => 'Сайт не найден.'
            ];
        }

        return [
            'success' => true,
            //id сайта
            'id' => $site_info->id,
            //host сайта
            'host' => $site_info->host,
            'test' => 1,
            //Информация для интеграции кода на сайт
            'javascript' => $site_info->inline_code
        ];
    }
}
