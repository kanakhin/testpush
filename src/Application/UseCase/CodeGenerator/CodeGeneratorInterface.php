<?php

namespace Kanakhin\Push\Application\UseCase\CodeGenerator;

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

interface CodeGeneratorInterface
{
    public function generate(CodeGeneratorRequest $request): CodeGeneratorResponse;
    public function getInlineJavaScriptByHashId(InlineJavascriptByHashIdRequest $request): InlineJavascriptByHashIdResponse;
    public function getInlineJavaScriptByHost(InlineJavascriptByHostRequest $request): InlineJavascriptByHostResponse;
    public function minify(MinifyRequest $request): MinifyResponse;
    public function saveToCache(JavascriptCacheRequest $request): JavascriptCacheResponse;
}