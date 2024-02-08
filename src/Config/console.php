<?php

/**
 * Регистрация консольных команд
 */

use Kanakhin\Push\Application\UseCase\CreateSiteUseCase;
use Kanakhin\Push\Application\UseCase\GetInlineCodeByHashIdUseCase;
use Kanakhin\Push\Application\UseCase\GetInlineCodeByHostUseCase;
use Kanakhin\Push\Application\UseCase\GetMessageToSendUseCase;
use Kanakhin\Push\Application\UseCase\GetSubscriptionsForMessageUseCase;
use Kanakhin\Push\Application\UseCase\MarkMessageAsSendedUseCase;
use Kanakhin\Push\Application\UseCase\RemoveSiteByHostUseCase;
use Kanakhin\Push\Application\UseCase\SendPushUseCase;
use Kanakhin\Push\Core;

return function($app) {
    $siteRepository = Core::$services->get(\Kanakhin\Push\Infrastructure\Repository\PdoSiteRepository::class);
    $messageRepository = Core::$services->get(\Kanakhin\Push\Infrastructure\Repository\PdoMessageRepository::class);
    $subscriberRepository = Core::$services->get(\Kanakhin\Push\Infrastructure\Repository\PdoSubscriberRepository::class);

    $codeGenerator = Core::$services->get(\Kanakhin\Push\Infrastructure\CodeGenerator\JsCodeGenerator::class);

    $createSiteUseCase = new CreateSiteUseCase($siteRepository);
    $javascriptByHashIdUseCase = new GetInlineCodeByHashIdUseCase($siteRepository, $codeGenerator);
    $javascriptByHostUseCase = new GetInlineCodeByHostUseCase($siteRepository, $codeGenerator);
    $removeSiteUseCase = new RemoveSiteByHostUseCase($siteRepository);

    $getMessageToSendUseCase = new GetMessageToSendUseCase($messageRepository);
    $markMessageAsSendedUseCase = new MarkMessageAsSendedUseCase($messageRepository);
    $getSubscriptionsUseCase = new GetSubscriptionsForMessageUseCase($subscriberRepository, $messageRepository);

    $pushGateway = Core::$services->get(\Kanakhin\Push\Infrastructure\PushGateway\PushGateway::class);
    $sendPushUseCase = new SendPushUseCase($pushGateway);

    $app->add(new \Kanakhin\Push\Infrastructure\Console\Cron(null, $getMessageToSendUseCase, $markMessageAsSendedUseCase, $getSubscriptionsUseCase, $sendPushUseCase));
    $app->add(new \Kanakhin\Push\Infrastructure\Console\SiteAdd(null, $createSiteUseCase, $javascriptByHashIdUseCase));
    $app->add(new \Kanakhin\Push\Infrastructure\Console\SiteRemove(null, $removeSiteUseCase));
    $app->add(new \Kanakhin\Push\Infrastructure\Console\SiteCode(null, $javascriptByHostUseCase));
};
