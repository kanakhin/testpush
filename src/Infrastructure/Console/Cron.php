<?php

declare(strict_types=1);

/**
 * Команда без параметров должна запускаться кроном каждую минуту (console core:cron)
 */

namespace Kanakhin\Push\Infrastructure\Console;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Kanakhin\Push\Application\UseCase\GetMessageToSendUseCase;
use Kanakhin\Push\Application\UseCase\GetSubscriptionsForMessageUseCase;
use Kanakhin\Push\Application\UseCase\MarkMessageAsSendedUseCase;
use Kanakhin\Push\Application\UseCase\Request\SendPushRequest;
use Kanakhin\Push\Application\UseCase\Request\GetMessageToSendRequest;
use Kanakhin\Push\Application\UseCase\Request\GetSubscriptionsToMessageRequest;
use Kanakhin\Push\Application\UseCase\Request\MarkMessageAsSendedRequest;
use Kanakhin\Push\Application\UseCase\SendPushUseCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Cron extends Command
{
    private GetMessageToSendUseCase $getMessageToSendUseCase;
    private MarkMessageAsSendedUseCase $markMessageAsSendedUseCase;
    private GetSubscriptionsForMessageUseCase $getSubscriptionsUseCase;
    private SendPushUseCase $sendPushUseCase;
    public function __construct(?string $name, GetMessageToSendUseCase $getMessageToSendUseCase, MarkMessageAsSendedUseCase $markMessageAsSendedUseCase, GetSubscriptionsForMessageUseCase $getSubscriptionsUseCase, SendPushUseCase $sendPushUseCase)
    {
        parent::__construct($name);
        $this->getMessageToSendUseCase = $getMessageToSendUseCase;
        $this->markMessageAsSendedUseCase = $markMessageAsSendedUseCase;
        $this->getSubscriptionsUseCase = $getSubscriptionsUseCase;
        $this->sendPushUseCase = $sendPushUseCase;
    }

    protected function configure()
	{
		$this->setName("core:cron")
			->setDescription("Рассылка сообщений")
			->setHelp("Подбор сообщений, готовых к рассылке и рассылка сообщений");
	}

	protected function execute(InputInterface $input, OutputInterface $output): int
	{
		$output->writeln("<info>" . date("d.m.y H:i:s") . " Поиск сообщений для отправки...</info>");

        $request = new GetMessageToSendRequest();
        $message_id = ($this->getMessageToSendUseCase)($request)->id;

        if (is_null($message_id)) {
            $output->writeln("<info>" . date("d.m.y H:i:s") . " Нет сообщений для отправки</info>");
            return 0;
        }

        $output->writeln("<info>" . date("d.m.y H:i:s") . " Найдено сообщение для отправки [$message_id]</info>");

        $request = new MarkMessageAsSendedRequest($message_id);
        ($this->markMessageAsSendedUseCase)($request);

        $request = new GetSubscriptionsToMessageRequest($message_id);
        $subscriptions = ($this->getSubscriptionsUseCase)($request)->subscriptions;

        if (empty($subscriptions)) {
            $output->writeln("<info>" . date("d.m.y H:i:s") . " Не найден ни один подписчик для сообщения</info>");
        }

        foreach ($subscriptions AS $subscription) {
            $request = new SendPushRequest(
              $subscription['vapid_key_public'],
              $subscription['vapid_key_private'],
              $subscription['title'],
              600,
              $subscription['text'],
              $subscription['link'],
              $subscription['endpoint'],
              $subscription['key'],
              $subscription['token']
            );

            $response = ($this->sendPushUseCase)($request)->response;
            $output->writeln("<info>" . date("d.m.y H:i:s") . " Результат отправки: $response</info>");
        }

        return 0;
	}
}
