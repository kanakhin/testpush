<?php

declare(strict_types=1);

/**
 * Команда для отображения уникального javascript кода для сайта
 */

namespace Kanakhin\Push\Infrastructure\Console;

use Kanakhin\Push\Application\UseCase\GetInlineCodeByHostUseCase;
use Kanakhin\Push\Application\UseCase\Request\GetInlineCodeByHostRequest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SiteCode extends Command
{
    private GetInlineCodeByHostUseCase $javascriptUseCase;
    public function __construct(?string $name, GetInlineCodeByHostUseCase $javascriptUseCase)
    {
        parent::__construct($name);
        $this->javascriptUseCase = $javascriptUseCase;
    }

    protected function configure()
    {
        $this->setName("sites:showcode")
            ->setDescription("Показать javascript код для сайта")
            ->addArgument('site', InputArgument::REQUIRED, 'Сайт');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $host = $input->getArgument('site');
        $request = new GetInlineCodeByHostRequest($host);
        $js_code = ($this->javascriptUseCase)($request)->inline_code;

        if (empty($js_code)) {
            $output->writeln("<info>$host</info>\t<error>Сайт не найден.</error>");
        } else {
            $output->writeln("<info>$host</info>\t$js_code");
        }

        return 0;
    }
}
