<?php

namespace App\Entity;

use App\Repository\VehicleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VehicleRepository::class)]
class Vehicle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $spz = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $note = null;

    #[ORM\Column(nullable: true, options: ['default' => false])]
    private ?bool $isDeactivated = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSpz(): ?string
    {
        return $this->spz;
    }

    public function setSpz(string $spz): static
    {
        $this->spz = $spz;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function isDeactivated(): ?bool
    {
        return $this->isDeactivated;
    }

    public function setIsDeactivated(bool $isDeactivated): static
    {
        $this->isDeactivated = $isDeactivated;

        return $this;
    }
}
