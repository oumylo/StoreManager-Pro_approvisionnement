<?php

require_once dirname(__DIR__) . '/Entity/Fournisseur.php';
require_once dirname(dirname(__DIR__)) . '/Core/Database.php';

class FournisseurRepository
{
    public static function getAllFournisseur(): array
    {
        $sql = "SELECT * FROM fournisseurs ORDER BY nom";

        return Database::query($sql, false, Fournisseur::class);
    }


    public static function createFournisseur(Fournisseur $fournisseur): int
    {
        $sql = "INSERT INTO fournisseurs
                (nom, email, tel, adresse)
                VALUES
                (:nom, :email, :tel, :adresse)";

        return Database::executeUpdate($sql, [
            'nom' => $fournisseur->getNom(),
            'email' => $fournisseur->getEmail(),
            'tel' => $fournisseur->getTel(),
            'adresse' => $fournisseur->getAdresse()
        ]);
    }


   

}