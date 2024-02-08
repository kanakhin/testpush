<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase;

use Kanakhin\Push\Application\UseCase\CodeGenerator\CodeGeneratorInterface;
use Kanakhin\Push\Application\UseCase\CodeGenerator\Request\InlineJavascriptByHashIdRequest;
use Kanakhin\Push\Application\UseCase\Request\GetSiteInfoRequest;
use Kanakhin\Push\Application\UseCase\Response\GetSiteInfoResponse;
use Kanakhin\Push\Domain\Repository\SiteRepositoryInterface;

class GetSiteInfoUseCase
{
    private SiteRepositoryInterface $siteRepository;
    private CodeGeneratorInterface $codeGenerator;

    /**
     * @param SiteRepositoryInterface $siteRepository
     */
    public function __construct(SiteRepositoryInterface $siteRepository, CodeGeneratorInterface $codeGenerator)
    {
        $this->siteRepository = $siteRepository;
        $this->codeGenerator = $codeGenerator;
    }

    public function __invoke(GetSiteInfoRequest $request): ?GetSiteInfoResponse
    {
        $site = $this->siteRepository->findOneById($request->id);

        if (is_null($site)) {
            return null;
        }

        $code_generator_request = new InlineJavascriptByHashIdRequest($site->getHashId(), $this->siteRepository->getServiceHost());
        $js_code_response = $this->codeGenerator->getInlineJavaScriptByHashId($code_generator_request);

        return new GetSiteInfoResponse($site->getId(), $site->getHost(), $js_code_response->code);
    }

}