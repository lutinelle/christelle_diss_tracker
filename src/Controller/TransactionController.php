<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\TransactionType;
use App\Repository\CryptoCurrencyRepository;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction')]
class TransactionController extends AbstractController
{
    #[Route('/', name: 'app_transaction_index', methods: ['GET'])]
    public function index(TransactionRepository $transactionRepository, CryptoCurrencyRepository $currencyRepository): Response
    {
        // get all currency capId from db as an array
        $currencies = $currencyRepository->findAll();
        $capIDs=[];
        foreach ($currencies as $currency) {
            $capIDs[]=$currency->getCapId();
        }
        //next transform array in string for API entry

        //get gurrency price
        $url = 'https://pro-api.coinmarketcap.com/v2/cryptocurrency/quotes/latest';

        $parameters = [
            'id' => "1,1321,52",
            'convert' => 'EUR'
        ];

        $headers = [
            'Accepts: application/json',
            'X-CMC_PRO_API_KEY: 4e6a1f04-fbce-47ad-8638-d0dda8bbef61'
        ];
        $qs = http_build_query($parameters); // query string encode the parameters
        $request = "{$url}?{$qs}"; // create the request URL


        $curl = curl_init(); // Get cURL resource
        // Set cURL options
        curl_setopt_array($curl, array(
            CURLOPT_URL => $request,            // set the request URL
            CURLOPT_HTTPHEADER => $headers,     // set the headers
            CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
        ));

        $response = curl_exec($curl); // Send the request, save the response
        $objResponse =json_decode($response); //json decoded response


        //build array of capId => current price from API
        $currentValue=[];
        foreach ($capIDs as $capId){
            $currentValue[$capId]=$objResponse->data->$capId->quote->EUR->price;
        }

        curl_close($curl); // Close request

        $transactions = $transactionRepository->findAll();
        $total =0;

        // calculate total balance based on API current value
        foreach ($transactions as $transaction){
            $total += ($currentValue[$transaction->getCurrency()->getCapId()]-$transaction->getPrice())*$transaction->getQty();
        }

        return $this->render('transaction/index.html.twig', [
            'transactions' => $transactionRepository->findAll(), 'total'=>$total
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
