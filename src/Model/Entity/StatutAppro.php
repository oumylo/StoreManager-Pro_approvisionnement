<?php

class StatutAppro
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
                "Le nom du statut est obligatoire."
            );
        }

        $this->nom = $nom;
    }

    // MÉTHODES MÉTIER

    public function estEnAttente(): bool
    {
        return $this->nom === 'EN_ATTENTE';
    }

    public function estRecu(): bool
    {
        return $this->nom === 'RECU';
    }

    public function estAnnule(): bool
    {
        return $this->nom === 'ANNULE';
    }
}