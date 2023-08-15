<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ORM\Index(name:'idx_registration_code', columns:['registration_code'])]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $company_name = null;

    #[ORM\Column(length: 100)]
    private ?string $registration_code = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $vat = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mobile_phone = null;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: CompanyTurnover::class, orphanRemoval: true, cascade: ["persist"])]
    private Collection $company_turnover;

    #[ORM\Column(length:100, nullable:true, options: ["default" => 'completed'])]
    private ?string $status = 'completed';

    public function __construct()
    {
        $this->company_turnover = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompanyName(): ?string
    {
        return $this->company_name;
    }

    public function setCompanyName(string $company_name): static
    {
        $this->company_name = $company_name;

        return $this;
    }

    public function getRegistrationCode(): ?string
    {
        return $this->registration_code;
    }

    public function setRegistrationCode(string $registration_code): static
    {
        $this->registration_code = $registration_code;

        return $this;
    }

    public function getVat(): ?string
    {
        return $this->vat;
    }

    public function setVat(?string $vat): static
    {
        $this->vat = $vat;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getMobilePhone(): ?string
    {
        return $this->mobile_phone;
    }

    public function setMobilePhone(?string $mobile_phone): static
    {
        $this->mobile_phone = $mobile_phone;

        return $this;
    }

    /**
     * @return Collection<int, CompanyTurnover>
     */
    public function getCompanyTurnover(): Collection
    {
        return $this->company_turnover;
    }

    public function addCompanyTurnover(CompanyTurnover $companyTurnover): static
    {
        if (!$this->company_turnover->contains($companyTurnover)) {
            $this->company_turnover->add($companyTurnover);
            $companyTurnover->setCompany($this);
        }

        return $this;
    }

    public function removeCompanyTurnover(CompanyTurnover $companyTurnover): static
    {
        if ($this->company_turnover->removeElement($companyTurnover)) {
            // set the owning side to null (unless already changed)
            if ($companyTurnover->getCompany() === $this) {
                $companyTurnover->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * Get serialized turnover data
     *
     * @return array
     */
    public function getSerializedTurnover(): array
    {
        $serializedTurnover = [];

        foreach ($this->company_turnover as $turnover) {
            $serializedTurnover[] = [
                'year'                                  => $turnover->getYear(),
                'non_current_assets'                    => $turnover->getNonCurrentAssets(),
                'current_assets'                        => $turnover->getCurrentAssets(),
                'equity_capital'                        => $turnover->getEquityCapital(),
                'amounts_payable_and_other_liabilities' => $turnover->getAmountsPayableAndOtherLiabilities(),
                'sales_revenue'                         => $turnover->getSalesRevenue(),
                'profit_loss_before_taxes'              => $turnover->getProfitLossBeforeTaxes(),
                'profit_before_taxes_margin'            => $turnover->getProfitBeforeTaxesMargin(),
                'net_profit_loss'                       => $turnover->getNetProfitLoss(),
                'net_profit_margin'                     => $turnover->getNetProfitMargin()
            ];
        }

        return $serializedTurnover;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }
}
