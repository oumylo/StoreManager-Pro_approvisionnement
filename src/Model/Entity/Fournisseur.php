<?php

class Fournisseur
{
    private ?int $id = null;
    private string $nom = '';
    private ?string $email = null;
    private ?string $tel = null;
    private ?string $adresse = null;

    public function __construct( ?int $id = null, ?string $nom = null, ?string $email = null, ?string $tel = null, ?string $adresse = null) 
    {
        $this->id = $id;
        if ($nom !== null) {
            $this->setNom($nom);
        }

        if ($email !== null) {
            $this->setEmail($email);
        }

        if ($tel !== null) {
            $this->setTel($tel);
        }

        if ($adresse !== null) {
            $this->setAdresse($adresse);
        }
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