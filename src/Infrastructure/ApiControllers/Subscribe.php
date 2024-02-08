<?php

declare(strict_types=1);

/**
 * Сохранение подписки браузера на сервере
 */

namespace Kanakhin\Push\Infrastructure\ApiControllers;

use Kanakhin\Push\Application\Subscriptions\SubscriptionManager;
use Kanakhin\Push\Application\UseCase\Request\SubscribeRequest;
use Kanakhin\Push\Application\UseCase\SubscribeUseCase;
use Kanakhin\Push\Domain\Sites\SiteManager;
use League\Route\Http\Exception\BadRequestException;

class Subscribe
{
	private SubscribeUseCase $useCase;

	public function __construct(SubscribeUseCase $useCase)
	{
		$this->useCase = $useCase;
	}

	/**
	 * Сохранение подписки в базе
	 * Подписка передаётся браузером в виде json массива
	 * POST /api/subscribe
	 * 
	 * @param RequestInterface $request
	 * @return array
	 * @throws BadRequestException
	 */
	public function __invoke($request)
	{
		$raw_request = json_decode($request->getBody()->getContents());

		$host = $raw_request->host ?? null;
		$endpoint = $raw_request->endpoint ?? null;
		$key = $raw_request->key ?? null;
		$token = $raw_request->token ?? null;
		$keyPublic = $raw_request->keyPublic ?? null;

		//Проверка наличия всех входных данных
		if (!isset($host, $endpoint, $key, $token)) {
			throw new BadRequestException();
		}

		$ip = $request->getServerParams()['REMOTE_ADDR'];
		$useragent = $request->getHeader('User-Agent')[0];

        $subscribe_request = new SubscribeRequest($host, $endpoint, $key, $token, $keyPublic, $ip, $useragent);

		//Добавление новой подписки в базу
		$is_added = ($this->useCase)($subscribe_request)->subscribed;

		return [
			'success' => $is_added,
			'description' => $is_added ? 'Subscription added successfully' : 'Subscription is already has',
		];
	}
}
