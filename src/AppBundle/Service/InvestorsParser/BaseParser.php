<?php

namespace AppBundle\Service\InvestorsParser;

use Doctrine\ORM\EntityManagerInterface;

use AppBundle\Entity\Parsers\Investor;
use AppBundle\Entity\Parsers\ParseUrl;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;

class BaseParser
{
    private $entityManager;
    private $currentParserUrl;
    private $currentStatus;

    protected $pagesProcessed = 0;

    protected $url;
    protected $parserId = 0;
    protected $limit = 1;
    protected $sleep = 1;
    protected $port = 9050;

    protected $pages = 0;

    protected $crawler = null;
    protected $client = null;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    protected function getBody($url)
    {
        $userAgentArray = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.132 Safari/537.36',
            'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:64.0) Gecko/20100101 Firefox/64.0',
            'Mozilla/5.0 (Windows NT 6.2; WOW64) AppleWebKit/537.36 (KHTML like Gecko) Chrome/44.0.2403.155 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.1 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML like Gecko) Chrome/51.0.2704.79 Safari/537.36 Edge/14.14931',
            'Chrome (AppleWebKit/537.1; Chrome50.0; Windows NT 6.3) AppleWebKit/537.36 (KHTML like Gecko) Chrome/51.0.2704.79 Safari/537.36 Edge/14.14393'
        ];

        $userAgent = $userAgentArray[ rand(0,5) ];

        $headers = [
            'Accept' => 'text/html',
            'User-agent' => $userAgent,
            'Content-Type' => 'text/html; charset=UTF-8',
            'Referer' => 'https://www.google.com/',
            'Connection' => 'Keep-Alive'
        ];

        $html = '';
        $message = '';

        try {
            $response = $this->client->get($url,['header' => $headers]);
            $html = $response->getBody()->getContents();
            $this->currentStatus = $response->getStatusCode();
            //$message = $response->getMessage();
        } catch (ClientException $e) {
            $this->currentStatus = $e->getCode();
            //$message = $e->getMessage();

            //echo Psr7\str($e->getRequest());
            //echo Psr7\str($e->getResponse());

        } catch (RequestException $e) {
            $this->currentStatus = $e->getCode();
            //$message = $e->getMessage();

            //echo Psr7\str($e->getRequest());
            //if ($e->hasResponse()) {
            //    echo Psr7\str($e->getResponse());
            //}
        } catch (Exception $e) {
            $this->currentStatus = $e->getCode();
            //$message = $e->getMessage();

        }

        print_r($this->currentStatus);
        print_r($url);


        $result = [
            'html' => $html,
            'status' => $this->currentStatus,
            'message' => $message
        ];

        return $result;
    }

    protected function parse($parseItem)
    {
        return null;
    }

    public function init($url, $limit = 1, $sleep = 1, $port = 9050, $crawlerName = 'HomePage')
    {
        $this->limit = $limit; //5
        $this->sleep = $sleep; //1
        $this->url = $url;
        $this->port = $port;

        $this->client = new Client([
            'base_uri' => $this->url,
            'timeout'  => 5.0,
            'proxy' => 'socks5://127.0.0.1:' . $port
        ]);

        $parseUrlRepository = $this->entityManager->getRepository(ParseUrl::class);
        $entity = $parseUrlRepository->findOneBy([
            'parserId' => $this->parserId,
            'url' => $this->url
        ]);

        if ($entity === null) {
            $parseUrl = new ParseUrl();
            $parseUrl->setParserId($this->parserId);
            $parseUrl->setCrawlerName($crawlerName);
            $parseUrl->setUrl($this->url);
            $parseUrl->setPort($this->port);

            $this->entityManager->persist($parseUrl);
            $this->entityManager->flush();
        }
    }

    public function run()
    {
        $parseUrls = $this->entityManager->getRepository(ParseUrl::class)
            ->findBy(['parserId' => $this->parserId,
                      'port' => $this->port,
                      'status' => null,
                      'crawlerName' => 'getLawyers']);

        foreach ($parseUrls as $item)
        {
            $this->currentParserUrl = $item;

            $body = $this->parse($this->currentParserUrl);
            $this->pagesProcessed++;

            $this->updateStatus($body);

            if ($this->limit && $this->pagesProcessed >= $this->limit)
                return;

            sleep($this->sleep);
        }
    }

    public function next()
    {
        $this->entityManager->getConnection()->beginTransaction(); // suspend auto-commit
        try {
            $this->currentParserUrl = $this->entityManager->getRepository(ParseUrl::class)
                ->findOneBy(['parserId' => $this->parserId,
                    'port' => null,
                    'status' => null]);

            if ($this->currentParserUrl == null)
            {
                $this->entityManager->getConnection()->rollBack();
                return false;
            }

            $this->currentParserUrl->setPort($this->port);

            $this->entityManager->persist($this->currentParserUrl);
            $this->entityManager->flush();


            $this->entityManager->getConnection()->commit();
        } catch (Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            return false;
        }


        $body = $this->parse($this->currentParserUrl);
        $this->pagesProcessed++;

        $this->updateStatus($body);

        if ($this->limit && $this->pagesProcessed >= $this->limit)
            return false;

        return true;
    }

    protected function addLink($url, $crawler)
    {
        if ($this->isAllowLink($url) )
        {
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
                $parseUrl->setPort($this->port);

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


    public function getCurrentParserUrl()
    {
        return $this->currentParserUrl;
    }

    public function getCurrentStatus()
    {
        return $this->currentStatus;
    }

    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    public function getSleep()
    {
        return $this->sleep;
    }

    public function setSleep($sleep)
    {
        $this->sleep = $sleep;
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

    public function updateStatus($body)
    {
        $html = $body['html'];
        $status = $body['status'];

        if ($status == 200 )
        {
            //$root = $this->container->get('kernel')->getRootDir();
            //$root = __DIR__ . '/../../../';
            $root = 'D:/';
            $file = $root . 'storage/' . $this->currentParserUrl->getId() . '.html';

            file_put_contents($file, $html);

            $this->currentParserUrl->setStatus($status);
            $this->currentParserUrl->setFileName($file);

            $this->entityManager->persist($this->currentParserUrl);
            $this->entityManager->flush();
        }

    }
}