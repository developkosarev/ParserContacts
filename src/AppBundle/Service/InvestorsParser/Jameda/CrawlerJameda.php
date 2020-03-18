<?php

namespace AppBundle\Service\InvestorsParser\Jameda;

use Symfony\Component\DomCrawler\Crawler;

class CrawlerJameda
{
    protected $crawler;

    public function __construct()
    {
        $this->crawler = new Crawler();
    }

    public function getDoctor($html)
    {
        $this->crawler->clear();
        $this->crawler->addHtmlContent($html);

        $degree = '';
        $degreeCrawler = $this->crawler->filter('h1 > span')->first();
        if ($degreeCrawler->count() > 0 ){
            $degree = $degreeCrawler->text();
            $degree = trim($degree);
        }

        $name = $this->crawler->filter('h1')->first()->text();
        $name = str_replace($degree, "", $name);
        $name = str_replace("\r\n", "", $name);
        $name = trim($name);

        $phoneNumber = $this->crawler->filter('#phoneNumber')->attr('href');
        $phoneNumber = str_replace('tel:','',$phoneNumber);

        $phoneNumberMobile = $this->crawler->filter('#phoneNumberMobile')->attr('href');
        $phoneNumberMobile = str_replace('tel:','',$phoneNumberMobile);

        $website = $this->crawler->filter('#website')->attr('href');

        return [
            'degree' => $degree,
            'name' => $name,
            'phoneNumber' => $phoneNumber,
            'phoneNumberMobile' => $phoneNumberMobile,
            'website' => $website
            ];
    }

    public function getDoctors($html)
    {
        //$domElements = $this->crawler->filter('#phoneNumber');

        //foreach ($domElements as $domElement) {
        //    print_r($domElement->attributes['href']->value . PHP_EOL);
        //    //print_r($domElement);
        //}
    }

    public function getRegions($html)
    {

    }
}