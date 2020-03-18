<?php

namespace AppBundle\Service\InvestorsParser\Local;

use AppBundle\Service\InvestorsParser\AbstractParser;
use AppBundle\Service\InvestorsParser\LinkProcessedRepository;
use AppBundle\Service\InvestorsParser\LinkRepository;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Parsers\Investor;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

class ParserLocal extends AbstractParser
{
    protected $parserId = 1;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);

        //$this->linkProcess = new LinkRepository('LinkProcess.csv');
        //$this->linkProcessed = new LinkProcessedRepository('LinkProcessed.csv');

        $this->crawler = new CrawlerLocal();
    }

    protected function parse($url, $nameCrawler)
    {
        $body = $this->getBody($url);
        $html = $body['html'];
        $status = $body['status'];

        if ($status != 200) {
            return $status;
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

        return $status;
    }

    protected function getBody($url)
    {
        $headers = [
            'Accept' => 'text/html',
            'User-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36',
            'Content-Type' => 'text/html; charset=UTF-8'
        ];

        $html = '';
        $status = null;

        try {
            $response = $this->client->get($url,['header' => $headers]);
            $html = $response->getBody()->getContents();
            $status = $response->getStatusCode();
        } catch (ClientException $e) {
            $status = $e->getCode();

            //echo Psr7\str($e->getRequest());
            //echo Psr7\str($e->getResponse());

        } catch (RequestException $e) {
            $status = $e->getCode();

            //echo Psr7\str($e->getRequest());
            //if ($e->hasResponse()) {
            //    echo Psr7\str($e->getResponse());
            //}
        }

        $result = ['html' => $html, 'status' => $status];

        return $result;
    }

    protected function isAllowLink($url): bool
    {
        return true;
        //return strpos($url, $this->linkProcess->getUrl(0))===0;
    }

}