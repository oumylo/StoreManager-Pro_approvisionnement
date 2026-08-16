
<?php

require_once dirname(__DIR__) . '/Core/Database.php';

require_once dirname(__DIR__) . '/Model/Repository/ProduitRepository.php';
require_once dirname(__DIR__) . '/Model/Repository/ClientRepository.php';
require_once dirname(__DIR__) . '/Model/Repository/CommandeRepository.php';
require_once dirname(__DIR__) . '/Model/Repository/DetteRepository.php';


class VenteService
{
    private PDO $pdo;

    private ProduitRepository $produitRepo;
    private ClientRepository $clientRepo;
    private CommandeRepository $commandeRepo;
    private DetteRepository $detteRepo;


    public function __construct()
    {
        
        $this->pdo = connexionDB();

        
        $this->produitRepo = new ProduitRepository();
        $this->clientRepo = new ClientRepository();
        $this->commandeRepo = new CommandeRepository();
        $this->detteRepo = new DetteRepository();
    }


    public function validerVente( int $clientId, int $utilisateurId, array $panier, float $montantVerse): int {

        if (empty($panier)) {
            throw new InvalidArgumentException("Le panier est vide.");
        }


        $this->pdo->beginTransaction();


        try {

            $montantTotal = 0;

            $lignesAValider = [];


            foreach ($panier as $article) {

                $produitId = $article['produit_id'];
                $quantite = $article['qte'];


                $produit = $this->produitRepo->findById($produitId);


                if ($produit === null) {
                    throw new RuntimeException(
                        "Produit introuvable."
                    );
                }

                if ($quantite > $produit->getQuantiteStock()) {

                    throw new RuntimeException(
                        "Stock insuffisant pour "
                        . $produit->getLibelle()
                    );
                }

                $sousTotal = $produit->getPrixVente() * $quantite;


                $montantTotal = $montantTotal + $sousTotal;

                $lignesAValider[] = [
                    'produit' => $produit,
                    'qte' => $quantite,
                    'prix' => $produit->getPrixVente()
                ];
            }


            $montantACredit = $montantTotal - $montantVerse;

            if ($montantACredit > 0) {

                $client = $this->clientRepo->findById($clientId);


                if ($client === null) {
                    throw new RuntimeException(
                        "Client introuvable."
                    );
                }

                $creditDejaUtilise =
                    $this->clientRepo->getCreditUtilise($clientId);

                $creditAutorise = $client->peutPrendreCredit(
                    $montantACredit,
                    $creditDejaUtilise
                );


                if (!$creditAutorise) {
                    throw new RuntimeException(
                        "Limite de crédit dépassée pour ce client."
                    );
                }
            }


            $commandeId = $this->commandeRepo->create(
                $clientId,
                $utilisateurId,
                $montantTotal,
                $montantVerse
            );

            foreach ($lignesAValider as $ligne) {

                $produit = $ligne['produit'];
                $quantite = $ligne['qte'];
                $prix = $ligne['prix'];

                $this->commandeRepo->addLigne(
                    $commandeId,
                    $produit->getId(),
                    $quantite,
                    $prix
                );

                $nouveauStock =
                    $produit->getQuantiteStock() - $quantite;

                $this->produitRepo->updateStock(
                    $produit->getId(),
                    $nouveauStock
                );
            }


            if ($montantACredit > 0) {

                $this->detteRepo->create(
                    $commandeId,
                    $clientId,
                    $montantTotal,
                    $montantVerse
                );
            }


            $this->pdo->commit();


            return $commandeId;


        } catch (Exception $e) {

            $this->pdo->rollBack();


            throw $e;
        }
    }
}

