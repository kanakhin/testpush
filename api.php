<?php

/**
 * Точка входа для api запросов
 */

require 'vendor/autoload.php';

$request = \Laminas\Diactoros\ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
$sapiEmitter = new \Laminas\HttpHandlerRunner\Emitter\SapiEmitter();
$response = \Kanakhin\Push\Infrastructure\Core::request($request);
$sapiEmitter->emit($response);
