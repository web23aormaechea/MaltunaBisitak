<?php

namespace App\Entity;

use App\Repository\BisitaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BisitaRepository::class)]
class Bisita
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Izena = null;

    #[ORM\Column(length: 255)]
    private ?string $Nondik = null;

    #[ORM\Column]
    private ?bool $Itxita = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIzena(): ?string
    {
        return $this->Izena;
    }

    public function setIzena(string $Izena): static
    {
        $this->Izena = $Izena;

        return $this;
    }

    public function getNondik(): ?string
    {
        return $this->Nondik;
    }

    public function setNondik(string $Nondik): static
    {
        $this->Nondik = $Nondik;

        return $this;
    }

    public function isItxita(): ?bool
    {
        return $this->Itxita;
    }

    public function setItxita(bool $Itxita): static
    {
        $this->Itxita = $Itxita;

        return $this;
    }
}
