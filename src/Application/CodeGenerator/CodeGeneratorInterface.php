<?php

namespace Kanakhin\Push\Application\CodeGenerator;

use Kanakhin\Push\Application\CodeGenerator\Request\CodeGeneratorRequest;
use Kanakhin\Push\Application\CodeGenerator\Request\InlineJavascriptByHashIdRequest;
use Kanakhin\Push\Application\CodeGenerator\Request\InlineJavascriptByHostRequest;
use Kanakhin\Push\Application\CodeGenerator\Request\JavascriptCacheRequest;
use Kanakhin\Push\Application\CodeGenerator\Request\MinifyRequest;
use Kanakhin\Push\Application\CodeGenerator\Response\CodeGeneratorResponse;
use Kanakhin\Push\Application\CodeGenerator\Response\InlineJavascriptByHashIdResponse;
use Kanakhin\Push\Application\CodeGenerator\Response\InlineJavascriptByHostResponse;
use Kanakhin\Push\Application\CodeGenerator\Response\JavascriptCacheResponse;
use Kanakhin\Push\Application\CodeGenerator\Response\MinifyResponse;

interface CodeGeneratorInterface
{
    public function generate(CodeGeneratorRequest $request): CodeGeneratorResponse;
    public function getInlineJavaScriptByHashId(InlineJavascriptByHashIdRequest $request): InlineJavascriptByHashIdResponse;
    public function getInlineJavaScriptByHost(InlineJavascriptByHostRequest $request): InlineJavascriptByHostResponse;
    public function minify(MinifyRequest $request): MinifyResponse;
    public function saveToCache(JavascriptCacheRequest $request): JavascriptCacheResponse;
}