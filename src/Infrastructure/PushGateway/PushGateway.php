<?php

declare(strict_types=1);

namespace Kanakhin\Push\Infrastructure\PushGateway;

use Kanakhin\Push\Application\PushGateway\PushGatewayInterface;
use Kanakhin\Push\Application\PushGateway\Request\SendPushRequest;
use Kanakhin\Push\Application\PushGateway\Response\SendPushResponse;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class PushGateway implements PushGatewayInterface
{
    public function sendPush(SendPushRequest $request): SendPushResponse
    {
        $auth = [
            'VAPID' => [
                'subject' => $request->title,
                'publicKey' => $request->public_vapid_key,
                'privateKey' => $request->private_vapid_key,
            ],
        ];

        $defaultOptions = [
            'TTL' => 600,
            'batchSize' => 1000,
        ];

        $webPush = new WebPush($auth, $defaultOptions);
        $webPush->setAutomaticPadding(false);

        //Отправка пуш сообщения каждому получателю
        $subscription = Subscription::create([
            'endpoint' => $request->endpoint,
            'publicKey' => $request->key,
            'authToken' => $request->token
        ]);
        $payload = json_encode([
            'title' => $request->title,
            'body' => $request->body,
            'data' => ['url' => $request->url]
        ], JSON_UNESCAPED_UNICODE);

        $webPush->queueNotification($subscription, $payload);

        $result = [];
        foreach ($webPush->flush() AS $report) {
            $result[] = $report;
        }

        return new SendPushResponse(json_encode($result));
    }

}