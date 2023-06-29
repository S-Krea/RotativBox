<?php

namespace App\Entity;

use App\Repository\SettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SettingsRepository::class)]
class Settings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $maintenanceCost = null;

    #[ORM\Column(length: 255)]
    private ?string $dacOptionPrice = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMaintenanceCost(): ?string
    {
        return $this->maintenanceCost;
    }

    public function setMaintenanceCost(?string $maintenanceCost): self
    {
        $this->maintenanceCost = $maintenanceCost;

        return $this;
    }

    public function getDacOptionPrice(): ?string
    {
        return $this->dacOptionPrice;
    }

    public function setDacOptionPrice(string $dacOptionPrice): self
    {
        $this->dacOptionPrice = $dacOptionPrice;

        return $this;
    }
}
