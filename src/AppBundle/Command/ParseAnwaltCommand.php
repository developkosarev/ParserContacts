<?php

namespace AppBundle\Command;

use AppBundle\Service\InvestorsParser\Anwalt\ParserAnwalt;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class ParseAnwaltCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('app:parse-anwalt')
            ->setDescription('Command parses site https://www.anwalt.de and make Investors.csv file with contacts')
            ->setHelp('This command parses site...')
            ->addArgument('filename', InputArgument::REQUIRED, 'The file name .csv ');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Parser https://www.anwalt.de',
            '============',
            '',
        ]);

        //$p = new ParserAnwalt('https://www.anwalt.de', 120, 3, 'HomePage');
        //$p = new ParserAnwalt('https://www.anwalt.de/rechtsanwalt/berlin.php', 6, 3, 'getLawyers');
        //$p->run();
    }
}