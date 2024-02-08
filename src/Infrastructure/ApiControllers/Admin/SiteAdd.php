<?php

declare(strict_types=1);

/**
 * Добавление нового сайта
 */

namespace Kanakhin\Push\Infrastructure\ApiControllers\Admin;

use Kanakhin\Push\Application\UseCase\CreateSiteUseCase;
use Kanakhin\Push\Application\UseCase\GetInlineCodeByHashIdUseCase;
use Kanakhin\Push\Application\UseCase\Request\CreateSiteRequest;
use Kanakhin\Push\Application\UseCase\Request\GetInlineCodeByHashIdRequest;
use League\Route\Http\Exception\BadRequestException;
use Minishlink\WebPush\VAPID;

class SiteAdd
{
    private CreateSiteUseCase $createSiteUseCase;
    private GetInlineCodeByHashIdUseCase $javascriptUseCase;

    public function __construct(CreateSiteUseCase $createSiteUseCase, GetInlineCodeByHashIdUseCase $javascriptUseCase)
    {
        $this->createSiteUseCase = $createSiteUseCase;
        $this->javascriptUseCase = $javascriptUseCase;
    }

    public function __invoke($request): ?array
    {
        $data = json_decode($request->getBody()->getContents(), true);

        if (empty($data['site'])) {
            throw new BadRequestException();
        }

        $host = parse_url($data['site'])['host'] ?? $data['site'];
        $vapid_keys = VAPID::createVapidKeys(); //Генерация новых VAPID ключей
        $hash_id = bin2hex(random_bytes(16)); //Генерация нового hash_id

        $request = new CreateSiteRequest($hash_id, $host, $vapid_keys['publicKey'], $vapid_keys['privateKey']);
        $id = ($this->createSiteUseCase)($request)->id;

        if (!is_null($id)) {
            $request = new GetInlineCodeByHashIdRequest($hash_id);
            $js_code = ($this->javascriptUseCase)($request)->inline_code;

            return [
                'success' => true,
                'js_code' => $js_code,
                'id' => $id
            ];
        } else {
            return [
                'success' => false
            ];
        }
    }
}
