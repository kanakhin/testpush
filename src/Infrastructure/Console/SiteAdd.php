<?php

declare(strict_types=1);

/**
 * Команда для добавления нового сайта или несколько сайтов
 */

namespace Kanakhin\Push\Infrastructure\Console;

use Kanakhin\Push\Application\UseCase\CreateSiteUseCase;
use Kanakhin\Push\Application\UseCase\GetInlineCodeByHashIdUseCase;
use Kanakhin\Push\Application\UseCase\Request\CreateSiteRequest;
use Kanakhin\Push\Application\UseCase\Request\GetInlineCodeByHashIdRequest;
use Minishlink\WebPush\VAPID;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SiteAdd extends Command
{
    private CreateSiteUseCase $createSiteUseCase;
    private GetInlineCodeByHashIdUseCase $javascriptUseCase;

    public function __construct(?string $name, CreateSiteUseCase $createSiteUseCase, GetInlineCodeByHashIdUseCase $javascriptUseCase)
    {
        parent::__construct($name);
        $this->createSiteUseCase = $createSiteUseCase;
        $this->javascriptUseCase = $javascriptUseCase;
    }

    protected function configure()
    {
        $this->setName("sites:add")
            ->setDescription("Добавить новый сайт")
            ->addArgument('sites', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Сайты для добавления');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($input->getArgument('sites') as $host) {
            $host = parse_url($host)['host'] ?? $host;
            $vapid_keys = VAPID::createVapidKeys(); //Генерация новых VAPID ключей
            $hash_id = bin2hex(random_bytes(16)); //Генерация нового hash_id

            $request = new CreateSiteRequest($hash_id, $host, $vapid_keys['publicKey'], $vapid_keys['privateKey']);
            $id = ($this->createSiteUseCase)($request)->id;

            if (!is_null($id)) {
                $request = new GetInlineCodeByHashIdRequest($hash_id);
                $js_code = ($this->javascriptUseCase)($request)->inline_code;

                $output->writeln("<info>Сайт {$host} успешно добавлен. Код установки на сайт $js_code</info>");
            } else {
                $output->writeln("<info>Ошибка при сохранении сайта {$host}</info>");
            }
        }

        return 0;
    }
}
