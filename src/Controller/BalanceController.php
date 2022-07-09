<?php

namespace App\Controller;



use App\Repository\BalanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
}
