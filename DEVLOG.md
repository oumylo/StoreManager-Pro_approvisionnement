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


##  1.2 — Schéma SQL PostgreSQL / SQLite

## Ce qui a été fait :
-Traduction du diagramme de classes PlantUML en scripts relationnels cibles pour deux SGBD différents en parallèle.

- Création du script schema.sql (PostgreSQL) : configuration fine des types de données (SERIAL pour l'auto-incrémentation, DECIMAL(10,2) pour la précision financière, TIMESTAMP pour les dates).

- Création du script schema_sqlite.sql (SQLite) : adaptation de la syntaxe au type  simplifié (INTEGER PRIMARY KEY AUTOINCREMENT).

- Implémentation de l'intégrité référentielle : application systématique de la contrainte ON DELETE RESTRICT sur toutes les clés étrangères (client_id, produit_id, role_id, etc.) pour empêcher la suppression accidentelle de données parentes liées à des transactions actives.

- Gestion de la contrainte 0..1 : application d'une contrainte UNIQUE sur la colonne commande_id de la table dettes pour modéliser proprement la relation optionnelle du diagramme de classes.

- Ordonnancement strict de la création des tables (des tables de base indépendantes vers les tables de détails imbriquées) pour respecter les dépendances de clés étrangères lors de l'exécution séquentielle.

- Création physique du dossier Database/ et génération avec succès de la base de données embarquée erp.db via le terminal Linux (sqlite3 Database/erp.db < schema_sqlite.sql).

##