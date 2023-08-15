<?php
namespace App\Tests\Service;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Service\CompanyService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class CompanyServiceTest extends TestCase
{
    private $entityManagerMock;
    private $companyRepositoryMock;
    private $companyService;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $this->companyRepositoryMock = $this->createMock(CompanyRepository::class);

        $this->companyService = new CompanyService(
            $this->entityManagerMock,
            $this->companyRepositoryMock
        );
    }

    public function testCreateOrUpdateCompanyAndTurnoverInformation()
    {
        $scrapedData = $this->getCompanyScrappedData();

        $companyMock = $this->createMock(Company::class);
        $this->companyRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->willReturn($companyMock);

        $this->entityManagerMock
            ->expects($this->once())
            ->method('persist');

        $this->entityManagerMock
            ->expects($this->once())
            ->method('flush');

        $this->companyService->createOrUpdateCompanyAndTurnoverInformation($scrapedData);
    }

    public function testFindCompanyByCriteria()
    {
        $criteria = ['status' => 'completed'];

        $this->companyRepositoryMock
            ->expects($this->once())
            ->method('findBy')
            ->with($criteria, ['id' => 'DESC'])
            ->willReturn([]);

        $result = $this->companyService->findCompanyByCriteria($criteria);

        $this->assertEquals([], $result);
    }

    public function testFindCompanyAll()
    {
        $this->companyRepositoryMock
            ->expects($this->once())
            ->method('findBy')
            ->with([], ['id' => 'DESC'])
            ->willReturn([]);

        $result = $this->companyService->findCompanyAll();

        $this->assertEquals([], $result);
    }

    public function testFindCompanyById()
    {
        $companyId = 1;

        $this->companyRepositoryMock
            ->expects($this->once())
            ->method('find')
            ->with($companyId)
            ->willReturn(null);

        $result = $this->companyService->findCompanyById($companyId);

        $this->assertNull($result);
    }

    public function testRemoveCompanyByRegistrationCode()
    {
        $registrationCode = 'ABC123';
        $companyMock = $this->createMock(Company::class);

        $this->companyRepositoryMock
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['registration_code' => $registrationCode])
            ->willReturn($companyMock);

        $this->entityManagerMock
            ->expects($this->once())
            ->method('remove');

        $this->entityManagerMock
            ->expects($this->once())
            ->method('flush');

        $this->companyService->removeCompanyByRegistrationCode($registrationCode);
    }

    public function testCountStatusByRegistrationCodes()
    {
        $registrationCodes = ['ABC123', 'XYZ456'];
        $statusCounts = ['completed' => 2];

        $this->companyRepositoryMock
            ->expects($this->once())
            ->method('countStatusByRegistrationCodes')
            ->with($registrationCodes)
            ->willReturn($statusCounts);

        $result = $this->companyService->countStatusByRegistrationCodes($registrationCodes);

        $this->assertEquals($statusCounts, $result);
    }

    private function getCompanyScrappedData(){
        return [
            'company_details' => [
                'registration_code' => 'ABC123',
                'company_name' => 'Example Company',
                'vat' => 'L122323',
                'address' => 'Totori Kaunas',
                'mobile_phone' => '',
            ],
            'company_turnover' => [
                'year'                                  => ['2020', '2021', '2022'],
                'non_current_assets'                    => ['0 €','0 €','7 826 €'],
                'current_assets'                        => ['6 602 €','16 050 €','21 154 €'],
                'equity_capital'                        => ['6 602 €','15 578 €','28 310 €'],
                'amounts_payable_and_other_liabilities' => ['','',''],
                'sales_revenue'                         => ['12 253 €','39 347 €','53 004 €'],
                'profit_loss_before_taxes'              => ['6 552 €','9 448 €','13 402 €'],
                'profit_before_taxes_margin'            => ['53,47 %','24,01 %','25,28 %'],
                'net_profit_loss'                       => ['6 552 €','8 976 €','12 732 €'],
                'net_profit_margin'                     => ['53,47 %','22,81 %','24,02 %']
            ]
        ];
    }
}
