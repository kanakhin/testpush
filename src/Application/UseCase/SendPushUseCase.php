<?php

declare(strict_types=1);

namespace Kanakhin\Push\Application\UseCase;

use Kanakhin\Push\Application\PushGateway\PushGatewayInterface;
use Kanakhin\Push\Application\PushGateway\Request\SendPushRequest as PushGatewaySendPushRequest;
use Kanakhin\Push\Application\UseCase\Request\SendPushRequest;
use Kanakhin\Push\Application\UseCase\Response\SendPushResponse;

class SendPushUseCase
{
    private PushGatewayInterface $push_gateway;
    public function __construct(PushGatewayInterface $push_gateway)
    {
        $this->push_gateway = $push_gateway;
    }

    public function __invoke(SendPushRequest $request): SendPushResponse
    {
        $push_request = new PushGatewaySendPushRequest(
            $request->public_vapid_key,
            $request->private_vapid_key,
            $request->title,
            $request->ttl,
            $request->body,
            $request->url,
            $request->endpoint,
            $request->key,
            $request->token
        );

        $response = $this->push_gateway->sendPush($push_request)->response;

        return new SendPushResponse($response);
    }
}