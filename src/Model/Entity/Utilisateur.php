<?php

require_once __DIR__ . '/Role.php';

class Utilisateur
{
    private ?int $id;
    private string $nomComplet;
    private string $email;
    private string $motPasse;
    private ?string $adresse;
    private ?string $tel;
    private Role $role;

    public function __construct( ?int $id, string $nomComplet, string $email, string $motPasse, ?string $adresse, ?string $tel, Role $role) {
        
        $this->id = $id;
        $this->setNomComplet($nomComplet);
        $this->setEmail($email);
        $this->setMotPasse($motPasse);
        $this->setAdresse($adresse);
        $this->setTel($tel);
        $this->setRole($role);
    }

    // GETTERS

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomComplet(): string
    {
        return $this->nomComplet;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getMotPasse(): string
    {
        return $this->motPasse;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    // SETTERS

    public function setNomComplet(string $nomComplet): void
    {
        if (trim($nomComplet) === '') {
            throw new InvalidArgumentException(
                "Le nom complet est obligatoire."
            );
        }

        $this->nomComplet = trim($nomComplet);
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

    public function setMotPasse(string $motPasse): void
    {
        if (trim($motPasse) === '') {
            throw new InvalidArgumentException(
                "Le mot de passe est obligatoire."
            );
        }

        $this->motPasse = $motPasse;
    }

    public function setAdresse(?string $adresse): void
    {
        $this->adresse = $adresse !== null
            ? trim($adresse)
            : null;
    }

    public function setTel(?string $tel): void
    {
        $this->tel = $tel !== null
            ? trim($tel)
            : null;
    }

    public function setRole(Role $role): void
    {
        $this->role = $role;
    }

    // MÉTHODES MÉTIER

    public function estAdmin(): bool
    {
        return $this->role->estAdmin();
    }

    public function estVente(): bool
    {
        return $this->role->estVente();
    }

    public function estStock(): bool
    {
        return $this->role->estStock();
    }

    public function estInventaire(): bool
    {
        return $this->role->estInventaire();
    }
}