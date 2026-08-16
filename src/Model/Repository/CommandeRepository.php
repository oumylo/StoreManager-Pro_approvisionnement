
<?php

require_once dirname(__DIR__) . '/Entity/Commande.php';
require_once dirname(__DIR__) . '/Entity/LigneCommande.php';
require_once dirname(dirname(__DIR__)) . '/Core/Database.php';


class CommandeRepository
{
    private PDO $pdo;


    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    public function create(
        int $clientId,
        int $utilisateurId,
        float $montantInitial,
        float $avance
    ): int {

        $sql = "INSERT INTO commandes
                (client_id, utilisateur_id, montant_initial, avance)
                VALUES
                (:client_id, :utilisateur_id, :montant_initial, :avance)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'client_id' => $clientId,
            'utilisateur_id' => $utilisateurId,
            'montant_initial' => $montantInitial,
            'avance' => $avance
        ]);

        $id = $this->pdo->lastInsertId();

        return (int) $id;
    }



    public function addLigne(
        int $commandeId,
        int $produitId,
        int $qte,
        float $prixReel
    ): int {

        $sql = "INSERT INTO lignes_commande
                (commande_id, produit_id, qte_commande, prix_reel)
                VALUES
                (:commande_id, :produit_id, :qte_commande, :prix_reel)";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'commande_id' => $commandeId,
            'produit_id' => $produitId,
            'qte_commande' => $qte,
            'prix_reel' => $prixReel
        ]);

        $id = $this->pdo->lastInsertId();

        return (int) $id;
    }


   
    public function findById(int $id): ?Commande
    {
        $sql = "SELECT * FROM commandes WHERE id = ?";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([$id]);

        $stmt->setFetchMode(PDO::FETCH_CLASS, Commande::class);

        $commande = $stmt->fetch();

        if ($commande === false) {
            return null;
        }

        return $commande;
    }
}
