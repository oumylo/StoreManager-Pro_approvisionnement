<?php

require_once dirname(__DIR__) . '/Entity/Produit.php';

class ProduitRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    public function getAllProduits(): array
    {
        $sql = "
            SELECT
                id,
                libelle,
                prix_vente,
                stock_initial
            FROM produits
            ORDER BY libelle
        ";

        $stmt = $this->pdo->query($sql);

        $stmt->setFetchMode(
            PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,
            Produit::class
        );

        return $stmt->fetchAll();
    }


    public function saveProduit(Produit $produit): int
    {
        $sql = "
            INSERT INTO produits
                (libelle, prix_vente, stock_initial)
            VALUES
                (:libelle, :prix_vente, :stock_initial)
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'libelle' => $produit->getLibelle(),
            'prix_vente' => $produit->getPrixVente(),
            'stock_initial' => $produit->getStockInitial()
        ]);

        return (int) $this->pdo->lastInsertId();
    }

  

    public function deleteProduit(int $id): bool
    {
        $sql = "
            DELETE FROM produits
            WHERE id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id' => $id
        ]);
    }
}