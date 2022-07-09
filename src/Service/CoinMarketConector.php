<?php

namespace App\Service;

use App\Repository\CryptoCurrencyRepository;
use App\Repository\TransactionRepository;

class CoinMarketConector
{
    public function getInstantBalance(CryptoCurrencyRepository $currencyRepository, TransactionRepository $transactionRepository): array
    {
        // get all currency capId from db as an array
        $paramApiConvert = "EUR";
        $currencies = $currencyRepository->findAll();
        $capIDs=[];
        foreach ($currencies as $currency) {
            $capIDs[]=$currency->getCapId();
        }
        //next transform array in string for API entry
        $paramApiId= implode(",", $capIDs);

        //get currency price
        $url = 'https://pro-api.coinmarketcap.com/v2/cryptocurrency/quotes/latest';

        $parameters = [
            'id' => $paramApiId,
            'convert' => $paramApiConvert
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
        $currentTendency24=[];
        foreach ($capIDs as $capId){
            $currentValue[$capId]=$objResponse->data->$capId->quote->$paramApiConvert->price;
            $currentTendency24[$capId]=$objResponse->data->$capId->quote->$paramApiConvert->percent_change_24h;
        }


        curl_close($curl); // Close request

        $transactions = $transactionRepository->findAll();
        $total =0;

        // calculate total balance based on API current value
        foreach ($transactions as $transaction){
            $total += ($currentValue[$transaction->getCurrency()->getCapId()]-$transaction->getPrice())*$transaction->getQty();
        }

        return ['total'=>$total,'currentTendency24'=>$currentTendency24];
    }
}