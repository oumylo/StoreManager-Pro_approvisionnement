<?php


require_once dirname(__DIR__) . '/Entity/Fournisseur.php';
require_once dirname(__DIR__) . '/src/Core/Database.php';

class FournisseurRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = connexionDB();
    }

    // Récupérer tous les fournisseurs
    public function findAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM fournisseurs ORDER BY nom");
        $stmt->setFetchMode(PDO::FETCH_CLASS, Fournisseur::class);

        return $stmt->fetchAll();
    }

    // Récupérer un fournisseur par son id
    public function findById(int $id): ?Fournisseur
    {
        $stmt = $this->pdo->prepare("SELECT * FROM fournisseurs WHERE id = ?");
        $stmt->execute([$id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, Fournisseur::class);

        $fournisseur = $stmt->fetch();

        return $fournisseur !== false ? $fournisseur : null;
    }

    // Insérer un nouveau fournisseur et retourner son id
    public function create(Fournisseur $fournisseur): int
    {
        $sql = "INSERT INTO fournisseurs (nom, email, tel, adresse)
                VALUES (:nom, :email, :tel, :adresse)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nom'     => $fournisseur->getNom(),
            'email'   => $fournisseur->getEmail(),
            'tel'     => $fournisseur->getTel(),
            'adresse' => $fournisseur->getAdresse(),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    // Mettre à jour un fournisseur existant
    public function update(Fournisseur $fournisseur): bool
    {
        $sql = "UPDATE fournisseurs
                SET nom = :nom,
                    email = :email,
                    tel = :tel,
                    adresse = :adresse
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'nom'     => $fournisseur->getNom(),
            'email'   => $fournisseur->getEmail(),
            'tel'     => $fournisseur->getTel(),
            'adresse' => $fournisseur->getAdresse(),
            'id'      => $fournisseur->getId(),
        ]);
    }

    // Supprimer un fournisseur
    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM fournisseurs WHERE id = ?");

        return $stmt->execute([$id]);
    }
}