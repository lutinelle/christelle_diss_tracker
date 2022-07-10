<?php

namespace App\Command;

use App\Entity\Balance;
use App\Repository\CryptoCurrencyRepository;
use App\Repository\TransactionRepository;
use App\Service\CoinMarketConector;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:create-entry')]
class DailyEntryCommand extends Command
{
    protected static $defaultName = 'app:create-entry';
    // the command description shown when running "php bin/console list"
    protected static $defaultDescription = 'Creates a new entry in balance table.';


    private $em;
    private $coinMarketConector;
    private $currencyRepository;
    private $transactionRepository;

    public function __construct(EntityManagerInterface $em, CoinMarketConector $coinMarketConector,
                                CryptoCurrencyRepository $currencyRepository, TransactionRepository $transactionRepository)
    {
        $this->em = $em;
        $this->coinMarketConector = $coinMarketConector;
        $this->currencyRepository = $currencyRepository;
        $this->transactionRepository = $transactionRepository;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // the command help shown when running the command with the "--help" option
            ->setHelp('This command allows you to store the balance in the balance table')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output ): int

    {
        $arrayCoinMarket=$this->coinMarketConector->getInstantBalance($this->currencyRepository, $this->transactionRepository);
        //create
        $entry = new Balance();
        $entry->setDate(new \DateTimeImmutable());
        $entry->setValue($arrayCoinMarket['total']) ;
        $this->em->persist($entry);
        $this->em->flush();
        return Command::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;

        // or return this to indicate incorrect command usage; e.g. invalid options
        // or missing arguments (it's equivalent to returning int(2))
        // return Command::INVALID
    }
}