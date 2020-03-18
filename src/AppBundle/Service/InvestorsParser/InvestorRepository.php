<?php

namespace AppBundle\Service\InvestorsParser;


class InvestorRepository
{
    private $phones = array();
    private $investors = array();
    private $fileName;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;

        $this->load();
    }

    public function add($phone, $name, $specialization, $address)
    {
        if (!empty($phone) && !in_array($phone, $this->phones) )
        {
            $currentDate = date("Y-m-d H:i:s");

            $this->phones[] = $phone;
            $this->investors[] = [$phone, $name, $specialization, $address, $currentDate];

            $this->save([$phone, $name, $specialization, $address, $currentDate]);
        }
    }

    private function loadInvestor($phone, $name, $specialization, $address, $currentDate)
    {
        if (!empty($phone) && !in_array($phone, $this->phones) )
        {
            $this->phones[] = $phone;
            $this->investors[] = [$phone, $name, $specialization, $address, $currentDate];
        }
    }

    private function load()
    {
        if (!file_exists($this->fileName)) {
            return;
        }

        $this->phones = array();
        $this->investors = array();

        $lines = file($this->fileName);
        foreach($lines as $line){
            $line = rtrim($line);
            $parts = explode(';', $line);

            $phone = $parts[0];
            $name = $parts[1];
            $specialization = $parts[2];
            $address = $parts[3];
            $currentDate = $parts[4];

            $this->loadInvestor($phone, $name, $specialization, $address, $currentDate);
        }
    }

    private function save($investor)
    {
        $str = implode(';', $investor) . "\n";

        $f = fopen($this->fileName, 'a');
        fwrite($f, $str);
        fclose($f);
    }
}