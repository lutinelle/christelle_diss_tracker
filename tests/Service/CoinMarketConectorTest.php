<?php

namespace App\Tests\Service;


use App\Entity\CryptoCurrency;
use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use App\Service\CoinMarketConector;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CoinMarketConectorTest extends KernelTestCase
{
    public function testCalculateBalance(): void
    {
        //  boot the Symfony kernel
        self::bootKernel();

        //  use static::getContainer() to access the service container
        $container = static::getContainer();

        //  run some service
        $CoinMarketConector = $container->get(CoinMarketConector::class);

        // create test currency entity
        $currencyBitcoin= new CryptoCurrency();
        $currencyBitcoin->setCapId(1);
        $currencyBitcoin->setName("BitCoin");
        $currencyBitcoin->setSlug("bitcoin");
        $currencyBitcoin->setSymbol("BTC");

        // create test transaction entity
        $transaction1 = new Transaction();
        $transaction1->setPrice(50);
        $transaction1->setQty(3);
        $transaction1->setCurrency($currencyBitcoin);

        $transaction2 = new Transaction();
        $transaction2->setPrice(100);
        $transaction2->setQty(5);
        $transaction2->setCurrency($currencyBitcoin);

        // create morck for transactionRepository

        $transactionRepository = $this->createMock(TransactionRepository::class);
        $transactionRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$transaction1,$transaction2]);

        // test data for current value of currency (only for currency used)
        $currentValue=[1=>150];

        //call fct to test
        $total=$CoinMarketConector->calculateBalance($transactionRepository, $currentValue);


        $this->assertSame(550.0 ,$total);
    }


}
