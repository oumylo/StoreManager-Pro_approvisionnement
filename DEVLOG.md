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

- Création du dossier Database/ et initialisation avec succès de la base SQLite erp.db à partir du script schema_sqlite.sql à l'aide de la commande sqlite3 Database/erp.db < schema_sqlite.sql. 
Vérification de la présence des 13 tables avec la commande .tables et on a 
appros
clients
commandes
dettes
fournisseurs
lignes_appro
lignes_commande
modes_paiement
produits
reglements
roles
statuts_appro
utilisateurs.

## ## 1.3 — Database Singleton & Fallback Automatique

### Ce qui a été fait :

* Création de la classe `Database` dans `src/Core/Database.php`.

* Mise en place du pattern **Singleton** afin de garantir qu'une seule instance de la classe `Database` soit utilisée pendant l'exécution de l'application.

* Déclaration d'une instance statique avec :

  ```php
  private static ?Database $instance = null;
  ```

* Mise en place d'un constructeur privé :

  ```php
  private function __construct()
  ```

  afin d'empêcher la création directe de plusieurs instances avec `new Database()`.

* Création de la méthode `getInstance()` permettant de récupérer l'unique instance de `Database` :

  ```php
  public static function getInstance(): Database
  {
      if (self::$instance === null) {
          self::$instance = new Database();
      }

      return self::$instance;
  }
  ```

* Mise en place d'une connexion PDO prioritaire vers PostgreSQL avec la base `storemanager`.

* Configuration de PDO avec :

  * `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC`
  * `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`

* Mise en place d'un mécanisme `try/catch` permettant de détecter une erreur de connexion PostgreSQL.

* Implémentation du **fallback automatique vers SQLite** lorsque PostgreSQL n'est pas disponible :

  text
  PostgreSQL
       ↓
  Échec de connexion
       ↓
  SQLite
       ↓
  Database/erp.db


* Utilisation du fichier `Database/erp.db` comme base de secours.

* Création de la méthode `getConnection()` permettant de récupérer la connexion PDO.

* Conservation de la fonction `connexionDB()` afin de maintenir une utilisation simple de la connexion dans le reste de l'application :

   php
  function connexionDB(): PDO
  {
      return Database::getInstance()->getConnection();
  }
  

### Tests réalisés :

* Test de connexion avec PostgreSQL :

  text
  Connexion réussie !
  Base utilisée : pgsql
  

* Test du fallback en provoquant volontairement une erreur de connexion PostgreSQL
  text
  Connexion réussie !
  Base utilisée : sqlite
 

* Vérification que les deux bases possèdent les 13 tables nécessaires au fonctionnement de l'application.

### Difficultés / Obstacles :

* Compréhension du fonctionnement du pattern Singleton et de la différence entre une instance unique de `Database` et une connexion PDO.

* Compréhension du mécanisme de fallback permettant de passer automatiquement de PostgreSQL à SQLite en cas d'échec de connexion.

* Compréhension du rôle de `PDO::FETCH_ASSOC`. Ce mode reste utilisé par défaut dans la classe `Database`, tandis que la transformation directe des résultats SQL en entités POO avec `PDO::FETCH_CLASS` des repositories.

### Résultat :

Le mécanisme de connexion est opérationnel. L'application utilise PostgreSQL comme base principale et peut automatiquement basculer vers SQLite lorsque la connexion PostgreSQL échoue.


## ---

#  PHASE 2 : SAMEDI — Cœur POO & Ventes POS

## 2.1 — Entités POO avec encapsulation et méthodes métier

### Ce qui a été fait :

* Création du dossier `src/Model/Entity/` destiné aux classes représentant les entités métier de l'application.

* Création des principales entités POO correspondant aux tables de la base de données :

  - `Produit.php`
  - `Client.php`
  - `Fournisseur.php`
  - `Role.php`
  - `Utilisateur.php`
  - `ModePaiement.php`
  - `StatutAppro.php`
  - `Commande.php`
  - `Dette.php`
  - `Reglement.php`
  - `Appro.php`
  - `LigneCommande.php`
  - `LigneAppro.php`

* Mise en place de l'encapsulation avec des propriétés déclarées en `private`.

* Création des constructeurs permettant d'initialiser les entités.

* Création des getters permettant d'accéder aux données privées des entités.

* Création des setters permettant de modifier les données tout en contrôlant leur validité.

* Ajout de validations métier dans les setters afin d'empêcher les données incohérentes.

### Exemples de règles métier implémentées :

* Un produit ne peut pas avoir un prix de vente négatif.
* Le stock d'un produit ne peut pas être négatif.
* Une quantité commandée doit être supérieure à zéro.
* Une avance ne peut pas dépasser le montant de la commande.
* Une dette ne peut pas avoir un montant payé supérieur à son montant initial.
* Un règlement doit être supérieur à zéro.
* Un règlement ne peut pas dépasser le reste dû d'une dette.
* Un client doit avoir un nom, un prénom, un email et un téléphone valides.
* Un fournisseur doit avoir un nom, un email, un téléphone et une adresse valides.

### Méthodes métier ajoutées :

* `Produit`
  - `augmenterStock()`
  - `diminuerStock()`

* `Client`
  - `getCreditDisponible()`
  - `peutPrendreCredit()`

* `Commande`
  - `getResteAPayer()`
  - `estPayee()`
  - `estAcredit()`
  - `ajouterAvance()`

* `Dette`
  - `rembourser()`
  - `estSoldee()`
  - calcul automatique du `resteDu`
  - mise à jour automatique du `statut`

* `Reglement`
  - `effectuerPaiement()`

* `LigneCommande`
  - `calculerSousTotal()`
  - `changerQuantite()`

* `Appro`
  - `changerStatut()`

* `Role`
  - `estAdmin()`
  - `estVente()`
  - `estStock()`
  - `estInventaire()`

* `StatutAppro`
  - `estEnAttente()`
  - `estRecu()`
  - `estAnnule()`

* `Utilisateur`
  - `estAdmin()`
  - `estVente()`
  - `estStock()`
  - `estInventaire()`

### Principe POO respecté :

Les entités ne contiennent aucune requête SQL et ne communiquent pas directement avec la base de données.

Elles représentent uniquement les données et les règles métier de l'application.

La communication avec PostgreSQL ou SQLite sera réalisée dans les classes `Repository`.

Ainsi :

    Entity
       ↓
    Représente les données + règles métier

    Repository
       ↓
    Communique avec la base de données


### Résultat :

Les principales entités métier de StoreManager Pro sont maintenant implémentées en POO avec encapsulation, validations et méthodes métier.

Les entités sont indépendantes de la base de données et pourront être utilisées par les Repository pour effectuer des opérations .


##