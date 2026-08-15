<?php

require_once dirname(__DIR__) . '/Entity/Fournisseur.php';

class FournisseurRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    public function getAllFournisseurs(): array
    {
        $sql = "
            SELECT
                id,
                nom,
                email,
                tel,
                adresse
            FROM fournisseurs
            ORDER BY nom
        ";

        $stmt = $this->pdo->query($sql);

        $stmt->setFetchMode(
            PDO::FETCH_CLASS,
            Fournisseur::class
        );

        return $stmt->fetchAll();
    }


    public function saveFournisseur(
        Fournisseur $fournisseur
    ): int {
        $sql = "
            INSERT INTO fournisseurs
                (nom, email, tel, adresse)
            VALUES
                (:nom, :email, :tel, :adresse)
        ";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'nom' => $fournisseur->getNom(),
            'email' => $fournisseur->getEmail(),
            'tel' => $fournisseur->getTel(),
            'adresse' => $fournisseur->getAdresse()
        ]);

        return (int) $this->pdo->lastInsertId();
    }



}