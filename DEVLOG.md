# Journal de Développement (DEVLOG)
**Nom & Prénom** : Oumy LO
**Projet** : StoreManager Pro (ERP PHP/POO)


## 1. Suivi Chronologique des Phases

### [Vendredi - Phase 1] : Conception & BDD Fallback

- **Heure de réalisation** : 19h00 - 23h00
- **Ce qui a été fait** :
  - Conception UML (Use Case + Classes).
  - Écriture des scripts SQL `schema.sql` (PostgreSQL) et `schema_sqlite.sql` (SQLite).
 
- **Difficultés / Obstacles** :
  - Distinguer les droits des quatre profils (Admin, Vente, Stock, Inventaire) sur le diagramme de cas d'utilisation.
  
---

####  Step 1.1 (19h00 - 20h30) : Conception UML


**Ce qui a été fait :**

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

- Création de `schema_sqlite.sql` (SQLite) : `INTEGER PRIMARY KEY AUTOINCREMENT` à la place de `SERIAL`.



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
  

  
- Connexion PDO prioritaire vers PostgreSQL (base `storemanager`), 

- Mécanisme `try/catch` : si la connexion PostgreSQL échoue, bascule automatique sur SQLite (`Database/erp.db`) :
  
 

**Tests réalisés :**
- Connexion avec PostgreSQL disponible → `Connexion réussie ! Base utilisée : pgsql`.


**Difficultés / Obstacles :**

- Comprendre le rôle de `PDO::FETCH_ASSOC` : c'est le mode par défaut de la classe `Database`, alors que les Repository utilisent plutôt `PDO::FETCH_CLASS` pour transformer directement le résultat SQL en objets métier.

**dificulter  :** quand les utilisé 

---

###  [Samedi - Phase 2] : POO, Repositories & Ventes POS

- **Heure de réalisation** : 09h00 - 20h00
- **Ce qui a été fait** :
  - Création des entités POO (`Produit`, `Client`, `Fournisseur`, `Commande`, `Dette`, etc.) 
  - Création des Repository `ProduitRepository`, `ClientRepository`, `FournisseurRepository` `.

- **Difficultés / Obstacles** :
  - Rendre les entités compatibles avec `PDO::FETCH_CLASS`.
 
---

#### Step 2.1 (09h00 - 11h00) : Entités POO Pure

**Ce qui a été fait :**
- Création du dossier `src/Model/Entity/`.
- Création des entités : `Produit`, `Client`, `Fournisseur`, `Role`, `Utilisateur`, `ModePaiement`, `StatutAppro`, `Commande`, `Dette`, `Reglement`, `Appro`, `LigneCommande`, `LigneAppro`.

- Chaque entité a un constructeur, des getters et des setters.
- Ajout de validations métier dans les setters pour empêcher les données incohérentes.

**Exemples de règles métier :**
- Un produit ne peut pas avoir un prix de vente négatif.
- Le stock d'un produit ne peut pas être négatif.
- Une quantité commandée doit être supérieure à zéro.
- Une avance ne peut pas dépasser le montant de la commande.
- Une dette ne peut pas avoir un montant payé supérieur à son montant initial.



**Principe POO respecté :**
Les entités ne contiennent aucune requête SQL. Elles représentent uniquement les données et les règles métier. La communication avec la base de données est faite uniquement par les classes `Repository`.

---

####  Step 2.2 (11h00 - 13h00) : Repositories & SQL Sécurisé


**Ce qui a été fait :**
- Création du dossier `src/Model/Repository/`.
- Création des trois Repository demandés : `ProduitRepository`, `ClientRepository`, `FournisseurRepository`.




 

**Difficultés / Obstacles :**

- Erreur de chemin lors des `require_once` entre les dossiers `Entity`, `Repository` et `Core`. 




