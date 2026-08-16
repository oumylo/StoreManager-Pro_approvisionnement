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

        
        $this->produitRepo = new ProduitRepository($this->pdo);
        $this->clientRepo = new ClientRepository($this->pdo);
        $this->commandeRepo = new CommandeRepository($this->pdo);
        $this->detteRepo = new DetteRepository($this->pdo);
    }


    public function validerVente(int $clientId, int $utilisateurId, array $panier, float $montantVerse): int {

        

        if (empty($panier)) {
            throw new Exception("Le panier est vide.");
        }

        if ($montantVerse < 0) {
            throw new Exception("Le montant versé ne peut pas être négatif.");
        }


        $this->pdo->beginTransaction();


        try {

            $montantTotal = 0;

            $lignesAValider = [];


            foreach ($panier as $article) {

                $produitId = (int) $article['produit_id'];
                $quantite = (int) $article['qte'];


                if ($quantite <= 0) {
                    throw new Exception(
                        "La quantité doit être supérieure à zéro."
                    );
                }


                $produit = $this->produitRepo->findById($produitId);


                if ($produit === null) {
                    throw new Exception(
                        "Produit introuvable."
                    );
                }

                if ($quantite > $produit->getQuantiteStock()) {
                    throw new Exception(
                        "Stock insuffisant pour "
                        . $produit->getLibelle()
                    );
                }


                $prix = $produit->getPrixVente();

                $sousTotal = $prix * $quantite;

                $montantTotal += $sousTotal;


                $lignesAValider[] = [
                    'produit' => $produit,
                    'qte' => $quantite,
                    'prix' => $prix
                ];
            }


            if ($montantVerse > $montantTotal) {
                throw new Exception(
                    "Le montant versé ne peut pas dépasser le montant total."
                );
            }


            $montantACredit = $montantTotal - $montantVerse;


            if ($montantACredit > 0) {

                $client = $this->clientRepo->findById($clientId);

                if ($client === null) {
                    throw new Exception(
                        "Client introuvable."
                    );
                }

                $creditDejaUtilise =
                    $this->clientRepo->getCreditUtilise($clientId);


                if (!$client->peutPrendreCredit(
                    $montantACredit,
                    $creditDejaUtilise
                )) {

                    throw new Exception(
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


        } catch (Throwable $e) {

          
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw $e;
        }
    }
}