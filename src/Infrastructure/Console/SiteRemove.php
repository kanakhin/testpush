<?php

declare(strict_types=1);

/**
 * Команда для удаления сайта или нескольких сайтов
 */

namespace Kanakhin\Push\Infrastructure\Console;

use Kanakhin\Push\Application\UseCase\RemoveSiteByHostUseCase;
use Kanakhin\Push\Application\UseCase\Request\RemoveSiteByHostRequest;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class SiteRemove extends Command
{
    private RemoveSiteByHostUseCase $removeSiteUseCase;
    public function __construct(?string $name, RemoveSiteByHostUseCase $removeSiteUseCase)
    {
        parent::__construct($name);
        $this->removeSiteUseCase = $removeSiteUseCase;
    }

    protected function configure()
    {
        $this->setName("sites:remove")
            ->setDescription("Удалить сайт")
            ->addArgument('sites', InputArgument::IS_ARRAY | InputArgument::REQUIRED, 'Сайты для удаления');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        //Подтверждение удаления
        $helper = $this->getHelper('question');
        $sites_list = implode(', ', $input->getArgument('sites'));
        $question = new ConfirmationQuestion("Подтвердите удаление сайта $sites_list (y/n) ", false);
        if (!$helper->ask($input, $output, $question)) {
            return 0;
        }

        foreach ($input->getArgument('sites') as $host) {
            $request = new RemoveSiteByHostRequest($host);
            $removed = ($this->removeSiteUseCase)($request)->removed;

            if (!$removed) {
                $output->writeln("<info>$host</info>\t не найден");
            } else {
                $output->writeln("<info>$host</info>\t удален");
            }
        }

        return 0;
    }
}
