<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;

#[MongoDB\Document]
class Message
{
    #[MongoDB\Id]
    private string $id;

    #[MongoDB\Field(type: 'string')]
    #[Assert\NotBlank(message: 'Le prénom est requis.')]
    private string $firstname;

    #[MongoDB\Field(type: 'string')]
    #[Assert\NotBlank(message: 'Le nom est requis.')]
    private string $lastname;

    #[MongoDB\Field(type: 'string', nullable: true)]
    private ?string $phone = null;

    #[MongoDB\Field(type: 'string')]
    #[Assert\NotBlank(message: 'L\'adresse email est requise.')]
    #[Assert\Email(message: 'L\'adresse email n\'est pas valide.')]
    private string $email;

    #[MongoDB\Field(type: 'string')]
    #[Assert\NotBlank(message: 'L\'objet est requis.')]
    private string $subject = '';

    #[MongoDB\Field(type: 'string')]
    #[Assert\NotBlank(message: 'Le message est requis.')]
    private string $content;

    #[MongoDB\Field(type: 'date')]
    private \DateTime $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->subject = ''; // Initialisation de la propriété subject
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;
        return $this;
    }

    
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }
}