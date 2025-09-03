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
        error_log("DEBUG getRoles() - roles from DB: " . json_encode($this->roles));
        
        // Commencer par les rôles stockés en base
        $roles = $this->roles;
        
        // Ajouter ROLE_ADMIN si l'utilisateur est dans ADMIN_USERS
        if (isset($_ENV['ADMIN_USERS'])) {
            $admins = explode(',', $_ENV['ADMIN_USERS']);
            if (in_array($this->username, $admins) && !in_array('ROLE_ADMIN', $roles)) {
                $roles[] = 'ROLE_ADMIN';
            }
        }
        
        // Garantir qu'il y a toujours ROLE_USER
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }
        
        error_log("DEBUG getRoles() - final roles: " . json_encode($roles));
        return $roles;
    }

    /*
    public function getRoles(): array
    {
        // Commencer par les rôles stockés en base
        $roles = $this->roles;
        
        // Ajouter ROLE_ADMIN si l'utilisateur est dans ADMIN_USERS
        if (isset($_ENV['ADMIN_USERS'])) {
            $admins = explode(',', $_ENV['ADMIN_USERS']);
            if (in_array($this->username, $admins) && !in_array('ROLE_ADMIN', $roles)) {
                $roles[] = 'ROLE_ADMIN';
            }
        }
        
        // Garantir qu'il y a toujours ROLE_USER
        if (!in_array('ROLE_USER', $roles)) {
            $roles[] = 'ROLE_USER';
        }
        
        return $roles;
    } */

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