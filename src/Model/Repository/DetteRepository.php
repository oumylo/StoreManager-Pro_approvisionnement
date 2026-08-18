<?php

require_once dirname(__DIR__) . '/Entity/Dette.php';
require_once dirname(dirname(__DIR__)) . '/Core/Database.php';

class DetteRepository
{
    public static function create(
        int $commandeId,
        int $clientId,
        float $montantInitial,
        float $montantPaye
    ): int {

        $resteDu = $montantInitial - $montantPaye;

        if ($resteDu == 0) {
            $statut = 'SOLDEE';
        } else {
            $statut = 'NON_SOLDEE';
        }

        $sql = "INSERT INTO dettes
                (commande_id, client_id, montant_initial, montant_paye, reste_du, statut)
                VALUES
                (:commande_id, :client_id, :montant_initial, :montant_paye, :reste_du, :statut)";

        return Database::executeUpdate($sql, [
            'commande_id' => $commandeId,
            'client_id' => $clientId,
            'montant_initial' => $montantInitial,
            'montant_paye' => $montantPaye,
            'reste_du' => $resteDu,
            'statut' => $statut
        ]);
    }


}