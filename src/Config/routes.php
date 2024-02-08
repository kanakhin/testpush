<?php

/**
 * Регистрация роутинга для api
 */

return function(\League\Route\Router $r) {

	//Получение уникального javascript кода по хешу
	$r->map('GET', '/js/push/{hash:[0-9a-f]+}.js', 'Kanakhin\Push\Infrastructure\ApiControllers\PersonalJavascript');

	//Отправка подписки на сервер
	$r->map('POST', '/api/subscribe', 'Kanakhin\Push\Infrastructure\ApiControllers\Subscribe');

	//Административные API методы
	$r->group('/api/admin', function(\League\Route\RouteGroup $r) {

		//Загрузка новой рассылки
		$r->map('POST', '/message/load[/]', 'Kanakhin\Push\Infrastructure\ApiControllers\Admin\MessageLoader');

		//Отменить рассылку по id
		$r->map('GET', '/message/cancel/{id:[0-9]+}[/]', 'Kanakhin\Push\Infrastructure\ApiControllers\Admin\Cancel');

		//Получить список зарегистрированных сайтов
		$r->map('GET', '/sites[/]', 'Kanakhin\Push\Infrastructure\ApiControllers\Admin\SitesList');

		//Получить основную информацию о сайте
		$r->map('GET', '/site/{id:[0-9]+}[/]', 'Kanakhin\Push\Infrastructure\ApiControllers\Admin\SiteInfo');

		//Удалить сайт
		$r->map('DELETE', '/site/{id:[0-9]+}[/]', 'Kanakhin\Push\Infrastructure\ApiControllers\Admin\SiteRemove');

		//Добавить новый сайт
		$r->map('POST', '/site/add[/]', 'Kanakhin\Push\Infrastructure\ApiControllers\Admin\SiteAdd');

	})->middleware(new \Kanakhin\Push\Infrastructure\Middlewares\AdminApiAuthorization());
};
