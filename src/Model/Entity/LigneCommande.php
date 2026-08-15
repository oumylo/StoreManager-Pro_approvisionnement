<?php

require_once __DIR__ . '/Commande.php';
require_once __DIR__ . '/Produit.php';

class LigneCommande
{
    private ?int $id;
    private int $qteCommande;
    private float $prixReel;
    private Commande $commande;
    private Produit $produit;

    public function __construct( ?int $id, int $qteCommande, float $prixReel, Commande $commande, Produit $produit) {
        
        $this->id = $id;

        $this->setQteCommande($qteCommande);
        $this->setPrixReel($prixReel);

        $this->commande = $commande;
        $this->produit = $produit;
    }

    // GETTERS

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQteCommande(): int
    {
        return $this->qteCommande;
    }

    public function getPrixReel(): float
    {
        return $this->prixReel;
    }

    public function getCommande(): Commande
    {
        return $this->commande;
    }

    public function getProduit(): Produit
    {
        return $this->produit;
    }

    // SETTERS
  

    public function setQteCommande(int $qteCommande): void
    {
        if ($qteCommande <= 0) {
            throw new InvalidArgumentException(
                "La quantité commandée doit être supérieure à zéro."
            );
        }

        $this->qteCommande = $qteCommande;
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
        return $this->qteCommande * $this->prixReel;
    }

    public function changerQuantite(int $nouvelleQuantite): void
    {
        $this->setQteCommande($nouvelleQuantite);
    }
}