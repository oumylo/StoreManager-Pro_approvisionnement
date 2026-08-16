# Journal de Développement (DEVLOG)
**Nom & Prénom** : Oumy LO
**Projet** : StoreManager Pro (ERP PHP/POO)


## 1. Suivi Chronologique des Phases

### [Vendredi - Phase 1] : Conception & BDD Fallback

- **Heure de réalisation** : 19h00 - 23h00
- **Ce qui a été fait** :
  - Conception UML (Use Case + Classes).
  - Écriture des scripts SQL `schema.sql` (PostgreSQL) et `schema_sqlite.sql` (SQLite).
  - Création de la classe `Database` en Singleton avec fallback automatique vers SQLite.
- **Difficultés / Obstacles** :
  - Distinguer les droits des quatre profils (Admin, Vente, Stock, Inventaire) sur le diagramme de cas d'utilisation.
  - Comprendre la différence entre une instance unique de `Database` et une connexion PDO.
  - Comprendre le mécanisme de fallback PostgreSQL → SQLite.

---

####  Step 1.1 (19h00 - 20h30) : Conception UML


**Ce qui a été fait :**
- Analyse des fonctionnalités principales de StoreManager Pro.
- Identification des quatre profils utilisateurs : Admin Boutique, Chargé de Vente, Chargé de Stock, Chargé d'Inventaire.
- Réalisation d'un diagramme de cas d'utilisation pour chaque profil.
- Réalisation du diagramme de classes UML avec les classes métier : `Utilisateur`, `Role`, `Client`, `Produit`, `Fournisseur`, `Commande`, `LigneCommande`, `Dette`, `Reglement`, `ModePaiement`, `Appro`, `LigneAppro`, `StatutAppro`.
- Définition des associations entre les classes.
- Rangement des diagrammes dans le dossier `/docs/`.

**Difficultés / Obstacles :**
- Distinguer les fonctionnalités des quatre profils pour ne pas donner les mêmes droits à tout le monde.
- Modéliser correctement les commandes et les approvisionnements avec les classes `LigneCommande` et `LigneAppro`.
- Ajouter les classes `Utilisateur` et `Role` pour gérer les droits par profil.

---

####  Step 1.2 (20h30 - 22h00) : Schéma SQL PostgreSQL / SQLite



**Ce qui a été fait :**
- Traduction du diagramme de classes en scripts SQL, pour deux bases différentes.
- Création de `schema.sql` (PostgreSQL) : `SERIAL` pour l'auto-incrémentation, `DECIMAL(10,2)` pour les montants, `TIMESTAMP` pour les dates.
- Création de `schema_sqlite.sql` (SQLite) : `INTEGER PRIMARY KEY AUTOINCREMENT` à la place de `SERIAL`.
- Ajout de la contrainte `ON DELETE RESTRICT` sur toutes les clés étrangères (`client_id`, `produit_id`, `role_id`, etc.) pour empêcher la suppression d'une donnée encore utilisée ailleurs.
- Ajout d'une contrainte `UNIQUE` sur `commande_id` dans la table `dettes`, pour respecter la relation 0..1 du diagramme de classes.
- Création des tables dans le bon ordre : d'abord les tables indépendantes, puis les tables qui dépendent d'elles par clé étrangère.

- Vérification des 13 tables avec `.tables` : `appros`, `clients`, `commandes`, `dettes`, `fournisseurs`, `lignes_appro`, `lignes_commande`, `modes_paiement`, `produits`, `reglements`, `roles`, `statuts_appro`, `utilisateurs`.

**Difficultés / Obstacles :**
- Respecter l'ordre de création des tables à cause des clés étrangères.
- Adapter la syntaxe SQL entre PostgreSQL et SQLite (types de données différents).

---

####  Step 1.3 (22h00 - 23h00) : Singleton Database & Fallback Automatique

