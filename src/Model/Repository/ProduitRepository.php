<?php

require_once dirname(__DIR__) . '/Entity/Produit.php';
require_once dirname(dirname(__DIR__)) . '/Core/Database.php';

class ProduitRepository
{
    public static function getAllProduit(): array
    {
        $sql = "SELECT * FROM produits ORDER BY libelle";

        return Database::query($sql, false, Produit::class);
    }



    public static function createProduit(Produit $produit): int
    {
        $sql = "INSERT INTO produits
                (libelle, prix_vente, quantite_stock)
                VALUES
                (:libelle, :prix_vente, :quantite_stock)";

        $stmp = Database::executeUpdate($sql, [
            'libelle' => $produit->getLibelle(),
            'prix_vente' => $produit->getPrixVente(),
            'quantite_stock' => $produit->getQuantiteStock()
        ]);

        return $stmp;
    }




    public static function updateStock(int $produitId, int $nouvelleQuantite): bool
    {
        $sql = "UPDATE produits
                SET quantite_stock = :quantite
                WHERE id = :id";

        $lignesModifiees = Database::executeUpdate($sql, [
            'quantite' => $nouvelleQuantite,
            'id' => $produitId
        ]);

        return $lignesModifiees > 0;
    }


    public static function deleteProduit(int $id): bool
    {
        $sql = "DELETE FROM produits WHERE id = ?";

        $lignesSupprimees = Database::executeUpdate($sql, [$id]);

        return $lignesSupprimees > 0;
    }
}