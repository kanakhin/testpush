<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase;

use Kanakhin\Push\Application\UseCase\CodeGenerator\CodeGeneratorInterface;
use Kanakhin\Push\Application\UseCase\CodeGenerator\Request\InlineJavascriptByHashIdRequest;
use Kanakhin\Push\Application\UseCase\Request\GetInlineCodeByHashIdRequest;
use Kanakhin\Push\Application\UseCase\Response\GetInlineCodeByHashIdResponse;
use Kanakhin\Push\Domain\Repository\SiteRepositoryInterface;

class GetInlineCodeByHashIdUseCase
{
    private CodeGeneratorInterface $codeGenerator;
    private SiteRepositoryInterface $siteRepository;

    /**
     * @param SiteRepositoryInterface $siteRepository
     */
    public function __construct(SiteRepositoryInterface $siteRepository, CodeGeneratorInterface $codeGenerator)
    {
        $this->siteRepository = $siteRepository;
        $this->codeGenerator = $codeGenerator;
    }

    public function __invoke(GetInlineCodeByHashIdRequest $request): GetInlineCodeByHashIdResponse
    {
        $code_generator_request = new InlineJavascriptByHashIdRequest($request->hash_id, $this->siteRepository->getServiceHost());
        $js_code_response = $this->codeGenerator->getInlineJavaScriptByHashId($code_generator_request);

        return new GetInlineCodeByHashIdResponse($js_code_response->code);
    }

}