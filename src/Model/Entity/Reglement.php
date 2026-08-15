<?php

require_once __DIR__ . '/Dette.php';
require_once __DIR__ . '/ModePaiement.php';

class Reglement
{
    private ?int $id;
    private DateTime $dateReglement;
    private float $montant;
    private Dette $dette;
    private ModePaiement $modePaiement;

    public function __construct( ?int $id, DateTime $dateReglement, float $montant, Dette $dette, ModePaiement $modePaiement) {
        
        $this->id = $id;
        $this->dateReglement = $dateReglement;
        $this->dette = $dette;
        $this->modePaiement = $modePaiement;
        $this->setMontant($montant);
    }

  
    // GETTERS

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateReglement(): DateTime
    {
        return $this->dateReglement;
    }

    public function getMontant(): float
    {
        return $this->montant;
    }

    public function getDette(): Dette
    {
        return $this->dette;
    }

    public function getModePaiement(): ModePaiement
    {
        return $this->modePaiement;
    }

    // SETTERS
   
    public function setMontant(float $montant): void
    {
        if ($montant <= 0) {
            throw new InvalidArgumentException(
                "Le montant du règlement doit être supérieur à zéro."
            );
        }

        if ($montant > $this->dette->getResteDu()) {
            throw new InvalidArgumentException(
                "Le règlement dépasse le montant restant de la dette."
            );
        }

        $this->montant = $montant;
    }

    // MÉTHODES MÉTIER

    public function effectuerPaiement(): void
    {
        $this->dette->rembourser($this->montant);
    }
}