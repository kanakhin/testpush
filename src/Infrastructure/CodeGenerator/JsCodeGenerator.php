<?php

declare(strict_types=1);

namespace Kanakhin\Push\Infrastructure\CodeGenerator;

use Kanakhin\Push\Application\UseCase\CodeGenerator\CodeGeneratorInterface;
use Kanakhin\Push\Application\UseCase\CodeGenerator\Request\CodeGeneratorRequest;
use Kanakhin\Push\Application\UseCase\CodeGenerator\Request\InlineJavascriptByHashIdRequest;
use Kanakhin\Push\Application\UseCase\CodeGenerator\Request\InlineJavascriptByHostRequest;
use Kanakhin\Push\Application\UseCase\CodeGenerator\Request\JavascriptCacheRequest;
use Kanakhin\Push\Application\UseCase\CodeGenerator\Request\MinifyRequest;
use Kanakhin\Push\Application\UseCase\CodeGenerator\Response\CodeGeneratorResponse;
use Kanakhin\Push\Application\UseCase\CodeGenerator\Response\InlineJavascriptByHashIdResponse;
use Kanakhin\Push\Application\UseCase\CodeGenerator\Response\InlineJavascriptByHostResponse;
use Kanakhin\Push\Application\UseCase\CodeGenerator\Response\JavascriptCacheResponse;
use Kanakhin\Push\Application\UseCase\CodeGenerator\Response\MinifyResponse;
use Kanakhin\Push\Domain\Repository\SiteRepositoryInterface;
use Kanakhin\Push\Infrastructure\Config;
use Kanakhin\Push\Infrastructure\Core;

class JsCodeGenerator implements CodeGeneratorInterface
{
    private \PDO $db;
    private Config $config;
    private SiteRepositoryInterface $siteRepository;
    public function __construct(\PDO $db, Config $config, SiteRepositoryInterface $siteRepository)
    {
        $this->db = $db;
        $this->config = $config;
        $this->siteRepository = $siteRepository;
    }

    public function generate(CodeGeneratorRequest $request): CodeGeneratorResponse
    {
        //Чтение базового javascript файла
        $basic_js_file = Core::$root . '/public/js/basic_subscribe.js';
        $raw_js = file_get_contents($basic_js_file);

        //Карта замены плейсхолдеров в базовом javascript коде
        $replace_map = [
            'public_vapid_key' => $request->public_vapid_key,
            'host' => trim('https://' . idn_to_ascii($request->host)),
            'service_host' => 'https://' . $this->config->get('service_host')
        ];

        $site = $this->siteRepository->findByHost($request->host);

        if (is_null($site)) {
            new CodeGeneratorResponse("");
        }

        $placeholders = array_map(fn($p) => "PLACEHOLDER($p)", array_keys($replace_map));

        //Замена плейсхолдеров на уникальные значения
        $code = str_replace($placeholders, $replace_map, $raw_js);

        //Минификация javascript файла и сохранение файла в кеше
        if (!$this->config->get('debug')) {
            $code = $this->minify(new MinifyRequest($code))->code;
            $this->saveToCache(new JavascriptCacheRequest($site->getHashId(), $code));
        }

        return new CodeGeneratorResponse($code);
    }

    public function getInlineJavaScriptByHashId(InlineJavascriptByHashIdRequest $request): InlineJavascriptByHashIdResponse
    {
        $code = '<script src="https://%s/js/push/%s.js" async></script>';
        return new InlineJavascriptByHashIdResponse(sprintf($code, $request->service_host, $request->hash_id));
    }

    public function getInlineJavaScriptByHost(InlineJavascriptByHostRequest $request): InlineJavascriptByHostResponse
    {
        $code = '<script src="https://%s/js/push/%s.js" async></script>';
        return new InlineJavascriptByHostResponse(sprintf($code, $request->service_host, $request->hash_id));
    }

    public function minify(MinifyRequest $request): MinifyResponse
    {
        // remove comments
        $code = preg_replace('/(?:(?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:(?<!\:)\/\/.*))/', '', $request->code);

        // remove tabs and newlines.
        $code = str_replace(["\n", "\r", "\t"], '', $code);

        // remove unnecessary spaces around operators that don't need any spaces (specifically newlines)
        $code = preg_replace("/\s?([=:\-+])\s?/", '$1', $code);

        return new MinifyResponse($code);
    }

    public function saveToCache(JavascriptCacheRequest $request): JavascriptCacheResponse
    {
        $cached_js_file = Core::$varJsCache . '/' . $request->hash_id . '.js';
        file_put_contents($cached_js_file, $request->code);

        return new JavascriptCacheResponse();
    }


}