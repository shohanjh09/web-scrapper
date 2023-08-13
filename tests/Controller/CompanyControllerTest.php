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
    private string $path = '/company/';
    private EntityManagerInterface $manager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->repository = static::getContainer()->get('doctrine')->getRepository(Company::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Company index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $originalNumObjectsInRepository = count($this->repository->findAll());

        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'company[company_name]' => 'Testing',
            'company[registration_code]' => 'Testing',
            'company[vat]' => 'Testing',
            'company[address]' => 'Testing',
            'company[mobile_phone]' => 'Testing',
        ]);

        self::assertResponseRedirects('/company/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Company();
        $fixture->setCompany_name('My Title');
        $fixture->setRegistration_code('My Title');
        $fixture->setVat('My Title');
        $fixture->setAddress('My Title');
        $fixture->setMobile_phone('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Company');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Company();
        $fixture->setCompany_name('My Title');
        $fixture->setRegistration_code('My Title');
        $fixture->setVat('My Title');
        $fixture->setAddress('My Title');
        $fixture->setMobile_phone('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'company[company_name]' => 'Something New',
            'company[registration_code]' => 'Something New',
            'company[vat]' => 'Something New',
            'company[address]' => 'Something New',
            'company[mobile_phone]' => 'Something New',
        ]);

        self::assertResponseRedirects('/company/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getCompany_name());
        self::assertSame('Something New', $fixture[0]->getRegistration_code());
        self::assertSame('Something New', $fixture[0]->getVat());
        self::assertSame('Something New', $fixture[0]->getAddress());
        self::assertSame('Something New', $fixture[0]->getMobile_phone());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();

        $originalNumObjectsInRepository = count($this->repository->findAll());

        $fixture = new Company();
        $fixture->setCompany_name('My Title');
        $fixture->setRegistration_code('My Title');
        $fixture->setVat('My Title');
        $fixture->setAddress('My Title');
        $fixture->setMobile_phone('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        self::assertSame($originalNumObjectsInRepository + 1, count($this->repository->findAll()));

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertSame($originalNumObjectsInRepository, count($this->repository->findAll()));
        self::assertResponseRedirects('/company/');
    }
}
