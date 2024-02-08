<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase;

use Kanakhin\Push\Application\UseCase\CodeGenerator\CodeGeneratorInterface;
use Kanakhin\Push\Application\UseCase\CodeGenerator\Request\InlineJavascriptByHostRequest;
use Kanakhin\Push\Application\UseCase\Request\GetInlineCodeByHostRequest;
use Kanakhin\Push\Application\UseCase\Response\GetInlineCodeByHostResponse;
use Kanakhin\Push\Domain\Repository\SiteRepositoryInterface;

class GetInlineCodeByHostUseCase
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

    public function __invoke(GetInlineCodeByHostRequest $request): GetInlineCodeByHostResponse
    {
        $site = $this->siteRepository->findByHost($request->host);
        if (is_null($site)) {
            return new GetInlineCodeByHostResponse("");
        }

        $code_generator_request = new InlineJavascriptByHostRequest($site->getHashId(), $this->siteRepository->getServiceHost());
        $js_code_response = $this->codeGenerator->getInlineJavaScriptByHost($code_generator_request);

        return new GetInlineCodeByHostResponse($js_code_response->code);
    }

}