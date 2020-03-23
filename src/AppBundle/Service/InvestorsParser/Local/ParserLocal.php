<?php

namespace AppBundle\Service\InvestorsParser\Local;

use AppBundle\Service\InvestorsParser\BaseParser;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Parsers\Investor;

class ParserLocal extends BaseParser
{
    protected $parserId = 1;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);

        $this->crawler = new CrawlerLocal();
    }

    protected function parse($parseItem)
    {
        $url = $parseItem->getUrl();
        $nameCrawler = $parseItem->getCrawlerName();

        $body = $this->getBody($url);
        $html = $body['html'];
        $status = $body['status'];

        if ($status != 200) {
            return $body;
        }

        if ($nameCrawler == 'HomePage')
        {
            $items = $this->crawler->filterHomePage($html);
            $this->addLinks($items);
        }
        elseif ($nameCrawler == 'cityAll')
        {
            $items = $this->crawler->filterCityAll($html);
            $this->addLinks($items);
        }
        elseif ($nameCrawler == 'city')
        {
            $items = $this->crawler->filterCity($html);
            $this->addLinks($items);

        }
        elseif ($nameCrawler == 'pearson')
        {
            $items = $this->crawler->filterPerson($html);

            if ($items['phone'] != '')
            {
                $investor = new Investor();
                $investor->setParserId($this->parserId);
                $investor->setPhone($items['phone']);
                $investor->setName($items['name']);
                $investor->setSpecialization($items['specialization']);
                $investor->setAddress($items['address']);

                $this->insertOrUpdateInvestor($investor);
            }
        }

        return $body;
    }

    protected function isAllowLink($url): bool
    {
        return true;
        //return strpos($url, $this->linkProcess->getUrl(0))===0;
    }

}