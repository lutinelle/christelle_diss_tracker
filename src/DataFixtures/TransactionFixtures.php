<?php

namespace App\DataFixtures;

use App\Entity\CryptoCurrency;
use App\Entity\Transaction;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TransactionFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $currencyBitcoin= (new CryptoCurrency())
            ->setCapId(1)
            ->setName("BitCoin")
            ->setSlug("bitcoin")
            ->setSymbol("BTC");
        $manager->persist($currencyBitcoin);


        for($i = 0; $i <10; $i++)
        {
            $transaction = (new Transaction())
                ->setCurrency($currencyBitcoin)
                ->setQty($i)
                ->setPrice($i*100);

             $manager->persist($transaction);
        }
        $manager->flush();
    }
}
