<?php

require_once __DIR__ . '/Appro.php';
require_once __DIR__ . '/Produit.php';

class LigneAppro
{
    private ?int $id;
    private int $qteAppro;
    private int $qteRecu;
    private float $prixReel;
    private Appro $appro;
    private Produit $produit;

    public function __construct( ?int $id, int $qteAppro, int $qteRecu, float $prixReel, Appro $appro, Produit $produit) {

        $this->id = $id;
        $this->setQteAppro($qteAppro);
        $this->setQteRecu($qteRecu);
        $this->setPrixReel($prixReel);

        $this->appro = $appro;
        $this->produit = $produit;
    }

    // GETTERS

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQteAppro(): int
    {
        return $this->qteAppro;
    }

    public function getQteRecu(): int
    {
        return $this->qteRecu;
    }

    public function getPrixReel(): float
    {
        return $this->prixReel;
    }

    public function getAppro(): Appro
    {
        return $this->appro;
    }

    public function getProduit(): Produit
    {
        return $this->produit;
    }

    // SETTERS

    public function setQteAppro(int $qteAppro): void
    {
        if ($qteAppro <= 0) {
            throw new InvalidArgumentException(
                "La quantité approvisionnée doit être supérieure à zéro."
            );
        }

        $this->qteAppro = $qteAppro;
    }

    public function setQteRecu(int $qteRecu): void
    {
        if ($qteRecu < 0) {
            throw new InvalidArgumentException(
                "La quantité reçue ne peut pas être négative."
            );
        }

        if ($qteRecu > $this->qteAppro) {
            throw new InvalidArgumentException(
                "La quantité reçue ne peut pas dépasser la quantité approvisionnée."
            );
        }

        $this->qteRecu = $qteRecu;
    }

    public function setPrixReel(float $prixReel): void
    {
        if ($prixReel < 0) {
            throw new InvalidArgumentException(
                "Le prix réel ne peut pas être négatif."
            );
        }

        $this->prixReel = $prixReel;
    }

    // MÉTHODES MÉTIER

    public function calculerSousTotal(): float
    {
        return $this->qteAppro * $this->prixReel;
    }

    public function ajouterReception(int $quantite): void
    {
        if ($quantite <= 0) {
            throw new InvalidArgumentException(
                "La quantité reçue doit être supérieure à zéro."
            );
        }

        if ($this->qteRecu + $quantite > $this->qteAppro) {
            throw new InvalidArgumentException(
                "La quantité reçue dépasse la quantité prévue."
            );
        }

        $this->qteRecu += $quantite;
    }

    public function estEntierementRecu(): bool
    {
        return $this->qteRecu === $this->qteAppro;
    }

    public function getQteRestante(): int
    {
        return $this->qteAppro - $this->qteRecu;
    }
}