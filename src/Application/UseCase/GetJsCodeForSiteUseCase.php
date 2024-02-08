<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase;

use Kanakhin\Push\Application\UseCase\CodeGenerator\CodeGeneratorInterface;
use Kanakhin\Push\Application\UseCase\CodeGenerator\Request\CodeGeneratorRequest;
use Kanakhin\Push\Application\UseCase\Request\GetJsCodeForSiteRequest;
use Kanakhin\Push\Application\UseCase\Response\GetJsCodeForSiteResponse;
use Kanakhin\Push\Domain\Repository\SiteRepositoryInterface;

class GetJsCodeForSiteUseCase
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

    public function __invoke(GetJsCodeForSiteRequest $request): GetJsCodeForSiteResponse
    {
        $site = $this->siteRepository->findByHash($request->hash_id);

        if (is_null($site)) {
            return new GetJsCodeForSiteResponse("");
        }

        $js_code_request = new CodeGeneratorRequest(
            $site->getVapidKeyPublic(),
            $site->getHost(),
            $this->siteRepository->getServiceHost()
        );

        $js_code_response = $this->codeGenerator->generate($js_code_request);

        return new GetJsCodeForSiteResponse($js_code_response->js_code);
    }

}