<?php

namespace App\Tests;

use App\Entity\Buyer;
use App\Kernel;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class APIWebTestCase extends WebTestCase
{
    const SAMPLE_PHONE = '600000000';

    const SAMPLE_CODE = '1111';

    const API_URL = '/api/buyer/v1';

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var KernelBrowser
     */
    private KernelBrowser $testClient;

    public function setUp(): void
    {
        $this->testClient = static::createClient();

        $this->entityManager = static::$kernel->getContainer()->get('doctrine')->getManager();
    }

    protected function getTestClient()
    {
        return $this->testClient;
    }

    protected function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @return Buyer
     */
    protected function getSampleBuyer(): object
    {
        return $this->getEntityManager()->getRepository(Buyer::class)->findOneBy([
            'phone' => $this->getSamplePhone()
        ]);
    }

    protected function getSamplePhone(): string
    {
        return self::SAMPLE_PHONE;
    }

    protected function getSampleCode(): string
    {
        return self::SAMPLE_CODE;
    }

}