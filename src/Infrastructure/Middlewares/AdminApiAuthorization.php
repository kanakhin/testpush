<?php

declare(strict_types=1);

/**
 * Middleware для ограничения доступа к админским api по ip адресу
 */

namespace Kanakhin\Push\Infrastructure\Middlewares;

use Kanakhin\Push\Infrastructure\Core;
use League\Route\Http\Exception\ForbiddenException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AdminApiAuthorization implements MiddlewareInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        $config = Core::$services->get(\Kanakhin\Push\Infrastructure\Config::Class);
        $admin_api_restriction = $config->get('admin_api_blocking');
        $admin_api_whitelist = $config->get('admin_api_whitelist', []);
        $ip = $request->getServerParams()['REMOTE_ADDR'];

        if ($admin_api_restriction && !in_array($ip, $admin_api_whitelist)) {
            throw new ForbiddenException();
        }

        return $handler->handle($request);
    }
}
