<?php

require_once __DIR__ . '/Client.php';
require_once __DIR__ . '/Utilisateur.php';

class Commande
{
    private ?int $id;
    private DateTime $dateCommande;
    private float $montantInitial;
    private float $avance;
    private Client $client;
    private Utilisateur $utilisateur;

    public function __construct( ?int $id, DateTime $dateCommande, float $montantInitial, float $avance, Client $client, Utilisateur $utilisateur) {

        $this->id = $id;
        $this->dateCommande = $dateCommande;

        $this->setMontantInitial($montantInitial);
        $this->setAvance($avance);

        $this->client = $client;
        $this->utilisateur = $utilisateur;
    }

  
    // GETTERS
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCommande(): DateTime
    {
        return $this->dateCommande;
    }

    public function getMontantInitial(): float
    {
        return $this->montantInitial;
    }

    public function getAvance(): float
    {
        return $this->avance;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getUtilisateur(): Utilisateur
    {
        return $this->utilisateur;
    }

  
    // SETTERS
   

    public function setMontantInitial(float $montantInitial): void
    {
        if ($montantInitial < 0) {
            throw new InvalidArgumentException(
                "Le montant de la commande ne peut pas être négatif."
            );
        }

        $this->montantInitial = $montantInitial;
    }

    public function setAvance(float $avance): void
    {
        if ($avance < 0) {
            throw new InvalidArgumentException(
                "L'avance ne peut pas être négative."
            );
        }

        if ($avance > $this->montantInitial) {
            throw new InvalidArgumentException(
                "L'avance ne peut pas dépasser le montant de la commande."
            );
        }

        $this->avance = $avance;
    }

   
    // MÉTHODES MÉTIER
    

    public function getResteAPayer(): float
    {
        return $this->montantInitial - $this->avance;
    }

   
    public function estPayee(): bool
    {
        return $this->getResteAPayer() == 0;
    }

    
    public function estAcredit(): bool
    {
        return $this->avance < $this->montantInitial;
    }

    
    public function ajouterAvance(float $montant): void
    {
        if ($montant <= 0) {
            throw new InvalidArgumentException(
                "Le montant de l'avance doit être supérieur à zéro."
            );
        }

        if ($this->avance + $montant > $this->montantInitial) {
            throw new InvalidArgumentException(
                "L'avance dépasse le montant restant à payer."
            );
        }

        $this->avance += $montant;
    }

   
   
}