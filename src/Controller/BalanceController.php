<?php

namespace App\Controller;



use App\Entity\Balance;

use App\Repository\BalanceRepository;
use App\Repository\CryptoCurrencyRepository;
use App\Repository\TransactionRepository;
use App\Service\CoinMarketConector;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/graph')]
class BalanceController extends AbstractController
{
    #[Route('/', name: 'app_balance')]
    public function index(ChartBuilderInterface $chartBuilder, BalanceRepository $balanceRepository): Response
    {
        $balanceHistory =$balanceRepository->findHistorySortByDate();
        $valueHistory=[];
        foreach ($balanceHistory as $dataCouple)
        {
            $valueHistory[]=$dataCouple['value'];
        }
        $dateHistory=array_keys($valueHistory);

        $chart = $chartBuilder->createChart(Chart::TYPE_LINE);

        $chart->setData([
            'labels' => $dateHistory,
            'datasets' => [
                [
                    'label' => 'gain',
                    'backgroundColor' => 'rgb(31, 196, 108)',
                    'borderColor' => 'rgb(31, 196, 108)',
                    'data' => $valueHistory,
                ],
            ],
        ]);

        $chart->setOptions([
            'scales' => [
                'x'=> [
                    'ticks'=> [
                        "display"=> false,
                    ]
                ],
                'y' => [
                    'suggestedMin' => 0,
                ],
            ],
            'plugins'=> [
                'legend'=> [
                    'display'=>  false
                ]
            ]
        ]);

        return $this->render('balance/index.html.twig', [
            'chart' => $chart,
            'data'=>$balanceHistory,
        ]);
    }

    #[Route('/new', name: 'app_balance_new', methods: ['GET', 'POST'])]

    public function newEntry(EntityManagerInterface $em, CryptoCurrencyRepository $currencyRepository, TransactionRepository $transactionRepository, CoinMarketConector $coinMarketConector): Response
    {
        $arrayCoinMarket=$coinMarketConector->getInstantBalance($currencyRepository, $transactionRepository);
        //create
        $entry = new Balance();
        $entry->setDate(new \DateTimeImmutable());
        $entry->setValue($arrayCoinMarket['total']) ;
            $em->persist($entry);
            $em->flush();
            return $this->redirectToRoute('app_transaction_index');

    }
}
