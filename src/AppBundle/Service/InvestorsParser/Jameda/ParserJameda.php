<?php

namespace AppBundle\Service\InvestorsParser\Jameda;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Symfony\Component\DomCrawler\Crawler;

class ParserJameda
{
    protected $client;
    protected $crawler;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://www.jameda.de',
            'timeout'  => 5.0,
        ]);

        $this->crawler = new Crawler();


        //City  https://www.jameda.de/arztsuche/staedte/
        //doctors Berlin  https://www.jameda.de/berlin/stadt/
        //                https://www.jameda.de/berlin/stadt/?page=2
        //doctors Marl  https://www.jameda.de/marl/aerzte/gruppe/

        //https://www.jameda.de/arztsuche/fachgebiete/staedte/uebersicht/
    }

    public function getPage1($html) //prof
    {
        $this->crawler->addHtmlContent($html);

        $domElements = $this->crawler->filter('li > a');

        foreach ($domElements as $domElement) {
            print_r($domElement->attributes[0]->value . PHP_EOL);
            //print_r($domElement);
        }

        return 42;
    }

    public function getPage2($html) //city
    {
        $this->crawler->addHtmlContent($html);

        $domElements = $this->crawler->filter('li > a');

        foreach ($domElements as $domElement) {
            print_r($domElement->attributes[0]->value . PHP_EOL);
            //print_r($domElement);
        }

        return 42;
    }

    public function getPage3($html) //doctors
    {
        $this->crawler->addHtmlContent($html);

        $domElements = $this->crawler->filter('li div.xdh2gj-0 a.sc-1cdz3d0-0');//xdh2gj-0 cwPaOo

        foreach ($domElements as $domElement) {
            print_r($domElement->attributes[0]->value . PHP_EOL);
            //print_r($domElement);
        }

        return 42;
    }

    public function getPage4($html) //doctor
    {
        $this->crawler->addHtmlContent($html);

        $domElements = $this->crawler->filter('li div.xdh2gj-0 a.sc-1cdz3d0-0');//xdh2gj-0 cwPaOo

        foreach ($domElements as $domElement) {
            print_r($domElement->attributes[0]->value . PHP_EOL);
            //print_r($domElement);
        }

        return 42;
    }


    public function getHomePage()
    {
        $headers = [
            'Accept' => 'text/html',
            'User-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36',
            'Content-Type' => 'text/html; charset=UTF-8'
        ];

        $response = $this->client->get('contact.html',[
            'header' => $headers
        ]);

        //print_r($response);

        $html = $response->getBody();
        $this->crawler->addHtmlContent($html);

        $domElements = $this->crawler->filter('p');

        foreach ($domElements as $domElement) {
            print_r($domElement->nodeName);
        }



        return $response;
    }

    public function getHappyMessage()
    {
        $messages = [
            'You did it! You updated the system! Amazing!',
            'That was one of the coolest updates I\'ve seen all day!',
            'Great work! Keep going!',
        ];

        $index = array_rand($messages);

        return $messages[$index];
    }
}