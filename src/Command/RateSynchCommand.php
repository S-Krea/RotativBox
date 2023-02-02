<?php

namespace App\Command;

use App\Service\PriceRateImporter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:rate:synch',
    description: 'Add a short description for your command',
)]
class RateSynchCommand extends Command
{
    private PriceRateImporter $rateImporter;

    public function __construct(PriceRateImporter $rateImporter)
    {
        parent::__construct();
        $this->rateImporter = $rateImporter;
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::REQUIRED, 'The file to import');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getArgument('file');

        $this->rateImporter->process($file);


        $io->success('Imported');

        return Command::SUCCESS;
    }
}
