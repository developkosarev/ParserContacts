<?php

namespace AppBundle\Service\InvestorsParser;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Parsers\Investor;
use AppBundle\Entity\Parsers\ParseUrl;

use GuzzleHttp\Client;

class AbstractParser
{
    private $entityManager;

    protected $linkProcess;

    protected $linkProcessed;
    protected $pagesProcessed = 0;

    protected $url;
    protected $parserId = 0;
    protected $limit = 1;
    protected $sleep = 1;
    protected $pages = 0;

    protected $crawler = null;
    protected $client = null;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getBody($url) //abstract
    {
        return null;
    }

    protected function parse($url, $nameCrawler) //abstract
    {
        return null;
    }

    public function init($url, $limit = 1, $sleep = 1)
    {
        $this->limit = $limit; //5
        $this->sleep = $sleep; //1
        $this->url = $url;

        $this->client = new Client([
            'base_uri' => $this->url,
            'timeout'  => 5.0,
        ]);

        //$this->linkProcess->add($this->url, 'HomePage');

        $parseUrlRepository = $this->entityManager->getRepository(ParseUrl::class);
        $entity = $parseUrlRepository->findOneBy([
            'parserId' => $this->parserId,
            'url' => $this->url
        ]);

        if ($entity === null) {
            $parseUrl = new ParseUrl();
            $parseUrl->setParserId($this->parserId);
            $parseUrl->setCrawlerName('HomePage');
            $parseUrl->setUrl($this->url);

            $this->entityManager->persist($parseUrl);
            $this->entityManager->flush();
        }
    }

    public function runNew()
    {
        $parseUrls = $this->entityManager->getRepository(ParseUrl::class)
            ->findBy(['parserId' => $this->parserId]);

        foreach ($parseUrls as $item)
        {
            if ($item->getStatus() != 0) {
                continue;
            }

            $status = $this->parse($item->getUrl(), $item->getCrawlerName());
            $this->pagesProcessed++;

            //$this->linkProcessed->add($url, $status);
            $this->updateStatus($item, $status);

            if ($this->limit && $this->pagesProcessed >= $this->limit)
                return;
        }
    }

    public function run()
    {
        while ($this->pages < $this->linkProcessCount() )
        {
            $url = $this->linkProcess->getUrl($this->pages);
            $nameCrawlers = $this->linkProcess->getNameCrawler($this->pages);

            if ($this->linkProcessed->exist($url)) {
                $this->pages++;
                continue;
            }

            $status = $this->parse($url, $nameCrawlers);
            $this->pages++;
            $this->pagesProcessed++;

            $this->linkProcessed->add($url, $status);

            if ($this->limit && $this->pagesProcessed >= $this->limit)
                return;

            sleep($this->sleep);
        }
    }

    protected function addLink($url, $crawler)
    {
        if ($this->isAllowLink($url) )
        {
            //$this->linkProcess->add($url, $crawler);

            $parseUrlRepository = $this->entityManager->getRepository(ParseUrl::class);
            $entity = $parseUrlRepository->findOneBy([
                'parserId' => $this->parserId,
                'url' => $url
            ]);

            if ($entity === null) {
                $parseUrl = new ParseUrl();
                $parseUrl->setParserId($this->parserId);
                $parseUrl->setCrawlerName($crawler);
                $parseUrl->setUrl($url);

                $this->entityManager->persist($parseUrl);
                $this->entityManager->flush();
            }
        }
    }

    protected function addLinks($items)
    {
        foreach ($items as $item)
        {
            $url = $item['href'];
            $crawler = $item['crawler'];

            $this->addLink($url, $crawler);
        }
    }

    protected function isAllowLink($url): bool
    {
        return true;
    }


    public function getAllLinks()
    {
        return $this->linkProcess->getLinks();
    }

    public function getCrawlers()
    {
        return $this->linkProcess->getNameCrawlers();
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function setSleep($sleep)
    {
        $this->sleep = $sleep;
    }

    public function linkProcessCount()
    {
        return $this->linkProcess->count();
    }

    public function insertOrUpdateInvestor(Investor $investor)
    {
        $investorRepository = $this->entityManager->getRepository(Investor::class);
        $entity = $investorRepository->findOneBy([
            'parserId' => $investor->getParserId(),
            'phone' => $investor->getPhone()
        ]);

        if ($entity === null) {
            $this->entityManager->persist($investor);
            $this->entityManager->flush();
        }
    }

    public function updateStatus(ParseUrl $parseUrl, $status)
    {
        $parseUrl->setStatus($status);

        $this->entityManager->persist($parseUrl);
        $this->entityManager->flush();
    }
}