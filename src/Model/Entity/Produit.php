<?php

class Produit
{
    private ?int $id = null;
    private string $libelle = '';
    private float $prix_vente = 0;
    private int $stock_initial = 0;

    public function __construct(
        ?int $id = null,
        ?string $libelle = null,
        ?float $prix_vente = null,
        ?int $stock_initial = null
    ) {
        $this->id = $id;

        if ($libelle !== null) {
            $this->setLibelle($libelle);
        }

        if ($prix_vente !== null) {
            $this->setPrixVente($prix_vente);
        }

        if ($stock_initial !== null) {
            $this->setStockInitial($stock_initial);
        }
    }

    // GETTERS

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
        return $this->prix_vente;
    }

    public function getStockInitial(): int
    {
        return $this->stock_initial;
    }

    // SETTERS

    public function setLibelle(string $libelle): void
    {
        if (trim($libelle) === '') {
            throw new InvalidArgumentException(
                "Le libellé du produit est obligatoire."
            );
        }

        $this->libelle = trim($libelle);
    }

    public function setPrixVente(float $prix_vente): void
    {
        if ($prix_vente < 0) {
            throw new InvalidArgumentException(
                "Le prix de vente ne peut pas être négatif."
            );
        }

        $this->prix_vente = $prix_vente;
    }

    public function setStockInitial(int $stock_initial): void
    {
        if ($stock_initial < 0) {
            throw new InvalidArgumentException(
                "Le stock ne peut pas être négatif."
            );
        }

        $this->stock_initial = $stock_initial;
    }

    // MÉTHODES MÉTIER

    public function augmenterStock(int $quantite): void
    {
        if ($quantite <= 0) {
            throw new InvalidArgumentException(
                "La quantité doit être supérieure à zéro."
            );
        }

        $this->stock_initial += $quantite;
    }

    public function diminuerStock(int $quantite): void
    {
        if ($quantite <= 0) {
            throw new InvalidArgumentException(
                "La quantité doit être supérieure à zéro."
            );
        }

        if ($quantite > $this->stock_initial) {
            throw new InvalidArgumentException(
                "Stock insuffisant."
            );
        }

        $this->stock_initial -= $quantite;
    }
}