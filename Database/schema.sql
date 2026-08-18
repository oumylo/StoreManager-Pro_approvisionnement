CREATE TABLE roles (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE clients (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE,
    tel VARCHAR(20),
    limite_credit DECIMAL(12,2) NOT NULL DEFAULT 0.00
        CHECK (limite_credit >= 0)
);

CREATE TABLE modes_paiement (
    id SERIAL PRIMARY KEY,
    mode VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE produits (
    id SERIAL PRIMARY KEY,
    libelle VARCHAR(150) NOT NULL,
    prix_vente DECIMAL(10,2) NOT NULL
        CHECK (prix_vente >= 0),
    stock_initial INT NOT NULL DEFAULT 0
        CHECK (stock_initial >= 0)
);

CREATE TABLE fournisseurs (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE,
    tel VARCHAR(20),
    adresse TEXT
);

CREATE TABLE statuts_appro (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE utilisateurs (
    id SERIAL PRIMARY KEY,
    nom_complet VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    mot_passe VARCHAR(255) NOT NULL,
    adresse TEXT,
    tel VARCHAR(20),
    role_id INT NOT NULL,
    FOREIGN KEY (role_id)
        REFERENCES roles(id)
        ON DELETE RESTRICT
);

CREATE TABLE commandes (
    id SERIAL PRIMARY KEY,
    date_commande TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    montant_initial DECIMAL(10,2) NOT NULL
        CHECK (montant_initial >= 0),
    avance DECIMAL(10,2) NOT NULL DEFAULT 0.00
        CHECK (avance >= 0 AND avance <= montant_initial),
    client_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    FOREIGN KEY (client_id)
        REFERENCES clients(id)
        ON DELETE RESTRICT,
    FOREIGN KEY (utilisateur_id)
        REFERENCES utilisateurs(id)
        ON DELETE RESTRICT
);

CREATE TABLE appros (
    id SERIAL PRIMARY KEY,
    ref_bl VARCHAR(50) NOT NULL UNIQUE,
    date_appro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    fournisseur_id INT NOT NULL,
    statut_appro_id INT NOT NULL,
    utilisateur_id INT NOT NULL,
    FOREIGN KEY (fournisseur_id)
        REFERENCES fournisseurs(id)
        ON DELETE RESTRICT,
    FOREIGN KEY (statut_appro_id)
        REFERENCES statuts_appro(id)
        ON DELETE RESTRICT,
    FOREIGN KEY (utilisateur_id)
        REFERENCES utilisateurs(id)
        ON DELETE RESTRICT
);

CREATE TABLE dettes (
    id SERIAL PRIMARY KEY,
    montant_initial DECIMAL(10,2) NOT NULL CHECK (montant_initial >= 0),
    montant_paye DECIMAL(10,2) NOT NULL DEFAULT 0.00 CHECK (montant_paye >= 0 AND montant_paye <= montant_initial),
    reste_du DECIMAL(10,2) NOT NULL CHECK (reste_du >= 0),
    statut VARCHAR(30) NOT NULL DEFAULT 'NON_SOLDEE' CHECK (statut IN ('NON_SOLDEE', 'SOLDEE')),
    date_creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    commande_id INT UNIQUE,
    client_id INT NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE RESTRICT,
    FOREIGN KEY (client_id)  REFERENCES clients(id) ON DELETE RESTRICT
);

CREATE TABLE lignes_commande (
    id SERIAL PRIMARY KEY,
    qte_commande INT NOT NULL CHECK (qte_commande > 0),
    prix_reel DECIMAL(10,2) NOT NULL CHECK (prix_reel >= 0),
    commande_id INT NOT NULL,
    produit_id INT NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE RESTRICT,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE RESTRICT
);

CREATE TABLE lignes_appro (
    id SERIAL PRIMARY KEY,
    qte_appro INT NOT NULL CHECK (qte_appro > 0),
    qte_recu INT NOT NULL DEFAULT 0 CHECK (qte_recu >= 0 AND qte_recu <= qte_appro),
    prix_reel DECIMAL(10,2) NOT NULL CHECK (prix_reel >= 0),
    appro_id INT NOT NULL,
    produit_id INT NOT NULL,
    FOREIGN KEY (appro_id) REFERENCES appros(id) ON DELETE RESTRICT,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE RESTRICT
);

CREATE TABLE reglements (
    id SERIAL PRIMARY KEY,
    date_reglement TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    montant DECIMAL(10,2) NOT NULL CHECK (montant > 0),
    dette_id INT NOT NULL,
    mode_paiement_id INT NOT NULL,
    FOREIGN KEY (dette_id) REFERENCES dettes(id) ON DELETE RESTRICT,
    FOREIGN KEY (mode_paiement_id) REFERENCES modes_paiement(id) ON DELETE RESTRICT
);