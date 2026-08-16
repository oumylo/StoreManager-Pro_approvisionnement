
<?php

require_once dirname(__DIR__) . '/Entity/Produit.php';
require_once dirname(dirname(__DIR__)) . '/Core/Database.php';

class ProduitRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = connexionDB();
    }


    
    public function findAll(): array
    {
        $sql = "SELECT * FROM produits ORDER BY libelle";

        $stmt = $this->pdo->query($sql);

        $stmt->setFetchMode(PDO::FETCH_CLASS, Produit::class);

        $produits = $stmt->fetchAll();

        return $produits;
    }


   
    public function findById(int $id): ?Produit
    {
        $sql = "SELECT * FROM produits WHERE id = ?";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([$id]);

        $stmt->setFetchMode(PDO::FETCH_CLASS, Produit::class);

        $produit = $stmt->fetch();

        if ($produit === false) {
            return null;
        }

        return $produit;
    }


    
    public function create(Produit $produit): int
    {
        $sql = "INSERT INTO produits
                (libelle, prix_vente, quantite_stock)
                VALUES
                (:libelle, :prix_vente, :quantite_stock)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'libelle' => $produit->getLibelle(),
            'prix_vente' => $produit->getPrixVente(),
            'quantite_stock' => $produit->getQuantiteStock()
        ]);

        $id = $this->pdo->lastInsertId();

        return (int) $id;
    }


    
    public function update(Produit $produit): bool
    {
        $sql = "UPDATE produits
                SET libelle = :libelle,
                    prix_vente = :prix_vente,
                    quantite_stock = :quantite_stock
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $resultat = $stmt->execute([
            'libelle' => $produit->getLibelle(),
            'prix_vente' => $produit->getPrixVente(),
            'quantite_stock' => $produit->getQuantiteStock(),
            'id' => $produit->getId()
        ]);

        return $resultat;
    }


    
    public function updateStock(int $produitId, int $nouvelleQuantite): bool
    {
        $sql = "UPDATE produits
                SET quantite_stock = :quantite
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $resultat = $stmt->execute([
            'quantite' => $nouvelleQuantite,
            'id' => $produitId
        ]);

        return $resultat;
    }


    
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM produits WHERE id = ?";

        $stmt = $this->pdo->prepare($sql);

        $resultat = $stmt->execute([$id]);

        return $resultat;
    }
}
