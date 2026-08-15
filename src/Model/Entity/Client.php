<?php

class Client
{
    private ?int $id = null;
    private string $nom = '';
    private string $prenom = '';
    private string $email = '';
    private string $tel = '';
    private float $limite_credit = 0;

    public function __construct( ?int $id = null, ?string $nom = null, ?string $prenom = null, ?string $email = null, ?string $tel = null, ?float $limite_credit = null) {
       
        $this->id = $id;

        if ($nom !== null) {
            $this->setNom($nom);
        }

        if ($prenom !== null) {
            $this->setPrenom($prenom);
        }

        if ($email !== null) {
            $this->setEmail($email);
        }

        if ($tel !== null) {
            $this->setTel($tel);
        }

        if ($limite_credit !== null) {
            $this->setLimiteCredit($limite_credit);
        }
    }

    // GETTERS

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function getLimiteCredit(): float
    {
        return $this->limite_credit;
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

    public function setLimiteCredit(float $limite_credit): void
    {
        if ($limite_credit < 0) {
            throw new InvalidArgumentException(
                "La limite de crédit ne peut pas être négative."
            );
        }

        $this->limite_credit = $limite_credit;
    }

    // MÉTHODES MÉTIER

    public function getCreditDisponible(float $creditUtilise): float
    {
        if ($creditUtilise < 0) {
            throw new InvalidArgumentException(
                "Le crédit utilisé ne peut pas être négatif."
            );
        }

        return max(0, $this->limite_credit - $creditUtilise);
    }

    public function peutPrendreCredit(
        float $montant,
        float $creditUtilise
    ): bool {
        if ($montant < 0 || $creditUtilise < 0) {
            throw new InvalidArgumentException(
                "Les montants ne peuvent pas être négatifs."
            );
        }

        return ($creditUtilise + $montant) <= $this->limite_credit;
    }
}