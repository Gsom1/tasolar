<?php

namespace App\Command;

use App\Entity\PaymentTransaction;
use App\PaymentTransaction\PaymentTransactionStatus;
use App\PaymentTransaction\SettleProcess;
use App\Repository\PaymentTransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;


/**
 * Only one instance of this command should work at a time
 *
 * In order to run multiple instance need to keep in redis last transaction id
 * and create group-shared (keep every card number lock till the end of process) lock for all card's number in the bunch
 */
#[AsCommand(
    name: 'transactions:settle_bulk',
)]
class TransactionsSettleBulkCommand extends Command
{
    public function __construct(
        private readonly PaymentTransactionRepository $transactionRepository,
        private readonly EntityManagerInterface       $em,
        private readonly SettleProcess                $settleProcess,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('size', InputArgument::OPTIONAL, 'Bulk size', 3)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $size = $input->getArgument('size');

        $transactions = $this->transactionRepository
            ->findBy(
                criteria: [
                              PaymentTransaction::FIELD_STATUS => PaymentTransactionStatus::APPROVED->value,
                          ],
                limit   : $size
            )
        ;

        foreach ($transactions as $transaction) {
            $io->info('Transaction ' . $transaction->getId());
            try {
                $this->settleProcess->settle($transaction);
            } catch (\Throwable $e) {
                $io->error($e->getMessage());
            }
        }

        //Commit the bulk changes to DB
        $this->em->flush();

        $io->success('Done.');

        return Command::SUCCESS;
    }
}
