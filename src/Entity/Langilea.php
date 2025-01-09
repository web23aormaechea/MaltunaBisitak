<?php

namespace App\Entity;

use App\Repository\LangileaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LangileaRepository::class)]
class Langilea
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
    private ?string $Telefonoa = null;

    #[ORM\Column(length: 255)]
    private ?string $Nondik = null;

    #[ORM\Column]
    private ?bool $Giltza = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $Data = null;

    #[ORM\Column(length: 255)]
    private ?string $Firma = null;

    #[ORM\Column]
    private ?bool $Irteera = null;

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

    public function getTelefonoa(): ?string
    {
        return $this->Telefonoa;
    }

    public function setTelefonoa(string $Telefonoa): static
    {
        $this->Telefonoa = $Telefonoa;

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

    public function isGiltza(): ?bool
    {
        return $this->Giltza;
    }

    public function setGiltza(bool $Giltza): static
    {
        $this->Giltza = $Giltza;

        return $this;
    }

    public function getData(): ?\DateTimeInterface
    {
        return $this->Data;
    }

    public function setData(\DateTimeInterface $Data): static
    {
        $this->Data = $Data;

        return $this;
    }

    public function getFirma(): ?string
    {
        return $this->Firma;
    }

    public function setFirma(string $Firma): static
    {
        $this->Firma = $Firma;

        return $this;
    }

    public function isIrteera(): ?bool
    {
        return $this->Irteera;
    }

    public function setIrteera(bool $Irteera): static
    {
        $this->Irteera = $Irteera;

        return $this;
    }
}