**Ce qui a été fait :**
- Création de la classe `Database` dans `src/Core/Database.php`.
- Mise en place du pattern **Singleton** pour garantir qu'une seule instance de `Database` existe pendant toute l'exécution.
- Déclaration d'une instance statique :
  
  private static ?Database $instance = null;
  
- Constructeur privé pour empêcher `new Database()` en dehors de la classe :
  
  private function __construct()
  
- Méthode `getInstance()` pour récupérer l'unique instance :
  
  public static function getInstance(): Database
  {
      if (self::$instance === null) {
          self::$instance = new Database();
      }
      return self::$instance;
  }
  
- Connexion PDO prioritaire vers PostgreSQL (base `storemanager`), 

- Mécanisme `try/catch` : si la connexion PostgreSQL échoue, bascule automatique sur SQLite (`Database/erp.db`) :
  
 

**Tests réalisés :**
- Connexion avec PostgreSQL disponible → `Connexion réussie ! Base utilisée : pgsql`.
- Connexion avec PostgreSQL indisponible (provoqué volontairement) → `Connexion réussie ! Base utilisée : sqlite`.
- Vérification que les deux bases contiennent bien les 13 tables.

**Difficultés / Obstacles :**

- Comprendre le rôle de `PDO::FETCH_ASSOC` : c'est le mode par défaut de la classe `Database`, alors que les Repository utilisent plutôt `PDO::FETCH_CLASS` pour transformer directement le résultat SQL en objets métier.

**Résultat :** Le mécanisme de connexion fonctionne. L'application utilise PostgreSQL en priorité et bascule automatiquement sur SQLite si besoin.

---

###  [Samedi - Phase 2] : POO, Repositories & Ventes POS

- **Heure de réalisation** : 09h00 - 20h00
- **Ce qui a été fait** :
  - Création des entités POO (`Produit`, `Client`, `Fournisseur`, `Commande`, `Dette`, etc.) avec encapsulation et règles métier.
  - Création des Repository `ProduitRepository`, `ClientRepository`, `FournisseurRepository` avec `PDO::FETCH_CLASS`.
- **Difficultés / Obstacles** :
  - Rendre les entités compatibles avec `PDO::FETCH_CLASS`.
 
---

#### Step 2.1 (09h00 - 11h00) : Entités POO Pure

**Ce qui a été fait :**
- Création du dossier `src/Model/Entity/`.
- Création des entités : `Produit`, `Client`, `Fournisseur`, `Role`, `Utilisateur`, `ModePaiement`, `StatutAppro`, `Commande`, `Dette`, `Reglement`, `Appro`, `LigneCommande`, `LigneAppro`.
- Toutes les propriétés sont `private` (encapsulation).
- Chaque entité a un constructeur, des getters et des setters.
- Ajout de validations métier dans les setters pour empêcher les données incohérentes.

**Exemples de règles métier :**
- Un produit ne peut pas avoir un prix de vente négatif.
- Le stock d'un produit ne peut pas être négatif.
- Une quantité commandée doit être supérieure à zéro.
- Une avance ne peut pas dépasser le montant de la commande.
- Une dette ne peut pas avoir un montant payé supérieur à son montant initial.
- Un règlement doit être supérieur à zéro.
- Un client doit avoir un nom, un prénom, un email et un téléphone valides.
- Un fournisseur doit avoir un nom, un email, un téléphone et une adresse valides.

**Méthodes métier ajoutées :**
- `Produit` : `augmenterStock()`, `diminuerStock()`
- `Client` : `getCreditDisponible()`, `peutPrendreCredit()`
- `Commande` : `getResteAPayer()`, `estPayee()`, `estAcredit()`, `ajouterAvance()`
- `Dette` : `rembourser()`, `estSoldee()`, calcul automatique du `reste_du` et mise à jour automatique du `statut`
- `Reglement` : logique de paiement liée à une dette
- `LigneCommande` : `getTotal()`
- `LigneAppro` : `calculerSousTotal()`, `ajouterReception()`, `estEntierementRecu()`
- `Appro` : `changerStatut()`
- `Role` : `estAdmin()`, `estVente()`, `estStock()`, `estInventaire()`
- `StatutAppro` : `estEnCours()`, `estRecu()`
- `Utilisateur` : `estAdmin()`, `estVente()`, `estStock()`, `estInventaire()`

