-- Création de la base de données ShopLuxe
CREATE DATABASE IF NOT EXISTS ShopLuxe;

USE ShopLuxe;

-- Table Utilisateurs
CREATE TABLE IF NOT EXISTS Utilisateurs (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255),
    email VARCHAR(191) UNIQUE,
    mot_de_passe VARCHAR(255),
    adresse TEXT
);

-- Table Produits
CREATE TABLE IF NOT EXISTS Produits (
    id_produit INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255),
    description TEXT,
    prix DECIMAL(10, 2),
    stock INT
);

-- Table Commandes
CREATE TABLE IF NOT EXISTS Commandes (
    id_commande INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT,
    date_commande DATETIME,
    total DECIMAL(10, 2),
    FOREIGN KEY (id_utilisateur) REFERENCES Utilisateurs(id_utilisateur)
);
