<?php

namespace App\Entity;

use App\Repository\BileraRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BileraRepository::class)]
class Bilera
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Izena = null;

    #[ORM\Column(length: 255)]
    private ?string $Lekua = null;


    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $Data = null;

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

    public function getLekua(): ?string
    {
        return $this->Lekua;
    }

    public function setLekua(string $Lekua): static
    {
        $this->Lekua = $Lekua;

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
}
