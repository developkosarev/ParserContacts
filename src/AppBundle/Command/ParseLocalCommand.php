<?php

namespace AppBundle\Command;

use AppBundle\Service\InvestorsParser\Local\ParserLocal;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class ParseLocalCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        //php bin/console app:parse-local
        $this
            ->setName('app:parse-local')
            ->setDescription('Command parses site http://local004.local and make Investors.csv file with contacts')
            ->setHelp('This command parses site...')
            ->addArgument('filename', InputArgument::REQUIRED, 'The file name .csv ');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Parser local004.local',
            '============',
            '',
        ]);

        $p = new ParserLocal($this->entityManager);
        $p->init('http://local004.local', 0, 0);
        //$p->run();

        while ($p->next()) {
            $output->writeln('Url: ' . $p->getCurrentParserUrl()->getId() . ' ' . $p->getCurrentParserUrl()->getStatus());

            //sleep($p->getSleep());
        }

    }
}