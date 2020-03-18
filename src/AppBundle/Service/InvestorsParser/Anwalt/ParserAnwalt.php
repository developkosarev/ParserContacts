<?php

namespace AppBundle\Service\InvestorsParser\Anwalt;

use AppBundle\Service\InvestorsParser\AbstractParser;
use AppBundle\Service\InvestorsParser\LinkProcessedRepository;
use AppBundle\Service\InvestorsParser\LinkRepository;

use AppBundle\Entity\Parsers\Investor;
use Doctrine\Common\Persistence\ObjectManager;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

class ParserAnwalt extends AbstractParser
{
    const PARSER_ID = 2;

    private $objectManager;

    protected $investors;

    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;

        $this->linkProcess = new LinkRepository('AnwaltLinkProcess.csv');
        $this->linkProcessed = new LinkProcessedRepository('AnwaltLinkProcessed.csv');

        $this->crawler = new CrawlerAnwalt();
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
                $investor->setParserId(ParserLocal::PARSER_ID);
                $investor->setPhone($items['phone']);
                $investor->setName($items['name']);
                $investor->setSpecialization($items['specialization']);
                $investor->setAddress($items['address']);

                $this->insertOrUpdateInvestor($investor);
            }

        };

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
}
