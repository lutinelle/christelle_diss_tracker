<?php

namespace App\Controller;

use App\Entity\CryptoCurrency;
use App\Form\CryptoCurrencyType;
use App\Repository\CryptoCurrencyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// for the admin only
#[Route('/cryptocurrency')]
class CryptoCurrencyController extends AbstractController
{

    #[Route('/', name: 'app_crypto_currency_index', methods: ['GET'])]
    public function index(CryptoCurrencyRepository $cryptoCurrencyRepository): Response
    {
        return $this->render('crypto_currency/index.html.twig', [
            'crypto_currencies' => $cryptoCurrencyRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_crypto_currency_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CryptoCurrencyRepository $cryptoCurrencyRepository): Response
    {
        $cryptoCurrency = new CryptoCurrency();
        $form = $this->createForm(CryptoCurrencyType::class, $cryptoCurrency);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cryptoCurrencyRepository->add($cryptoCurrency, true);

            return $this->redirectToRoute('app_crypto_currency_index', [], Response::HTTP_SEE_OTHER);
        }

        // if add currency, remind to add svg file for icon in public/svg
        //and name it same as slug-logo!!
        return $this->renderForm('crypto_currency/new.html.twig', [
            'crypto_currency' => $cryptoCurrency,
            'form' => $form,
        ]);
    }


    #[Route('/{id}', name: 'app_crypto_currency_show', methods: ['GET'])]

    public function show(CryptoCurrency $cryptoCurrency): Response
    {
        return $this->render('crypto_currency/show.html.twig', [
            'crypto_currency' => $cryptoCurrency,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_crypto_currency_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CryptoCurrency $cryptoCurrency, CryptoCurrencyRepository $cryptoCurrencyRepository): Response
    {
        $form = $this->createForm(CryptoCurrencyType::class, $cryptoCurrency);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cryptoCurrencyRepository->add($cryptoCurrency, true);

            return $this->redirectToRoute('app_crypto_currency_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('crypto_currency/edit.html.twig', [
            'crypto_currency' => $cryptoCurrency,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_crypto_currency_delete', methods: ['POST'])]
    public function delete(Request $request, CryptoCurrency $cryptoCurrency, CryptoCurrencyRepository $cryptoCurrencyRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cryptoCurrency->getId(), $request->request->get('_token'))) {
            $cryptoCurrencyRepository->remove($cryptoCurrency, true);
        }

        return $this->redirectToRoute('app_crypto_currency_index', [], Response::HTTP_SEE_OTHER);
    }
}
