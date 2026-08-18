<?php

require_once dirname(__DIR__) . '/Entity/Commande.php';
require_once dirname(__DIR__) . '/Entity/LigneCommande.php';
require_once dirname(dirname(__DIR__)) . '/Core/Database.php';

class CommandeRepository
{
    public static function create(
        int $clientId,
        int $utilisateurId,
        float $montantInitial,
        float $avance
    ): int {

        $sql = "INSERT INTO commandes
                (client_id, utilisateur_id, montant_initial, avance)
                VALUES
                (:client_id, :utilisateur_id, :montant_initial, :avance)";

        return Database::executeUpdate($sql, [
            'client_id' => $clientId,
            'utilisateur_id' => $utilisateurId,
            'montant_initial' => $montantInitial,
            'avance' => $avance
        ]);
    }


    public static function addLigne(
        int $commandeId,
        int $produitId,
        int $qte,
        float $prixReel
    ): int {

        $sql = "INSERT INTO lignes_commande
                (commande_id, produit_id, qte_commande, prix_reel)
                VALUES
                (:commande_id, :produit_id, :qte_commande, :prix_reel)";

        return Database::executeUpdate($sql, [
            'commande_id' => $commandeId,
            'produit_id' => $produitId,
            'qte_commande' => $qte,
            'prix_reel' => $prixReel
        ]);
    }


    public static function findById(int $id): ?Commande
    {
        $sql = "SELECT * FROM commandes WHERE id = ?";

        return Database::executeQuery($sql, [$id], true, Commande::class);
    }
}