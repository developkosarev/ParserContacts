<?php

namespace AppBundle\Service\InvestorsParser\Anwalt;

use AppBundle\Service\InvestorsParser\BaseParser;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Parsers\Investor;

class ParserAnwalt extends BaseParser
{
    protected $parserId = 2;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);

        $this->crawler = new CrawlerAnwalt();
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
        elseif ($nameCrawler == 'getPlaces')
        {
            $items = $this->crawler->getPlaces($html);
            $this->addLinks($items);
        }
        elseif ($nameCrawler == 'getLawyers')
        {
            $items = $this->crawler->getLawyers($html);
            $this->addLinks($items);

        }
        elseif ($nameCrawler == 'getLawyer')
        {
            $items = $this->crawler->getLawyer($html);

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

        };

        return $body;
    }
}
