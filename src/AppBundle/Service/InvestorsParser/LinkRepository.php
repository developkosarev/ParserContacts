<?php

namespace AppBundle\Service\InvestorsParser;


class LinkRepository
{
    private $links = array();
    private $nameCrawlers = array();

    private $fileName;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;

        $this->load();
    }

    public function add($url, $nameCrawler)
    {
        if (!empty($url) && !in_array($url, $this->links) )
        {
            $this->links[] = $url;
            $this->nameCrawlers[] = $nameCrawler;

            $this->save($url, $nameCrawler);
        }
    }

    public function addRange($items)
    {
        foreach ($items as $item)
        {
            $url = $item['href'];
            $crawler = $item['crawler'];

            $this->add($url, $crawler);
        }
    }

    #region Getters

    public function getUrl($index)
    {
        return $this->links[$index];
    }

    public function getNameCrawler($index)
    {
        return $this->nameCrawlers[$index];
    }

    public function getLinks()
    {
        return $this->links;
    }

    public function getNameCrawlers()
    {
        return $this->nameCrawlers;
    }

    public function count()
    {
        return count($this->links);
    }

    #endregion

    private function loadLink($url, $nameCrawler)
    {
        if (!empty($url) && !in_array($url, $this->links) )
        {
            $this->links[] = $url;
            $this->nameCrawlers[] = $nameCrawler;
        }
    }

    private function load()
    {
        if (!file_exists($this->fileName)) {
            return;
        }

        $this->links = array();
        $this->nameCrawlers = array();

        $lines = file($this->fileName);
        foreach($lines as $line){
            $line = rtrim($line);
            $parts = explode(';', $line);

            $url = $parts[0];
            $nameCrawler = $parts[1];

            $this->loadLink($url, $nameCrawler);
        }
    }

    private function save($url, $nameCrawler)
    {
        $str = $url . ';' . $nameCrawler . ';' . "\n";

        $f = fopen($this->fileName, 'a');
        fwrite($f, $str);
        fclose($f);
    }
}