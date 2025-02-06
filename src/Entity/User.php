<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Erabiltzailea = null;

    #[ORM\Column(length: 255)]
    private ?string $Pasahitza = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getErabiltzailea(): ?string
    {
        return $this->Erabiltzailea;
    }

    public function setErabiltzailea(string $Erabiltzailea): static
    {
        $this->Erabiltzailea = $Erabiltzailea;

        return $this;
    }

    public function getPasahitza(): ?string
    {
        return $this->Pasahitza;
    }
    public function getPassword(): ?string
    {
        return $this->Pasahitza; // o el nombre del atributo donde almacenas la contraseña
    }

    public function setPasahitza(string $Pasahitza): static
    {
        $this->Pasahitza = $Pasahitza;

        return $this;
    }
}