**Principe POO respecté :**
Les entités ne contiennent aucune requête SQL. Elles représentent uniquement les données et les règles métier. La communication avec la base de données est faite uniquement par les classes `Repository`.


**Difficultés / Obstacles :**
- Rendre les entités compatibles avec `PDO::FETCH_CLASS` : PDO remplit d'abord les propriétés avec les colonnes SQL, puis appelle le constructeur seulement après. Il fallait donc que le nom des propriétés corresponde exactement au nom des colonnes SQL, et que le constructeur puisse être appelé sans aucun argument (tous les paramètres doivent avoir une valeur par défaut `= null`).


**Résultat :** Les entités sont opérationnelles, indépendantes de la base de données, et compatibles avec `PDO::FETCH_CLASS`.

---

####  Step 2.2 (11h00 - 13h00) : Repositories & SQL Sécurisé


**Ce qui a été fait :**
- Création du dossier `src/Model/Repository/`.
- Création des trois Repository demandés : `ProduitRepository`, `ClientRepository`, `FournisseurRepository`.
- Chaque Repository a une propriété privée `PDO $pdo`.
- Le constructeur **ne reçoit aucun paramètre**. Il récupère directement la connexion PDO du Singleton `Database` grâce à la fonction `connexionDB()` :
  
  private PDO $pdo;

  public function __construct()
  {
      $this->pdo = connexionDB();
  }
  
- Ainsi chaque Repository utilise automatiquement la bonne connexion (PostgreSQL ou SQLite selon le fallback), sans avoir besoin qu'on la lui transmette de l'extérieur.

**Méthodes implémentées pour `ProduitRepository` :**
- `findAll()` : récupérer tous les produits.
- `findById()` : récupérer un produit par son id.
- `create()` : ajouter un produit.
- `update()` : modifier un produit.
- `updateStock()` : modifier uniquement le stock d'un produit.
- `delete()` : supprimer un produit.

**Méthodes implémentées pour `ClientRepository` :**
- `findAll()` : récupérer tous les clients.
- `findById()` : récupérer un client par son id.
- `findByEmail()` : vérifier si un email existe déjà.
- `create()` : ajouter un client.
- `update()` : modifier un client.
- `delete()` : supprimer un client.
- `getCreditUtilise()` : calculer le total des dettes non soldées d'un client.

**Méthodes implémentées pour `FournisseurRepository` :**
- `findAll()` : récupérer tous les fournisseurs.
- `findById()` : récupérer un fournisseur par son id.
- `create()` : ajouter un fournisseur.
- `update()` : modifier un fournisseur.
- `delete()` : supprimer un fournisseur.

**Utilisation de `PDO::FETCH_CLASS` :**
Toutes les méthodes `findAll()` et `findById()` transforment directement le résultat SQL en objet PHP, sans passer par un tableau associatif :

$stmt = $this->pdo->query("SELECT * FROM produits");
$stmt->setFetchMode(PDO::FETCH_CLASS, Produit::class);
return $stmt->fetchAll();


Toutes les requêtes qui insèrent, modifient ou suppriment des données utilisent des requêtes préparées (`prepare()` + `execute()`) 

**Difficultés / Obstacles :**

- Erreur de chemin lors des `require_once` entre les dossiers `Entity`, `Repository` et `Core`. 

**Test réalisé :**
Exécution de `test.php` avec `ProduitRepository::findAll()` : la liste des produits s'affiche correctement avec le libellé, le prix de vente et le stock de chaque produit.

**Résultat :** La couche Repository demandée pour l'étape 2.2 est opérationnelle.