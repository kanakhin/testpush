<?php

declare(strict_types=1);

/**
 * Получение уникального javascript кода для сайта
 */

namespace Kanakhin\Push\Infrastructure\ApiControllers;

use Kanakhin\Push\Application\UseCase\CodeGenerator\Request\CodeGeneratorRequest;
use Kanakhin\Push\Application\UseCase\GetJsCodeForSiteUseCase;
use Kanakhin\Push\Application\UseCase\Request\GetJsCodeForSiteRequest;
use Kanakhin\Push\Infrastructure\Core;
use Kanakhin\Push\Infrastructure\Repository\PdoSiteRepository;
use Laminas\Diactoros\Response\HtmlResponse;

class PersonalJavascript
{
    /** @var PdoSiteRepository */
    private GetJsCodeForSiteUseCase $useCase;

    public function __construct(GetJsCodeForSiteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * Получение уникального javascript кода для сайта по хешу
     * GET /js/push/{xxx}.js
     * 
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function __invoke($request)
    {
        $js_request = new GetJsCodeForSiteRequest($request->getAttribute('hash'));
        $code = ($this->useCase)($js_request)->js_code;

        //Возврат 404, если такого сайта не найдено
        if (is_null($code) || empty($code)) {
            return new HtmlResponse('404 Not Found', 404); 
        }

        //Возврат уникального javascript кода
        return new HtmlResponse($code, 200, [
            'Content-Type' => 'application/javascript; charset=utf-8',
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
