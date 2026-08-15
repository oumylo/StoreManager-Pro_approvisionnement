<?php

class Fournisseur
{
    private ?int $id;
    private string $nom;
    private string $email;
    private string $tel;
    private string $adresse;

    public function __construct( ?int $id, string $nom, string $email, string $tel, string $adresse) 
    {
        $this->id = $id;
        $this->setNom($nom);
        $this->setEmail($email);
        $this->setTel($tel);
        $this->setAdresse($adresse);
    }

    // GETTERS

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTel(): string
    {
        return $this->tel;
    }

    public function getAdresse(): string
    {
        return $this->adresse;
    }

    // SETTERS

    public function setNom(string $nom): void
    {
        if (trim($nom) === '') {
            throw new InvalidArgumentException(
                "Le nom du fournisseur est obligatoire."
            );
        }

        $this->nom = trim($nom);
    }

    public function setEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(
                "L'adresse email est invalide."
            );
        }

        $this->email = trim($email);
    }

    public function setTel(string $tel): void
    {
        if (trim($tel) === '') {
            throw new InvalidArgumentException(
                "Le téléphone est obligatoire."
            );
        }

        $this->tel = trim($tel);
    }

    public function setAdresse(string $adresse): void
    {
        if (trim($adresse) === '') {
            throw new InvalidArgumentException(
                "L'adresse du fournisseur est obligatoire."
            );
        }

        $this->adresse = trim($adresse);
    }
}