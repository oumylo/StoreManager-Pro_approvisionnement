
<?php

require_once dirname(__DIR__) . '/Entity/Dette.php';
require_once dirname(dirname(__DIR__)) . '/Core/Database.php';


class DetteRepository
{
    private PDO $pdo;


    public function __construct()
    {
        $this->pdo = connexionDB();
    }


   
    public function create( int $commandeId, int $clientId, float $montantInitial, float $montantPaye): int {

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


        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            'commande_id' => $commandeId,
            'client_id' => $clientId,
            'montant_initial' => $montantInitial,
            'montant_paye' => $montantPaye,
            'reste_du' => $resteDu,
            'statut' => $statut
        ]);


        $id = $this->pdo->lastInsertId();

        return (int) $id;
    }


   
    public function findById(int $id): ?Dette
    {
        $sql = "SELECT * FROM dettes WHERE id = ?";


        $stmt = $this->pdo->prepare($sql);


        $stmt->execute([$id]);


        $stmt->setFetchMode(PDO::FETCH_CLASS, Dette::class);


        $dette = $stmt->fetch();


        if ($dette === false) {
            return null;
        }


        return $dette;
    }
}

