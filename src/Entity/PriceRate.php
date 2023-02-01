<?php

namespace App\Entity;

use App\Repository\PriceRateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PriceRateRepository::class)]
class PriceRate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $rate = null;

    #[ORM\Column]
    private ?int $months = null;

    #[ORM\Column(length: 50)]
    private ?string $financingMode = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRate(): ?string
    {
        return $this->rate;
    }

    public function setRate(string $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getMonths(): ?int
    {
        return $this->months;
    }

    public function setMonths(int $months): self
    {
        $this->months = $months;

        return $this;
    }

    public function getFinancingMode(): ?string
    {
        return $this->financingMode;
    }

    public function setFinancingMode(string $financingMode): self
    {
        $this->financingMode = $financingMode;

        return $this;
    }
}
