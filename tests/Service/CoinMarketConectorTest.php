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
        // (1) boot the Symfony kernel
        self::bootKernel();

        // (2) use static::getContainer() to access the service container
        $container = static::getContainer();

        // (3) run some service & test the result
        $CoinMarketConector = $container->get(CoinMarketConector::class);

        $currencyBitcoin= new CryptoCurrency();
        $currencyBitcoin->setCapId(1);
        $currencyBitcoin->setName("BitCoin");
        $currencyBitcoin->setSlug("bitcoin");
        $currencyBitcoin->setSymbol("BTC");

        $transaction1 = new Transaction();
        $transaction1->setPrice(50);
        $transaction1->setQty(3);
        $transaction1->setCurrency($currencyBitcoin);

        $transaction2 = new Transaction();
        $transaction2->setPrice(100);
        $transaction2->setQty(5);
        $transaction2->setCurrency($currencyBitcoin);

        $transactionRepository = $this->createMock(TransactionRepository::class);
        $transactionRepository->expects($this->once())
            ->method('findAll')
            ->willReturn([$transaction1,$transaction2]);


        $currentValue=[1=>150];

        //appel fct
        $total=$CoinMarketConector->calculateBalance($transactionRepository, $currentValue);


        $this->assertSame(550.0 ,$total);
    }


}
