<?php
// Paramètres de connexion à la base de données
$servername = "localhost";  // Le serveur de base de données (ici en local avec WAMP)
$username = "root";         // Utilisateur par défaut dans WAMP (root)
$password = "";             // Mot de passe par défaut est vide dans WAMP
$dbname = "ShopLuxe";       // Nom de la base de données à utiliser (assure-toi que "ShopLuxe" existe)

// Création de la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérification de la connexion
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);  // Afficher une erreur si la connexion échoue
}
?>
