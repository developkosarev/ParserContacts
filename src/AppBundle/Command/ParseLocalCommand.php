<?php

namespace AppBundle\Command;

use AppBundle\Service\InvestorsParser\Local\ParserLocal;

use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\Parsers\Investor;

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
        $p->run();


        //$em = $this->entityManager;

        // A. Access repositories
        //$repo = $em->getRepository("App:SomeEntity");
        //$repo = $em->getRepository(Investor::class);

        // B. Search using regular methods.
        //$res1 = $repo->find(1);
        //$res2 = $repo->findBy(['field' => 'value']);
        //$res3 = $repo->findAll();
        //$res4 = $repo->createQueryBuilder('alias')
        //    ->where("alias.field = :fieldValue")
        //    ->setParameter("fieldValue", 123)
        //    ->setMaxResults(10)
        //    ->getQuery()
        //    ->getResult();

        // C. Persist and flush
        //$em->persist($someEntity);
        //$em->flush();

    }
}