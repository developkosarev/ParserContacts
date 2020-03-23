<?php

namespace AppBundle\Command;

use AppBundle\Service\InvestorsParser\Anwalt\ParserAnwalt;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class ParseAnwaltCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure()
    {
        //1//php bin/console app:parse-anwalt https://www.anwalt.de/rechtsanwalt/koeln.php 9050 getLawyers
        //2//php bin/console app:parse-anwalt https://www.anwalt.de/rechtsanwalt/frankfurt_am_main.php 9052 getLawyers
        //3//php bin/console app:parse-anwalt https://www.anwalt.de/rechtsanwalt/duesseldorf.php 9054 getLawyers
        //4//php bin/console app:parse-anwalt https://www.anwalt.de/rechtsanwalt/stuttgart.php 9056 getLawyers
        //5//php bin/console app:parse-anwalt https://www.anwalt.de/rechtsanwalt/nuernberg.php 9058 getLawyers
        //6//php bin/console app:parse-anwalt https://www.anwalt.de/rechtsanwalt/hannover.php 9060 getLawyers
        //7//php bin/console app:parse-anwalt https://www.anwalt.de/rechtsanwalt/bremen.php 9062 getLawyers
        //8//php bin/console app:parse-anwalt https://www.anwalt.de/rechtsanwalt/leipzig.php 9064 getLawyers
        //9//php bin/console app:parse-anwalt https://www.anwalt.de/rechtsanwalt/dresden.php 9066 getLawyers
        //10//php bin/console app:parse-anwalt https://www.anwalt.de/rechtsanwalt/dortmund.php 9068 getLawyers
        //11//php bin/console app:parse-anwalt https://www.anwalt.de/rechtsanwalt/essen.php 9070 getLawyers
        //12//php bin/console app:parse-anwalt https://www.anwalt.de/rechtsanwalt/karlsruhe.php 9072 getLawyers
        //13//php bin/console app:parse-anwalt https://www.anwalt.de/rechtsanwalt/wiesbaden.php 9074 getLawyers
        //14//php bin/console app:parse-anwalt https://www.anwalt.de/rechtsanwalt/bonn.php 9076 getLawyers
        //15//php bin/console app:parse-anwalt https://www.anwalt.de/rechtsanwalt/mannheim.php 9078 getLawyers
        //16//php bin/console app:parse-anwalt https://www.anwalt.de/rechtsanwalt/muenster.php 9080 getLawyers
        //17//php bin/console app:parse-anwalt https://www.anwalt.de/rechtsanwalt/augsburg.php 9082 getLawyers

        //php bin/console app:parse-anwalt https://www.anwalt.de 9050 HomePage


        $this
            ->setName('app:parse-anwalt')
            ->setDescription('Command parses site https://www.anwalt.de and make Investors.csv file with contacts')
            ->setHelp('This command parses site...')
            //->addArgument('filename', InputArgument::REQUIRED, 'The file name .csv ')
            ->addArgument('startPage', InputArgument::REQUIRED, 'start page tor ')
            ->addArgument('port', InputArgument::REQUIRED, 'port tor ')
            ->addArgument('crawler', InputArgument::REQUIRED, 'crawler');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Parser https://www.anwalt.de:' . $input->getArgument('port'),
            '============',
            '',
        ]);

        $p = new ParserAnwalt($this->entityManager);
        //$p->init('https://www.anwalt.de/rechtsanwalt/koeln.php', 1000, 30, $input->getArgument('port'), 'getLawyers');
        $p->init($input->getArgument('startPage'), 1000, 30, $input->getArgument('port'), $input->getArgument('crawler'));

        while ($p->next()) {
            $output->writeln('Url: ' . $p->getCurrentParserUrl()->getId() . ' ' . $p->getCurrentStatus());

            if ($p->getCurrentStatus() == 401 or $p->getCurrentStatus() == 402 or $p->getCurrentStatus() == 403)
            {
                break;
            }

            sleep(rand(30,40));
        }
    }
}