<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TransactionControllerTest extends WebTestCase
{
    public function testIndexPage(): void
    {
        // request index page
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        //test response (status must be 200)
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('h1:contains("Crypto Tracker")')->count());

        //clic on total link and check result page (balance)
        $link = $crawler->filter('#total')->link();
        $crawler=$client->click($link);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('h1:contains("History des gains")')->count());

        //close balance page and check result page (index)
        $link = $crawler->filter('.close')->link();
        $crawler=$client->click($link);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('h1:contains("Crypto Tracker")')->count());

        //clic on add link and check result page (add)
        $link = $crawler->filter('.add')->link();
        $crawler=$client->click($link);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('h1:contains("Ajouter une transaction")')->count());

        //close balance page and check result page (index)
        $link = $crawler->filter('.close')->link();
        $crawler=$client->click($link);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('h1:contains("Crypto Tracker")')->count());

        //clic on delete link and check result page (edit)
        $link = $crawler->filter('.edit')->link();
        $crawler=$client->click($link);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('h1:contains("Modifier une transaction")')->count());

        //close balance page and check result page (index)
        $link = $crawler->filter('.close')->link();
        $crawler=$client->click($link);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('h1:contains("Crypto Tracker")')->count());

        //clic on delete link and check result page (delete)
        $link = $crawler->filter('.del')->link();
        $crawler=$client->click($link);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('h1:contains("Supprimer un montant")')->count());

        //close balance page and check result page (index)
        $link = $crawler->filter('.close')->link();
        $crawler=$client->click($link);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('h1:contains("Crypto Tracker")')->count());

    }
    public function testAddTransactionScenario(): void
    {
        // request index page
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        //test response (status must be 200)
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('h1:contains("Crypto Tracker")')->count());

        //clic on add link and check result page (add)
        $link = $crawler->filter('.add')->link();
        $crawler=$client->click($link);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('h1:contains("Ajouter une transaction")')->count());

        // submit form and redirect
        $client->submitForm("Ajouter", ["transaction[qty]" => 100,"transaction[price]"=> 15000,"transaction[currency]" => 1]);
        $crawler = $client->followRedirect();

        // test redirect
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('h1:contains("Crypto Tracker")')->count());

        //clic on edit link and check result page (add)
        $link = $crawler->filter('.add')->link();
        $crawler=$client->click($link);
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('h1:contains("Ajouter une transaction")')->count());

        // submit form and redirect
        $client->submitForm("Ajouter", ["transaction[qty]" => 100,"transaction[price]"=> 15000,"transaction[currency]" => 1]);
        $crawler = $client->followRedirect();

        // test redirect
        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertSame(1, $crawler->filter('h1:contains("Crypto Tracker")')->count());


    }

}
