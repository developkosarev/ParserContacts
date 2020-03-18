<?php

namespace AppBundle\Service\InvestorsParser\Anwalt;

use Symfony\Component\DomCrawler\Crawler;

class CrawlerAnwalt
{
    protected $crawler;

    public function __construct()
    {
        $this->crawler = new Crawler();
    }

    /**
     * Parser page
     * HomePage
     */
    public function filterHomePage($html)
    {
        return [
            '0' =>[
                'href' => '/verzeichnis/orte.php',
                'crawler' => 'getPlaces'
            ]
        ];

    }

    /**
     * Parser page
     * https://www.anwalt.de/verzeichnis/orte.php
     */
    public function getPlaces($html)
    {
        $this->crawler->clear();
        $this->crawler->addHtmlContent($html);

        $nodeValues = $this->crawler->filter('ul.infoResultsList li a')->each(function (Crawler $node, $i) {
            $count = $node->parents()->first()->text();
            $count = preg_replace("/[^0-9]/", '', $count);

            $text = trim($node->text());
            $href = $node->attr('href');

            return [
                'plase' => $text,
                'count' => $count,
                'href' => $href,
                'crawler' => 'getLawyers'
            ];
        });

        return $nodeValues;
    }

    /**
     * Parser page
     * https://www.anwalt.de/rechtsanwalt/alpen.php
     * https://www.anwalt.de/rechtsanwalt/berlin.php
     */
    public function getLawyers($html)
    {
        $this->crawler->clear();
        $this->crawler->addHtmlContent($html);

        $items = $this->crawler->filter('ul li.anw-hover div.row a.anw-link-heading')->each(function (Crawler $node, $i) {

            $text = trim($node->text());
            $href = $node->attr('href');

            return [
                'name' => $text,
                'href' => $href,
                'crawler' => 'getLawyer'
            ];
        });

        $crawler = $this->crawler->filter('div.controlsRight a')->first();
        if ($crawler->count() > 0 ){
            $nextPage = $crawler->attr('href');

            $items[] = [
                'name' => '',
                'href' => $nextPage,
                'crawler' => 'getLawyers'
            ];
        }

        return $items;
    }

    /**
     * Parser page
     * https://www.anwalt.de/berthold-verhoeven
     * https://www.anwalt.de/antje-marschke
     */
    public function getLawyer($html)
    {
        $this->crawler->clear();
        $this->crawler->addHtmlContent($html);

        $name = $this->crawler->filter('h1')->first()->text();
        $name = trim($name);


        $crawler = $this->crawler->filter('div.jsProfileStickyBar div.row');

        $specialization = $crawler->filter('strong')->first()->text();
        $specialization = trim($specialization);

        $crawler = $this->crawler->filter('div.mb-2');

        $address = $crawler->filter('span')->first()->text();

        $phone = $crawler->filter('a.jsPhoneCounterAble')->first()->attr('href');
        $phone = str_replace('tel:','',$phone);

        return [
            'name' => $name,
            'specialization' => $specialization,
            'address' => $address,
            'phone' => $phone
        ];
    }
}