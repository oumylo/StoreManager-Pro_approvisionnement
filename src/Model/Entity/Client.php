<?php

class Client
{
    private ?int $id;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $tel;
    private float $limiteCredit;

    public function __construct(?int $id, string $nom, string $prenom, string $email, string $tel, float $limiteCredit = 0) {

        $this->id = $id;
        $this->setNom($nom);
        $this->setPrenom($prenom);
        $this->setEmail($email);
        $this->setTel($tel);
        $this->setLimiteCredit($limiteCredit);
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

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getTel(): string
    {
        return $this->tel;
    }

    public function getLimiteCredit(): float
    {
        return $this->limiteCredit;
    }

    // SETTERS

    public function setNom(string $nom): void
    {
        if (trim($nom) === '') {
            throw new InvalidArgumentException(
                "Le nom du client est obligatoire."
            );
        }

        $this->nom = trim($nom);
    }

    public function setPrenom(string $prenom): void
    {
        if (trim($prenom) === '') {
            throw new InvalidArgumentException(
                "Le prénom du client est obligatoire."
            );
        }

        $this->prenom = trim($prenom);
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

    public function setLimiteCredit(float $limiteCredit): void
    {
        if ($limiteCredit < 0) {
            throw new InvalidArgumentException(
                "La limite de crédit ne peut pas être négative."
            );
        }

        $this->limiteCredit = $limiteCredit;
    }

    // MÉTHODES MÉTIER

    public function getCreditDisponible(float $creditUtilise): float
    {
        if ($creditUtilise < 0) {
            throw new InvalidArgumentException(
                "Le crédit utilisé ne peut pas être négatif."
            );
        }

        return max(0, $this->limiteCredit - $creditUtilise);
    }

    public function peutPrendreCredit( float $montant, float $creditUtilise): bool {
        
        if ($montant < 0 || $creditUtilise < 0) {
            throw new InvalidArgumentException(
                "Les montants ne peuvent pas être négatifs."
            );
        }

        return ($creditUtilise + $montant) <= $this->limiteCredit;
    }
}