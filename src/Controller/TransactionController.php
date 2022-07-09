<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\TransactionType;
use App\Repository\CryptoCurrencyRepository;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\CoinMarketConector;


class TransactionController extends AbstractController
{
    #[Route('/', name: 'app_transaction_index', methods: ['GET'])]
    public function index(CryptoCurrencyRepository $currencyRepository, TransactionRepository $transactionRepository, CoinMarketConector $coinMarketConector ): Response
    {
        $arrayCoinMarket=$coinMarketConector->getInstantBalance($currencyRepository, $transactionRepository);

        return $this->render('transaction/index.html.twig', [
            'transactions' => $transactionRepository->findAll(), 'total'=>$arrayCoinMarket['total'],'tendency' =>$arrayCoinMarket['currentTendency24'],
        ]);
    }

    #[Route('/new', name: 'app_transaction_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        //create
        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);


         // submit
        if ($form->isSubmitted() ) {
            $em->persist($transaction);
            $em->flush();
            return $this->redirectToRoute('app_transaction_index');
        }

        //render

        return $this->renderForm('transaction/new.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_transaction_show', methods: ['GET'])]
    public function show(Transaction $transaction): Response
    {
        return $this->render('transaction/show.html.twig', [
            'transaction' => $transaction,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transaction_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Transaction $transaction, TransactionRepository $transactionRepository): Response
    {
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transactionRepository->add($transaction, true);

            return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/edit.html.twig', [
            'transaction' => $transaction,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_transaction_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Transaction $transaction, TransactionRepository $transactionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transaction->getId(), $request->request->get('_token'))) {
            $transactionRepository->remove($transaction, true);

            return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);

        }

        return $this->render('transaction/delete.html.twig', [
            'transaction' => $transaction,
        ]);
    }
}
