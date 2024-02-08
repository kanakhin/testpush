<?php

namespace Kanakhin\Push\Application\UseCase\PushGateway;

use Kanakhin\Push\Application\UseCase\PushGateway\Request\SendPushRequest;
use Kanakhin\Push\Application\UseCase\PushGateway\Response\SendPushResponse;

interface PushGatewayInterface
{
    public function sendPush(SendPushRequest $request): SendPushResponse;
}