<?php

namespace App\Entity;

use App\Repository\CompanyTurnoverRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyTurnoverRepository::class)]
class CompanyTurnover
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $year = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $non_current_assets = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $current_assets = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $equity_capital = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $amounts_payable_and_other_liabilities = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $sales_revenue = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $profit_loss_before_taxes = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $profit_before_taxes_margin = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $net_profit_loss = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $net_profit_margin = null;

    #[ORM\ManyToOne(inversedBy: 'company_turnover')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Company $company = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getNonCurrentAssets(): ?string
    {
        return $this->non_current_assets;
    }

    public function setNonCurrentAssets(?string $non_current_assets): static
    {
        $this->non_current_assets = $non_current_assets;

        return $this;
    }

    public function getCurrentAssets(): ?string
    {
        return $this->current_assets;
    }

    public function setCurrentAssets(?string $current_assets): static
    {
        $this->current_assets = $current_assets;

        return $this;
    }

    public function getEquityCapital(): ?string
    {
        return $this->equity_capital;
    }

    public function setEquityCapital(?string $equity_capital): static
    {
        $this->equity_capital = $equity_capital;

        return $this;
    }

    public function getAmountsPayableAndOtherLiabilities(): ?string
    {
        return $this->amounts_payable_and_other_liabilities;
    }

    public function setAmountsPayableAndOtherLiabilities(?string $amounts_payable_and_other_liabilities): static
    {
        $this->amounts_payable_and_other_liabilities = $amounts_payable_and_other_liabilities;

        return $this;
    }

    public function getSalesRevenue(): ?string
    {
        return $this->sales_revenue;
    }

    public function setSalesRevenue(?string $sales_revenue): static
    {
        $this->sales_revenue = $sales_revenue;

        return $this;
    }

    public function getProfitLossBeforeTaxes(): ?string
    {
        return $this->profit_loss_before_taxes;
    }

    public function setProfitLossBeforeTaxes(?string $profit_loss_before_taxes): static
    {
        $this->profit_loss_before_taxes = $profit_loss_before_taxes;

        return $this;
    }

    public function getProfitBeforeTaxesMargin(): ?string
    {
        return $this->profit_before_taxes_margin;
    }

    public function setProfitBeforeTaxesMargin(?string $profit_before_taxes_margin): static
    {
        $this->profit_before_taxes_margin = $profit_before_taxes_margin;

        return $this;
    }

    public function getNetProfitLoss(): ?string
    {
        return $this->net_profit_loss;
    }

    public function setNetProfitLoss(?string $net_profit_loss): static
    {
        $this->net_profit_loss = $net_profit_loss;

        return $this;
    }

    public function getNetProfitMargin(): ?string
    {
        return $this->net_profit_margin;
    }

    public function setNetProfitMargin(?string $net_profit_margin): static
    {
        $this->net_profit_margin = $net_profit_margin;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): static
    {
        $this->company = $company;

        return $this;
    }
}
