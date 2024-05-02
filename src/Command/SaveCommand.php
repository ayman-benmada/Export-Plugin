<?php

declare(strict_types=1);

namespace Abenmada\ExportPlugin\Command;

use Abenmada\ExportPlugin\Service\ProcessServiceInterface;
use Safe\DateTime;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class SaveCommand extends Command
{
    public function __construct(private ProcessServiceInterface $processService, ?string $name = null)
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Save exported data')
            ->setName('abenmada:export_plugin:save')
            ->addArgument('alias', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $nowDate = new DateTime();

        $alias = $input->getArgument('alias');

        $this->processService->process($alias, null, true);

        $io->success('Save exported data ' . $alias . ' has been successfully executed at ' . $nowDate->format('d-m-Y H:i'));

        return 1;
    }
}
