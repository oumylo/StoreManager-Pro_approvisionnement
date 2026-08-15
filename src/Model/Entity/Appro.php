<?php

require_once __DIR__ . '/Fournisseur.php';
require_once __DIR__ . '/StatutAppro.php';
require_once __DIR__ . '/Utilisateur.php';

class Appro
{
    private ?int $id;
    private string $refBL;
    private DateTime $dateAppro;
    private Fournisseur $fournisseur;
    private StatutAppro $statutAppro;
    private Utilisateur $utilisateur;

    public function __construct( ?int $id, string $refBL, DateTime $dateAppro, Fournisseur $fournisseur, StatutAppro $statutAppro,Utilisateur $utilisateur) {
        
        $this->id = $id;
        $this->setRefBL($refBL);
        $this->dateAppro = $dateAppro;
        $this->fournisseur = $fournisseur;
        $this->statutAppro = $statutAppro;
        $this->utilisateur = $utilisateur;
    }


    // GETTERS


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRefBL(): string
    {
        return $this->refBL;
    }

    public function getDateAppro(): DateTime
    {
        return $this->dateAppro;
    }

    public function getFournisseur(): Fournisseur
    {
        return $this->fournisseur;
    }

    public function getStatutAppro(): StatutAppro
    {
        return $this->statutAppro;
    }

    public function getUtilisateur(): Utilisateur
    {
        return $this->utilisateur;
    }


    // SETTERS

    public function setRefBL(string $refBL): void
    {
        if (trim($refBL) === '') {
            throw new InvalidArgumentException(
                "La référence du bon de livraison est obligatoire."
            );
        }

        $this->refBL = trim($refBL);
    }

    // MÉTHODES MÉTIER

    public function changerStatut(StatutAppro $statutAppro): void
    {
        $this->statutAppro = $statutAppro;
    }
}