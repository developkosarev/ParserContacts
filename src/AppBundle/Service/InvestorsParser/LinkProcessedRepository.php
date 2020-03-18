<?php

namespace AppBundle\Service\InvestorsParser;


class LinkProcessedRepository
{
    private $links = array();
    private $fileName;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;

        $this->load();
    }

    public function add($url, $status)
    {
        if (!empty($url) && !in_array($url, $this->links) )
        {
            $this->links[] = $url;

            $this->save($url, $status);
        }
    }

    public function exist($url)
    {
        if ( empty($url) ){
            return true;
        }

        return in_array($url, $this->links);
    }


    private function loadLink($url)
    {
        if (!empty($url) && !in_array($url, $this->links) )
        {
            $this->links[] = $url;
        }
    }

    private function load()
    {
        if (!file_exists($this->fileName)) {
            return;
        }

        $this->links = array();

        $lines = file($this->fileName);
        foreach($lines as $line){
            $line = rtrim($line);
            $parts = explode(';', $line);

            $url = $parts[0];

            $this->loadLink($url);
        }
    }

    private function save($url, $status)
    {
        $str = $url . ';' . $status . ';' . "\n";

        $f = fopen($this->fileName, 'a');
        fwrite($f, $str);
        fclose($f);
    }
}