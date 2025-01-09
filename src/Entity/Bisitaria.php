<?php

namespace App\Entity;

use App\Repository\BisitariaRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BisitariaRepository::class)]
class Bisitaria
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Izena = null;

    #[ORM\Column(length: 255)]
    private ?string $Abizena = null;

    #[ORM\Column(length: 255)]
    private ?string $Nondik = null;

    #[ORM\Column(length: 255)]
    private ?string $Email = null;

    #[ORM\ManyToOne]
    private ?Bilera $Bilera = null;

    #[ORM\ManyToOne]
    private ?Bisita $Bisita = null;

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

    public function getAbizena(): ?string
    {
        return $this->Abizena;
    }

    public function setAbizena(string $Abizena): static
    {
        $this->Abizena = $Abizena;

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

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): static
    {
        $this->Email = $Email;

        return $this;
    }

    public function getBileraID(): ?int
    {
        return $this->BileraID;
    }

    public function setBileraID(?int $BileraID): static
    {
        $this->BileraID = $BileraID;

        return $this;
    }

    public function getBisitaID(): ?int
    {
        return $this->BisitaID;
    }

    public function setBisitaID(?int $BisitaID): static
    {
        $this->BisitaID = $BisitaID;

        return $this;
    }

    public function getBilera(): ?Bilera
    {
        return $this->Bilera;
    }

    public function setBilera(?Bilera $Bilera): static
    {
        $this->Bilera = $Bilera;

        return $this;
    }

    public function getBisita(): ?Bisita
    {
        return $this->Bisita;
    }

    public function setBisita(?Bisita $Bisita): static
    {
        $this->Bisita = $Bisita;

        return $this;
    }
}
