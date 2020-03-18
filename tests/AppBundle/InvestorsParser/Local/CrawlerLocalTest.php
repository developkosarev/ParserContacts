<?php

namespace Tests\AppBundle\InvestorsParser\Local;

use AppBundle\Service\InvestorsParser\Local\CrawlerLocal;
use PHPUnit\Framework\TestCase;

class CrawlerTest extends TestCase
{
    public function testPageLink()
    {
        $file = __DIR__ . '/Html/HomePage.html';

        $html = file_get_contents($file);

        $crawler = new CrawlerLocal();
        $result = $crawler->filterLink($html);

        $this->assertEquals('http://local004.local', $result[0]);
        $this->assertEquals('http://local004.local/cityAll.html', $result[1]);
    }
}