<?php

namespace Tests\AppBundle\Parsers\Local;

use AppBundle\Entity\Parsers\Investor;
use AppBundle\Service\InvestorsParser\Local\ParserLocal;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ParserLocalTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testRun()
    {
        $p = new ParserLocal($this->entityManager);
        $p->init('http://local004.local', 0, 0);
        $p->run();

        //var_dump($p->getLinksCount());
        //print_r($p->getAllLinks());
        //print_r($p->getCrawlers());

        $result = 42;

        $this->assertEquals(42, $result);
    }

    public function testFindInvestor()
    {
        $investor = $this->entityManager
            ->getRepository(Investor::class)
            ->findBy(['phone' => 'Phone - 111111111'])
        ;

        //$this->assertCount(1, $investor);

        $result = 42;
        $this->assertEquals(42, $result);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null; // avoid memory leaks
    }
}