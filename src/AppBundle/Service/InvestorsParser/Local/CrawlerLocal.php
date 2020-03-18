<?php

namespace AppBundle\Service\InvestorsParser\Local;

use Symfony\Component\DomCrawler\Crawler;

class CrawlerLocal
{
    protected $crawler;

    public function __construct()
    {
        $this->crawler = new Crawler();
    }

    /**
     * Parser page
     * Tag a
     */
    public function filterLink($html)
    {
        $this->crawler->clear();
        $this->crawler->addHtmlContent($html);

        $nodeValues = $this->crawler->filter('a')->each(function (Crawler $node, $i) {
            $href = $node->attr('href');

            return $href;
        });

        return $nodeValues;
    }

    /**
     * Parser page
     * HomePage
     */
    public function filterHomePage($html)
    {
        $this->crawler->clear();
        $this->crawler->addHtmlContent($html);

        $nodeValues = $this->crawler->filter('a')->each(function (Crawler $node, $i) {
            $href = $node->attr('href');

            return [
                'href' => $href,
                'crawler' => 'cityAll'
            ];
        });

        return $nodeValues;
    }

    /**
     * Parser page
     * HomePage
     */
    public function filterCityAll($html)
    {
        $this->crawler->clear();
        $this->crawler->addHtmlContent($html);

        $nodeValues = $this->crawler->filter('a')->each(function (Crawler $node, $i) {
            $href = $node->attr('href');

            return [
                'href' => $href,
                'crawler' => 'city'
            ];
        });

        return $nodeValues;
    }

    /**
     * Parser page
     * HomeCity
     */
    public function filterCity($html)
    {
        $this->crawler->clear();
        $this->crawler->addHtmlContent($html);

        $nodeValues = $this->crawler->filter('a')->each(function (Crawler $node, $i) {
            $href = $node->attr('href');

            return [
                'href' => $href,
                'crawler' => 'pearson'
            ];
        });

        return $nodeValues;
    }

    /**
     * Parser page
     * HomeCity
     */
    public function filterPerson($html)
    {
        $this->crawler->clear();
        $this->crawler->addHtmlContent($html);

        $phone = '';
        $name = '';
        $specialization = '';
        $address = '';

        $crawler = $this->crawler->filter('h4')->first();
        if ($crawler->count() > 0 ) {
            $phone = trim($crawler->text());

            $name = $this->crawler->filter('h1')->first()->text();
            $name = trim($name);

            $specialization = $this->crawler->filter('h2')->first()->text();
            $specialization = trim($specialization);

            $address = $this->crawler->filter('h3')->first()->text();
            $address = trim($address);
        }

        return [
            'phone' => $phone,
            'name' => $name,
            'specialization' => $specialization,
            'address' => $address
        ];
    }
}