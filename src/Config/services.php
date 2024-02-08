<?php

/**
 * Регистрация сервисов
 */

return function(\League\Container\Container $c) {

    $c->add(\PDO::class, function() use(&$c) {
        $conf = $c->get(\Kanakhin\Push\Config::class);
        $dsn = "mysql:host={$conf->get('db_host')}; port={$conf->get('db_port')}; dbname={$conf->get('db_name')}; charset=utf8";
        $pdo = new \PDO($dsn, $conf->get('db_user'), $conf->get('db_pass'));
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
        $pdo->setAttribute(\PDO::ATTR_CURSOR, \PDO::CURSOR_SCROLL);
        $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);
        return $pdo;
    });

    $c->add(\Kanakhin\Push\Infrastructure\Repository\PdoSiteRepository::class)
        ->addArgument(\PDO::class)
        ->addArgument(\Kanakhin\Push\Config::class);

    $c->add(\Kanakhin\Push\Infrastructure\Repository\PdoMessageRepository::class)
        ->addArgument(\PDO::class)
        ->addArgument(\Kanakhin\Push\Config::class);

    $c->add(\Kanakhin\Push\Infrastructure\Repository\PdoSubscriberRepository::class)
        ->addArgument(\PDO::class)
        ->addArgument(\Kanakhin\Push\Config::class);

    $c->add(\Kanakhin\Push\Infrastructure\CodeGenerator\JsCodeGenerator::class)
        ->addArgument(\PDO::class)
        ->addArgument(\Kanakhin\Push\Config::class)
        ->addArgument(\Kanakhin\Push\Infrastructure\Repository\PdoSiteRepository::class);

    $c->add(\Kanakhin\Push\Infrastructure\PushGateway\PushGateway::class);

    $c->add(\Kanakhin\Push\Application\UseCase\GetJsCodeForSiteUseCase::class)
        ->addArgument(\Kanakhin\Push\Infrastructure\Repository\PdoSiteRepository::class)
        ->addArgument(\Kanakhin\Push\Infrastructure\CodeGenerator\JsCodeGenerator::class);

    $c->add(\Kanakhin\Push\Application\UseCase\GetJsCodeForSiteUseCase::class)
        ->addArgument(\Kanakhin\Push\Infrastructure\Repository\PdoSiteRepository::class)
        ->addArgument(\Kanakhin\Push\Infrastructure\CodeGenerator\JsCodeGenerator::class);

    $c->add(\Kanakhin\Push\Application\UseCase\CreateSiteUseCase::class)
        ->addArgument(\Kanakhin\Push\Infrastructure\Repository\PdoSiteRepository::class);

    $c->add(\Kanakhin\Push\Application\UseCase\GetInlineCodeByHashIdUseCase::class)
        ->addArgument(\Kanakhin\Push\Infrastructure\Repository\PdoSiteRepository::class)
        ->addArgument(\Kanakhin\Push\Infrastructure\CodeGenerator\JsCodeGenerator::class);

    $c->add(\Kanakhin\Push\Application\UseCase\SubscribeUseCase::class)
        ->addArgument(\Kanakhin\Push\Infrastructure\Repository\PdoSiteRepository::class)
        ->addArgument(\Kanakhin\Push\Infrastructure\Repository\PdoSubscriberRepository::class);

    $c->add(\Kanakhin\Push\Application\UseCase\SitesListUseCase::class)
        ->addArgument(\Kanakhin\Push\Infrastructure\Repository\PdoSiteRepository::class);

    $c->add(\Kanakhin\Push\Application\UseCase\RemoveSiteByHostUseCase::class)
        ->addArgument(\Kanakhin\Push\Infrastructure\Repository\PdoSiteRepository::class);

    $c->add(\Kanakhin\Push\Application\UseCase\RemoveSiteByIdUseCase::class)
        ->addArgument(\Kanakhin\Push\Infrastructure\Repository\PdoSiteRepository::class);

    $c->add(\Kanakhin\Push\Application\UseCase\GetSiteInfoUseCase::class)
        ->addArgument(\Kanakhin\Push\Infrastructure\Repository\PdoSiteRepository::class)
        ->addArgument(\Kanakhin\Push\Infrastructure\CodeGenerator\JsCodeGenerator::class);

    $c->add(\Kanakhin\Push\Application\UseCase\CreateMessageUseCase::class)
        ->addArgument(\Kanakhin\Push\Infrastructure\Repository\PdoSiteRepository::class)
        ->addArgument(\Kanakhin\Push\Infrastructure\Repository\PdoMessageRepository::class);

    $c->add(\Kanakhin\Push\Application\UseCase\CancelMessageUseCase::class)
        ->addArgument(\Kanakhin\Push\Infrastructure\Repository\PdoMessageRepository::class);


    //Controllers

    $c->add(\Kanakhin\Push\Infrastructure\ApiControllers\PersonalJavascript::class)
        ->addArgument(\Kanakhin\Push\Application\UseCase\GetJsCodeForSiteUseCase::class);

    $c->add(\Kanakhin\Push\Infrastructure\ApiControllers\Subscribe::class)
        ->addArgument(\Kanakhin\Push\Application\UseCase\SubscribeUseCase::class);

    $c->add(\Kanakhin\Push\Infrastructure\ApiControllers\Admin\SiteAdd::class)
        ->addArgument(\Kanakhin\Push\Application\UseCase\CreateSiteUseCase::class)
        ->addArgument(\Kanakhin\Push\Application\UseCase\GetInlineCodeByHashIdUseCase::class);

    $c->add(\Kanakhin\Push\Infrastructure\ApiControllers\Admin\SitesList::class)
        ->addArgument(\Kanakhin\Push\Application\UseCase\SitesListUseCase::class);

    $c->add(\Kanakhin\Push\Infrastructure\ApiControllers\Admin\SiteRemove::class)
        ->addArgument(\Kanakhin\Push\Application\UseCase\RemoveSiteByIdUseCase::class);

    $c->add(\Kanakhin\Push\Infrastructure\ApiControllers\Admin\SiteInfo::class)
        ->addArgument(\Kanakhin\Push\Application\UseCase\GetSiteInfoUseCase::class);

    $c->add(\Kanakhin\Push\Infrastructure\ApiControllers\Admin\MessageLoader::class)
        ->addArgument(\Kanakhin\Push\Application\UseCase\CreateMessageUseCase::class);

    $c->add(\Kanakhin\Push\Infrastructure\ApiControllers\Admin\Cancel::class)
        ->addArgument(\Kanakhin\Push\Application\UseCase\CancelMessageUseCase::class);



};
