<?php

declare(strict_types=1);

/**
 * Точка входа для http и console
 */

namespace Kanakhin\Push\Infrastructure;

use Kanakhin\Push\Infrastructure\Config;
use League\Container\Container;
use League\Route\Router;
use League\Route\Strategy\JsonStrategy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Console\Application;

class Core
{
    /**
     * @static
     * @var Container Контейнер служб
     */
    public static Container $services;

    /**
     * @static
     * @var string Абсолютный путь к корневой директории проекта
     */
    public static string $root;

    /**
     * @static
     * @var string Абсолютный путь к директории с кешем Javascript файлов
     */
    public static string $varJsCache;

    /**
     * Инициализация
     * 
     * @static
     * @return void
     */
    public static function init(): void
    {
        //Определение корневой директории
        self::$root = dirname(__DIR__) . '/..';

        //Определение директории с кешем javascript файлов
        self::$varJsCache = self::$root . '/var/js_cache';

        //Инициализация конфигурации
        $configFile = require self::$root . '/config.php';
        $config = new Config($configFile);

        //Инициализация контейнера
        self::$services = new Container();
        self::$services->defaultToShared();
        self::$services->add(Config::class, $config);
        $servicesRegister = require self::$root . '/src/Infrastructure/Config/services.php';
        $servicesRegister(self::$services);

        //Установка московской часовой зоны по умолчанию
        date_default_timezone_set('Europe/Moscow');

        //Отображение ошибок (если установлен параметр debug)
        error_reporting($config->get('debug') ? E_ALL : 0);
    }

    /**
     * Обработка http запроса и возврат ответа
     * 
     * @static
     * @return ResponseInterface
     */
    public static function Request(ServerRequestInterface $request): ResponseInterface
    {
        //Инициализация роутера
        $responseFactory = new \Laminas\Diactoros\ResponseFactory();
        $strategy = new JsonStrategy($responseFactory, JSON_UNESCAPED_UNICODE);
        $strategy->setContainer(self::$services);
        $router = new Router();
        $router->setStrategy($strategy);
        $routesRegister = require self::$root . '/src/Infrastructure/Config/routes.php';
        $routesRegister($router);
        $response = $router->dispatch($request);
        $response = $response->withHeader('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * Регистрация консольных комманд и возврат объекта Application
     * 
     * @static
     * @return Application
     */
    public static function Console(): Application
    {
        set_time_limit(0);
        $application = new Application();
        $application->setName('Сервис рассылки пуш сообщений');
        $applicationsRegister = require self::$root . '/src/Infrastructure/Config/console.php';
        $applicationsRegister($application);
        return $application;
    }
}

Core::init();
