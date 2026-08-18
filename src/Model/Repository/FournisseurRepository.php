```php
<?php

require_once dirname(__DIR__) . '/Entity/Fournisseur.php';
require_once dirname(dirname(__DIR__)) . '/Core/Database.php';

class FournisseurRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = connexionDB();
    }


   
    public function findAll(): array
    {
        $sql = "SELECT * FROM fournisseurs ORDER BY nom";

        $stmt = $this->pdo->query($sql);

        $stmt->setFetchMode(PDO::FETCH_CLASS, Fournisseur::class);

        $fournisseurs = $stmt->fetchAll();

        return $fournisseurs;
    }


   
    public function findById(int $id): ?Fournisseur
    {
        $sql = "SELECT * FROM fournisseurs WHERE id = ?";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([$id]);

        $stmt->setFetchMode(PDO::FETCH_CLASS, Fournisseur::class);

        $fournisseur = $stmt->fetch();

        if ($fournisseur === false) {
            return null;
        }

        return $fournisseur;
    }


   
    public function create(Fournisseur $fournisseur): int
    {
        $sql = "INSERT INTO fournisseurs
                (nom, email, tel, adresse)
                VALUES
                (:nom, :email, :tel, :adresse)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'nom' => $fournisseur->getNom(),
            'email' => $fournisseur->getEmail(),
            'tel' => $fournisseur->getTel(),
            'adresse' => $fournisseur->getAdresse()
        ]);

        $id = $this->pdo->lastInsertId();

        return (int) $id;
    }


    
    public function update(Fournisseur $fournisseur): bool
    {
        $sql = "UPDATE fournisseurs
                SET nom = :nom,
                    email = :email,
                    tel = :tel,
                    adresse = :adresse
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $resultat = $stmt->execute([
            'nom' => $fournisseur->getNom(),
            'email' => $fournisseur->getEmail(),
            'tel' => $fournisseur->getTel(),
            'adresse' => $fournisseur->getAdresse(),
            'id' => $fournisseur->getId()
        ]);

        return $resultat;
    }


   
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM fournisseurs WHERE id = ?";

        $stmt = $this->pdo->prepare($sql);

        $resultat = $stmt->execute([$id]);

        return $resultat;
    }
}
