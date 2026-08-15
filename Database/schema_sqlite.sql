-- ============================================================
-- StoreManager Pro — Schéma SQLite (fallback local, fichier erp.db)
-- ============================================================

PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS lignes_appro;
DROP TABLE IF EXISTS appros;
DROP TABLE IF EXISTS statuts_appro;
DROP TABLE IF EXISTS reglements;
DROP TABLE IF EXISTS modes_paiement;
DROP TABLE IF EXISTS dettes;
DROP TABLE IF EXISTS lignes_commande;
DROP TABLE IF EXISTS commandes;
DROP TABLE IF EXISTS produits;
DROP TABLE IF EXISTS clients;
DROP TABLE IF EXISTS fournisseurs;
DROP TABLE IF EXISTS utilisateurs;
DROP TABLE IF EXISTS roles;

-- ============================================================
-- RÔLES & UTILISATEURS
-- ============================================================

CREATE TABLE roles (
    id   INTEGER PRIMARY KEY AUTOINCREMENT,
    nom  TEXT NOT NULL UNIQUE
);

CREATE TABLE utilisateurs (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    nom_complet  TEXT NOT NULL,
    email        TEXT NOT NULL UNIQUE,
    mot_passe    TEXT NOT NULL,
    adresse      TEXT,
    tel          TEXT,
    role_id      INTEGER NOT NULL REFERENCES roles(id) ON DELETE RESTRICT
);

-- ============================================================
-- TIERS : CLIENTS & FOURNISSEURS
-- ============================================================

CREATE TABLE clients (
    id             INTEGER PRIMARY KEY AUTOINCREMENT,
    nom            TEXT NOT NULL,
    prenom         TEXT NOT NULL,
    email          TEXT,
    tel            TEXT NOT NULL,
    limite_credit  NUMERIC NOT NULL DEFAULT 0 CHECK (limite_credit >= 0)
);

CREATE TABLE fournisseurs (
    id       INTEGER PRIMARY KEY AUTOINCREMENT,
    nom      TEXT NOT NULL,
    email    TEXT,
    tel      TEXT NOT NULL,
    adresse  TEXT
);

-- ============================================================
-- CATALOGUE PRODUITS
-- ============================================================

CREATE TABLE produits (
    id              INTEGER PRIMARY KEY AUTOINCREMENT,
    libelle         TEXT NOT NULL,
    prix_vente      NUMERIC NOT NULL CHECK (prix_vente >= 0),
    quantite_stock  INTEGER NOT NULL DEFAULT 0 CHECK (quantite_stock >= 0)
);

-- ============================================================
-- RÉFÉRENTIELS (modes de paiement, statuts appro)
-- ============================================================

CREATE TABLE modes_paiement (
    id    INTEGER PRIMARY KEY AUTOINCREMENT,
    mode  TEXT NOT NULL UNIQUE
);

CREATE TABLE statuts_appro (
    id   INTEGER PRIMARY KEY AUTOINCREMENT,
    nom  TEXT NOT NULL UNIQUE
);

-- ============================================================
-- VENTES (Commandes + Lignes)
-- ============================================================

CREATE TABLE commandes (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    client_id        INTEGER NOT NULL REFERENCES clients(id) ON DELETE RESTRICT,
    utilisateur_id   INTEGER NOT NULL REFERENCES utilisateurs(id) ON DELETE RESTRICT,
    date_commande    TEXT NOT NULL DEFAULT (datetime('now')),
    montant_initial  NUMERIC NOT NULL CHECK (montant_initial >= 0),
    avance           NUMERIC NOT NULL DEFAULT 0 CHECK (avance >= 0)
);

CREATE TABLE lignes_commande (
    id            INTEGER PRIMARY KEY AUTOINCREMENT,
    commande_id   INTEGER NOT NULL REFERENCES commandes(id) ON DELETE CASCADE,
    produit_id    INTEGER NOT NULL REFERENCES produits(id) ON DELETE RESTRICT,
    qte_commande  INTEGER NOT NULL CHECK (qte_commande > 0),
    prix_reel     NUMERIC NOT NULL CHECK (prix_reel >= 0)
);

-- ============================================================
-- DETTES & RÈGLEMENTS
-- ============================================================

CREATE TABLE dettes (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    commande_id      INTEGER UNIQUE REFERENCES commandes(id) ON DELETE CASCADE,
    client_id        INTEGER NOT NULL REFERENCES clients(id) ON DELETE RESTRICT,
    montant_initial  NUMERIC NOT NULL CHECK (montant_initial >= 0),
    montant_paye     NUMERIC NOT NULL DEFAULT 0 CHECK (montant_paye >= 0),
    reste_du         NUMERIC NOT NULL CHECK (reste_du >= 0),
    statut           TEXT NOT NULL DEFAULT 'NON_SOLDEE' CHECK (statut IN ('NON_SOLDEE','SOLDEE')),
    date_creation    TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE reglements (
    id                INTEGER PRIMARY KEY AUTOINCREMENT,
    dette_id          INTEGER NOT NULL REFERENCES dettes(id) ON DELETE CASCADE,
    mode_paiement_id  INTEGER NOT NULL REFERENCES modes_paiement(id) ON DELETE RESTRICT,
    date              TEXT NOT NULL DEFAULT (datetime('now')),
    montant           NUMERIC NOT NULL CHECK (montant > 0)
);

-- ============================================================
-- APPROVISIONNEMENTS (BL + Lignes)
-- ============================================================

CREATE TABLE appros (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    fournisseur_id   INTEGER NOT NULL REFERENCES fournisseurs(id) ON DELETE RESTRICT,
    utilisateur_id   INTEGER NOT NULL REFERENCES utilisateurs(id) ON DELETE RESTRICT,
    statut_id        INTEGER NOT NULL REFERENCES statuts_appro(id) ON DELETE RESTRICT,
    ref_bl           TEXT NOT NULL UNIQUE,
    date_appro       TEXT NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE lignes_appro (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    appro_id    INTEGER NOT NULL REFERENCES appros(id) ON DELETE CASCADE,
    produit_id  INTEGER NOT NULL REFERENCES produits(id) ON DELETE RESTRICT,
    qte_appro   INTEGER NOT NULL CHECK (qte_appro > 0),
    qte_recu    INTEGER NOT NULL DEFAULT 0 CHECK (qte_recu >= 0),
    prix_reel   NUMERIC NOT NULL CHECK (prix_reel >= 0)
);

-- ============================================================
-- DONNÉES DE RÉFÉRENCE MINIMALES
-- ============================================================

INSERT INTO roles (nom) VALUES ('admin'), ('vente'), ('stock'), ('inventaire');
INSERT INTO modes_paiement (mode) VALUES ('Especes'), ('Wave'), ('Orange Money'), ('Virement');
INSERT INTO statuts_appro (nom) VALUES ('EN_COURS'), ('RECU');