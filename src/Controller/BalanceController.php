<?php

namespace App\Controller;



use App\Entity\Balance;
use App\Entity\Transaction;
use App\Form\BalanceType;
use App\Form\TransactionType;
use App\Repository\BalanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class BalanceController extends AbstractController
{
    #[Route('/graph', name: 'app_balance')]
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
                    'backgroundColor' => 'rgb(255, 99, 132)',
                    'borderColor' => 'rgb(255, 99, 132)',
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
        ]);
    }

    #[Route('/new', name: 'app_balance_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        //create
        $entry = new Balance();


            $em->persist($entry);
            $em->flush();
            return $this->redirectToRoute('app_transaction_index');

    }
}
