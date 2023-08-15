<?php

namespace App\Tests\Repository;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Log\NullLogger;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CompanyRepositoryTest extends KernelTestCase
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * @var NullLogger
     */
    private $logger;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $this->logger = new NullLogger();

        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->paginator = $container->get(PaginatorInterface::class);
    }

    public function testCountStatusByRegistrationCodes()
    {
        $companyRepository = $this->entityManager->getRepository(Company::class);

        $registrationCodes = ['ABC123', 'DEF456'];
        foreach ($registrationCodes as $registrationCode) {
            $company = new Company();
            $company->setCompanyName("Comapny Name - ".$registrationCode);
            $company->setRegistrationCode($registrationCode);
            $company->setStatus('completed');
            $this->entityManager->persist($company);
        }
        $this->entityManager->flush();

        $statusCounts = $companyRepository->countStatusByRegistrationCodes($registrationCodes);

        $this->assertEquals(['completed' => count($registrationCodes)], $statusCounts);
    }

    public function testGetCompletedCompanyByRegistrationCode()
    {
        $companyRepository = $this->entityManager->getRepository(Company::class);

        $registrationCodes = ['ABC123', 'DEF456'];
        foreach ($registrationCodes as $registrationCode) {
            $company = new Company();
            $company->setCompanyName("Comapny Name - ".$registrationCode);
            $company->setRegistrationCode($registrationCode);
            $company->setStatus('completed');
            $this->entityManager->persist($company);
        }
        $this->entityManager->flush();

        $page = 1;
        $limit = 10;
        $pagination = $companyRepository->getCompletedCompanyByRegistrationCode($registrationCodes, $page, $limit);

        $this->assertCount(count($registrationCodes), $pagination);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->removeTestData();
        $this->entityManager->close();
        $this->entityManager = null;
    }

    private function removeTestData()
    {
        $this->entityManager->getConnection()->executeStatement('DELETE FROM company');
    }
}
