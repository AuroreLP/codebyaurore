<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 30)]
    private string $firstname;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 30)]
    private string $lastname;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 30)]
    private string $username;

    #[ORM\Column]
    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    private string $password;

    #[ORM\Column(type: 'json')]
    private array $roles = [];


    public function __toString()
    {
        // Échappement des données pour éviter XSS
        return htmlspecialchars($this->username, ENT_QUOTES, 'UTF-8');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        // Nettoyage des données avant de les stocker
        $this->firstname = htmlspecialchars($firstname, ENT_QUOTES, 'UTF-8');

        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        // Nettoyage des données avant de les stocker
        $this->lastname = htmlspecialchars($lastname, ENT_QUOTES, 'UTF-8');

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        // Nettoyage des données avant de les stocker
        $this->username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        $admins = explode(',', $_ENV['ADMIN_USERS']);
        return in_array($this->username, $admins) ? ['ROLE_USER', 'ROLE_ADMIN'] : ['ROLE_USER']; 
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {
        return;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

}