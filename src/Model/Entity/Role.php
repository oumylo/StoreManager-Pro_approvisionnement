<?php

class Role
{
    private ?int $id;
    private string $nom;

    public function __construct(?int $id, string $nom)
    {
        $this->id = $id;
        $this->setNom($nom);
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

    // SETTER

    public function setNom(string $nom): void
    {
        $nom = strtoupper(trim($nom));

        if ($nom === '') {
            throw new InvalidArgumentException(
                "Le nom du rôle est obligatoire."
            );
        }

        $this->nom = $nom;
    }

    // MÉTHODES MÉTIER

    public function estAdmin(): bool
    {
        return $this->nom === 'ADMIN';
    }

    public function estVente(): bool
    {
        return $this->nom === 'VENTE';
    }

    public function estStock(): bool
    {
        return $this->nom === 'STOCK';
    }

    public function estInventaire(): bool
    {
        return $this->nom === 'INVENTAIRE';
    }
}