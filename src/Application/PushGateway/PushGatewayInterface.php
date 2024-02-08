<?php

namespace Kanakhin\Push\Application\PushGateway;

use Kanakhin\Push\Application\PushGateway\Request\SendPushRequest;
use Kanakhin\Push\Application\PushGateway\Response\SendPushResponse;

interface PushGatewayInterface
{
    public function sendPush(SendPushRequest $request): SendPushResponse;
}