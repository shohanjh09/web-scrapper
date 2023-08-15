<?php

namespace App\Test\Controller;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CompanyControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private CompanyRepository $repository;
    private string $path = '/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Company::class);
        $this->manager = static::getContainer()->get(EntityManagerInterface::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Company Database');
    }

    public function testNew(): void
    {
        $companyCount = count($this->repository->findAll());

        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'company[company_name]' => 'Testing',
            'company[registration_code]' => 'Testing',
            'company[vat]' => 'Testing',
            'company[address]' => 'Testing',
            'company[mobile_phone]' => 'Testing',
        ]);

        self::assertResponseRedirects('/');

        self::assertSame($companyCount + 1, count($this->repository->findAll()));
    }

    public function testEdit(): void
    {
        $company = new Company();
        $company->setCompanyName('My Title');
        $company->setRegistrationCode('My Title');
        $company->setVat('My Title');
        $company->setAddress('My Title');
        $company->setMobilePhone('My Title');

        $this->manager->persist($company);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $company->getId()));

        $this->client->submitForm('Update', [
            'company[company_name]' => 'Something New',
            'company[registration_code]' => 'Something New',
            'company[vat]' => 'Something New',
            'company[address]' => 'Something New',
            'company[mobile_phone]' => 'Something New',
        ]);

        self::assertResponseRedirects('/');

        $company = $this->repository->findAll();

        self::assertSame('Something New', $company[0]->getCompanyName());
        self::assertSame('Something New', $company[0]->getRegistrationCode());
        self::assertSame('Something New', $company[0]->getVat());
        self::assertSame('Something New', $company[0]->getAddress());
        self::assertSame('Something New', $company[0]->getMobilePhone());
    }

    public function testRemove(): void
    {
        $companyCount = count($this->repository->findAll());

        $company = new Company();
        $company->setCompanyName('My Title');
        $company->setRegistrationCode('My Title');
        $company->setVat('My Title');
        $company->setAddress('My Title');
        $company->setMobilePhone('My Title');

        $this->manager->persist($company);
        $this->manager->flush();

        self::assertSame($companyCount + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $company->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($companyCount, count($this->repository->findAll()));
        self::assertResponseRedirects('/');
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }
}
