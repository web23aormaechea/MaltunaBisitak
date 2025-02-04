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

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Izena = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Nondik = null;

    #[ORM\Column]
    private ?bool $Itxita = false;  // Valor por defecto en la propiedad

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $Data = null;

    #[ORM\Column(nullable: true)]
    private ?bool $Onartuta = null;

    public function __construct()
    {
        // Asegurarse de que el valor por defecto sea 'false' cuando se cree una nueva instancia
        $this->Itxita = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIzena(): ?string
    {
        return $this->Izena;
    }

    public function setIzena(?string $Izena): static
    {
        $this->Izena = $Izena;

        return $this;
    }

    public function getNondik(): ?string
    {
        return $this->Nondik;
    }

    public function setNondik(?string $Nondik): static
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

    public function getData(): ?\DateTimeInterface
    {
        return $this->Data;
    }

    public function setData(\DateTimeInterface $Data): static
    {
        $this->Data = $Data;

        return $this;
    }

    public function isOnartuta(): ?bool
    {
        return $this->Onartuta;
    }

    public function setOnartuta(?bool $Onartuta): static
    {
        $this->Onartuta = $Onartuta;

        return $this;
    }
}
