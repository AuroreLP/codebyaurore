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
    #[Assert\Length(
        min: 2,
        max: 30,
        minMessage: 'Le prénom doit comporter au moins {{ limit }} caractères.',
        maxMessage: 'Le prénom ne peut pas comporter plus de {{ limit }} caractères.'
    )]
    private string $firstname;

    #[MongoDB\Field(type: 'string')]
    #[Assert\NotBlank(message: 'Le nom est requis.')]
    #[Assert\Length(
        min: 2,
        max: 30,
        minMessage: 'Le nom doit comporter au moins {{ limit }} caractères.',
        maxMessage: 'Le nom ne peut pas comporter plus de {{ limit }} caractères.'
    )]
    private string $lastname;

    #[MongoDB\Field(type: 'string', nullable: true)]
    #[Assert\Regex(
        pattern: "/^[0-9\s\-\(\)\+]+$/",
        message: "Le numéro de téléphone n'est pas valide."
    )]
    private ?string $phone = null;

    #[MongoDB\Field(type: 'string')]
    #[Assert\NotBlank(message: 'L\'adresse email est requise.')]
    #[Assert\Email(message: 'L\'adresse email n\'est pas valide.')]
    private string $email;

    #[MongoDB\Field(type: 'string')]
    #[Assert\NotBlank(message: 'L\'objet est requis.')]
    #[Assert\Length(
        min: 5,
        max: 100,
        minMessage: "L'objet doit comporter au moins {{ limit }} caractères.",
        maxMessage: "L'objet ne peut pas comporter plus de {{ limit }} caractères."
    )]
    private string $subject = '';

    #[MongoDB\Field(type: 'string')]
    #[Assert\NotBlank(message: 'Le message est requis.')]
    #[Assert\Length(
        min: 10,
        max: 1000,
        minMessage: 'Le message doit comporter au moins {{ limit }} caractères.',
        maxMessage: 'Le message ne peut pas comporter plus de {{ limit }} caractères.'
    )]
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