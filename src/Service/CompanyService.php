<?php

namespace App\Service;

use App\Entity\Company;
use App\Entity\CompanyTurnover;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;

class CompanyService
{
    private $entityManager;
    private $companyRepository;

    public function __construct(EntityManagerInterface $entityManager, CompanyRepository $companyRepository)
    {
        $this->entityManager = $entityManager;
        $this->companyRepository = $companyRepository;
    }

    public function findCompanyByCriteria(array $criteria): array
    {
        return $this->companyRepository->findBy($criteria, ['id' => 'DESC']);
    }

    public function findCompanyAll(): array
    {
        return $this->companyRepository->findBy([], ['id' => 'DESC']);
    }

    public function findCompanyById($id)
    {
        return $this->companyRepository->find($id);
    }

    public function removeCompanyByRegistrationCode($registrationCode)
    {
        $company = $this->companyRepository->findOneBy(['registration_code' => $registrationCode]);

        if ($company) {
            $this->entityManager->remove($company);
            $this->entityManager->flush();
        }
    }

    public function countStatusByRegistrationCodes(array $registrationCodes){
        return $this->companyRepository->countStatusByRegistrationCodes($registrationCodes);
    }

    public function getActiveCompanyByRegistrationCode(array $registrationCodes, $page, $limit){
        return $this->companyRepository->getCompletedCompanyByRegistrationCode($registrationCodes, $page, $limit);
    }

    public function createOrUpdateCompanyAndTurnoverInformation(array $scrapedData): void
    {
        // Update Company entity and set its properties with scrapping information
        $registrationCode = $scrapedData['company_details']['registration_code'];

        $company = $this->companyRepository->findOneBy(['registration_code' => $registrationCode]);

        if (!$company) {
            // Create a new Company entity if not found
            $company = new Company();
            $company->setRegistrationCode($registrationCode);
        }

        $this->updateCompanyFromScrapedData($company, $scrapedData['company_details']);
        $this->updateTurnoversFromScrapedData($company, $scrapedData['company_turnover']);

        // Persist the Company entity and its associated CompanyTurnover entities
        $this->entityManager->persist($company);
        $this->entityManager->flush();
    }

    private function updateCompanyFromScrapedData(Company $company, array $scrapedCompanyData): void
    {
        $company->setStatus('completed');
        $company->setCompanyName($scrapedCompanyData['company_name']);
        $company->setVat($scrapedCompanyData['vat']);
        $company->setAddress($scrapedCompanyData['address']);
        $company->setMobilePhone($scrapedCompanyData['mobile_phone']);
    }

    private function updateTurnoversFromScrapedData(Company $company, array $scrapedTurnoverData): void
    {
        foreach ($scrapedTurnoverData['year'] as $index => $year) {
            $companyTurnover = new CompanyTurnover();
            $companyTurnover->setYear($year);
            $companyTurnover->setNonCurrentAssets($scrapedTurnoverData['non_current_assets'][$index]);
            $companyTurnover->setCurrentAssets($scrapedTurnoverData['current_assets'][$index]);
            $companyTurnover->setEquityCapital($scrapedTurnoverData['equity_capital'][$index]);
            $companyTurnover->setAmountsPayableAndOtherLiabilities($scrapedTurnoverData['amounts_payable_and_other_liabilities'][$index]);
            $companyTurnover->setSalesRevenue($scrapedTurnoverData['sales_revenue'][$index]);
            $companyTurnover->setProfitLossBeforeTaxes($scrapedTurnoverData['profit_loss_before_taxes'][$index]);
            $companyTurnover->setProfitBeforeTaxesMargin($scrapedTurnoverData['profit_before_taxes_margin'][$index]);
            $companyTurnover->setNetProfitLoss($scrapedTurnoverData['net_profit_loss'][$index]);
            $companyTurnover->setNetProfitMargin($scrapedTurnoverData['net_profit_margin'][$index]);

            $company->addCompanyTurnover($companyTurnover);
        }
    }
}
