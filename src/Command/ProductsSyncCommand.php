<?php

namespace App\Command;

use App\Service\ProductSynchronizer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:products:sync',
    description: 'Synchronize all products from Woocommerce',
)]
class ProductsSyncCommand extends Command
{
    private ProductSynchronizer $productSynchronizer;

    public function __construct(ProductSynchronizer $productSynchronizer)
    {
        parent::__construct();
        $this->productSynchronizer = $productSynchronizer;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $countProductsSynched = $this->productSynchronizer->synchronizeAll();

        $io->success(sprintf('%d products synched', $countProductsSynched));

        return Command::SUCCESS;
    }
}
