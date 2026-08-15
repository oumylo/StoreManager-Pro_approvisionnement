<?php

class Produit
{
    private ?int $id;
    private string $libelle;
    private float $prixVente;
    private int $stockInitial;

    public function __construct( ?int $id, string $libelle, float $prixVente, int $stockInitial = 0) {

        $this->id = $id;
        $this->setLibelle($libelle);
        $this->setPrixVente($prixVente);
        $this->setStockInitial($stockInitial);
    }

    /// GETTERS 
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): string
    {
        return $this->libelle;
    }

    public function getPrixVente(): float
    {
        return $this->prixVente;
    }

    public function getStockInitial(): int
    {
        return $this->stockInitial;
    }

     //SETTERS

    public function setLibelle(string $libelle): void
    {
        if (trim($libelle) === '') {
            throw new InvalidArgumentException(
                "Le libellé du produit est obligatoire."
            );
        }

        $this->libelle = trim($libelle);
    }

    public function setPrixVente(float $prixVente): void
    {
        if ($prixVente < 0) {
            throw new InvalidArgumentException(
                "Le prix de vente ne peut pas être négatif."
            );
        }

        $this->prixVente = $prixVente;
    }

    public function setStockInitial(int $stockInitial): void
    {
        if ($stockInitial < 0) {
            throw new InvalidArgumentException(
                "Le stock ne peut pas être négatif."
            );
        }

        $this->stockInitial = $stockInitial;
    }


    // Méthodes métier

    public function augmenterStock(int $quantite): void
    {
        if ($quantite <= 0) {
            throw new InvalidArgumentException(
                "La quantité doit être supérieure à zéro."
            );
        }

        $this->stockInitial += $quantite;
    }

    public function diminuerStock(int $quantite): void
    {
        if ($quantite <= 0) {
            throw new InvalidArgumentException(
                "La quantité doit être supérieure à zéro."
            );
        }

        if ($quantite > $this->stockInitial) {
            throw new InvalidArgumentException(
                "Stock insuffisant."
            );
        }

        $this->stockInitial -= $quantite;
    }
}