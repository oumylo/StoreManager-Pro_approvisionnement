# Journal de Développement (DEVLOG)

**Nom & Prénom** : Oumy LO  
**Projet** : StoreManager Pro (ERP PHP/POO)

---

## 1. Suivi Chronologique des Phases

### [Vendredi - Phase 1] : Conception & BDD Fallback

#### Étape 1.1 — Conception UML


- **Ce qui a été fait** :
  - Analyse des fonctionnalités principales de StoreManager Pro.
  - Identification des quatre profils utilisateurs :
    - Admin Boutique
    - Chargé de Vente
    - Chargé de Stock
    - Chargé d'Inventaire
  - Réalisation des diagrammes de cas d'utilisation pour chaque profil.
  - Réalisation du diagramme de classes UML.
  - Identification des principales classes métier :
    - Utilisateur
    - Role
    - Client
    - Produit
    - Fournisseur
    - Commande
    - LigneCommande
    - Dette
    - Reglement
    - ModePaiement
    - Appro
    - LigneAppro
    - StatutAppro
  - Définition des associations  entre les différentes classes.
  - Organisation des diagrammes dans le dossier `/docs/`.

- **Difficultés / Obstacles** :
  - Il a fallu distinguer les fonctionnalités des quatre profils afin de ne pas donner à chaque utilisateur les mêmes droits.
  - La modélisation des commandes et des approvisionnements a nécessité l'utilisation des classes `LigneCommande` et `LigneAppro`.
 
  - La gestion des rôles a conduit à ajouter les classes `Utilisateur` et `Role` au diagramme de classes.