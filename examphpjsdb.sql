-- Création de la base de données
CREATE DATABASE IF NOT EXISTS bdd_projet_web;

-- Utiliser la base de données
USE bdd_projet_web;

-- Suppression de la table si elle existe déjà
DROP TABLE IF EXISTS t_utilisateur_uti;

-- Création de la table utilisateur
CREATE TABLE t_utilisateur_uti (
    uti_id INT AUTO_INCREMENT PRIMARY KEY,
    uti_pseudo VARCHAR(255) NOT NULL UNIQUE,
    uti_email VARCHAR(255) NOT NULL UNIQUE,
    uti_motdepasse VARCHAR(255) NOT NULL,
    uti_compte_active BOOLEAN DEFAULT 1,
    uti_code_activation CHAR(5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;