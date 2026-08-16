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
        $stmt = $this->pdo->query("SELECT * FROM produits ORDER BY libelle");
        $stmt->setFetchMode(PDO::FETCH_CLASS, Produit::class);

        return $stmt->fetchAll();
    }

    public function findById(int $id): ?Produit
    {
        $stmt = $this->pdo->prepare("SELECT * FROM produits WHERE id = ?");
        $stmt->execute([$id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Produit::class);

        $produit = $stmt->fetch();

        return $produit !== false ? $produit : null;
    }

    public function create(Produit $produit): int
    {
        $sql = "INSERT INTO produits (libelle, prix_vente, quantite_stock)
                VALUES (:libelle, :prix_vente, :quantite_stock)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'libelle'        => $produit->getLibelle(),
            'prix_vente'     => $produit->getPrixVente(),
            'quantite_stock' => $produit->getQuantiteStock(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function update(Produit $produit): bool
    {
        $sql = "UPDATE produits
                SET libelle = :libelle,
                    prix_vente = :prix_vente,
                    quantite_stock = :quantite_stock
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'libelle'        => $produit->getLibelle(),
            'prix_vente'     => $produit->getPrixVente(),
            'quantite_stock' => $produit->getQuantiteStock(),
            'id'             => $produit->getId(),
        ]);
    }

    public function updateStock(int $produitId, int $nouvelleQuantite): bool
    {
        $sql = "UPDATE produits SET quantite_stock = :quantite WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'quantite' => $nouvelleQuantite,
            'id'       => $produitId,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM produits WHERE id = ?");

        return $stmt->execute([$id]);
    }
}