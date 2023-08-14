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

    public function createCompanyWithTurnover(array $data): Company
    {
        // Create a new Company entity and set its properties
        $company = new Company();
        $company->setCompanyName($data['company_details']['company_name']);
        $company->setRegistrationCode($data['company_details']['registration_code']);
        $company->setVat($data['company_details']['vat']);
        $company->setAddress($data['company_details']['address']);
        $company->setMobilePhone($data['company_details']['mobile_phone']);

        // Create and associate CompanyTurnover entities with the Company
        foreach ($data['company_turnover']['year'] as $index => $year) {
            $companyTurnover = new CompanyTurnover();
            $companyTurnover->setYear($year);
            $companyTurnover->setNonCurrentAssets($data['company_turnover']['non_current_assets'][$index]);
            $companyTurnover->setCurrentAssets($data['company_turnover']['current_assets'][$index]);
            $companyTurnover->setEquityCapital($data['company_turnover']['equity_capital'][$index]);
            $companyTurnover->setAmountsPayableAndOtherLiabilities($data['company_turnover']['amounts_payable_and_other_liabilities'][$index]);
            $companyTurnover->setSalesRevenue($data['company_turnover']['sales_revenue'][$index]);
            $companyTurnover->setProfitLossBeforeTaxes($data['company_turnover']['profit_loss_before_taxes'][$index]);
            $companyTurnover->setProfitBeforeTaxesMargin($data['company_turnover']['profit_before_taxes_margin'][$index]);
            $companyTurnover->setNetProfitLoss($data['company_turnover']['net_profit_loss'][$index]);
            $companyTurnover->setNetProfitMargin($data['company_turnover']['net_profit_margin'][$index]);

            $company->addCompanyTurnover($companyTurnover);
        }

        // Persist the Company entity and its associated CompanyTurnover entities
        $this->entityManager->persist($company);
        $this->entityManager->flush();

        return $company;
    }
}
