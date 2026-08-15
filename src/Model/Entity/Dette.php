<?php

require_once __DIR__ . '/Client.php';
require_once __DIR__ . '/Commande.php';

class Dette
{
    private ?int $id;
    private float $montantInitial;
    private float $montantPaye;
    private float $resteDu;
    private string $statut;
    private ?DateTime $dateCreation;
    private ?Commande $commande;
    private Client $client;

    public function __construct(?int $id, float $montantInitial, float $montantPaye, DateTime $dateCreation, ?Commande $commande,Client $client) {

        $this->id = $id;
        $this->dateCreation = $dateCreation;
        $this->commande = $commande;
        $this->client = $client;

        $this->setMontantInitial($montantInitial);
        $this->setMontantPaye($montantPaye);

        $this->calculerResteDu();
        $this->mettreAJourStatut();
    }

    // GETTERS

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontantInitial(): float
    {
        return $this->montantInitial;
    }

    public function getMontantPaye(): float
    {
        return $this->montantPaye;
    }

    public function getResteDu(): float
    {
        return $this->resteDu;
    }

    public function getStatut(): string
    {
        return $this->statut;
    }

    public function getDateCreation(): ?DateTime
    {
        return $this->dateCreation;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    // SETTERS

    public function setMontantInitial(float $montantInitial): void
    {
        if ($montantInitial < 0) {
            throw new InvalidArgumentException(
                "Le montant initial ne peut pas être négatif."
            );
        }

        $this->montantInitial = $montantInitial;
    }

    public function setMontantPaye(float $montantPaye): void
    {
        if ($montantPaye < 0) {
            throw new InvalidArgumentException(
                "Le montant payé ne peut pas être négatif."
            );
        }

        if ($montantPaye > $this->montantInitial) {
            throw new InvalidArgumentException(
                "Le montant payé ne peut pas dépasser la dette."
            );
        }

        $this->montantPaye = $montantPaye;
    }

    // MÉTHODES MÉTIER

    private function calculerResteDu(): void
    {
        $this->resteDu = $this->montantInitial - $this->montantPaye;
    }

    private function mettreAJourStatut(): void
    {
        $this->statut = $this->resteDu == 0
            ? 'SOLDEE'
            : 'NON_SOLDEE';
    }

    public function rembourser(float $montant): void
    {
        if ($montant <= 0) {
            throw new InvalidArgumentException(
                "Le montant du remboursement doit être supérieur à zéro."
            );
        }

        if ($this->montantPaye + $montant > $this->montantInitial) {
            throw new InvalidArgumentException(
                "Le remboursement dépasse le montant restant dû."
            );
        }

        $this->montantPaye += $montant;

        $this->calculerResteDu();
        $this->mettreAJourStatut();
    }

    public function estSoldee(): bool
    {
        return $this->statut === 'SOLDEE';
    }
}