<?php
session_start();  // Démarrer la session pour utiliser les variables de session
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inclure le fichier db.php avec le bon chemin relatif
include('../sql/includes/db.php');

// Connexion à la base de données
$sql = "SELECT id_produit, nom, prix FROM Produits";
$result = $conn->query($sql);


  // Vérifier si l'utilisateur est connecté avant d'afficher le bouton de déconnexion
        if (isset($_SESSION['id_utilisateur'])) {
            echo '<a href="logout.php">Déconnexion</a>';
        } else {
            echo '<a href="login.php">Se connecter</a>';
        }
        ?>
        
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopLuxe - Produits de Luxe</title>
    <link rel="stylesheet" href="css/style.css">  <!-- Inclusion du fichier CSS -->
</head>
<body>

    <h1>Nos produits de luxe</h1>

    <h2>Découvrez notre collection exclusive</h2>
    
    <ul>
        <?php
        // Affichage des produits
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<li>";
                echo "<h3>" . $row["nom"] . "</h3>";  // Nom du produit
                echo "<p class='prix'>" . $row["prix"] . " €</p>";  // Prix du produit
                
                // Formulaire d'ajout au panier
                echo "<form action='index.php' method='POST' style='display:inline;'>
                        <input type='hidden' name='id_produit' value='" . htmlspecialchars($row["id_produit"]) . "'>
                        <input type='submit' name='ajouter_panier' value='Ajouter au panier'>
                      </form>";
                echo "</li>";
            }
            echo "</ul>";
        } else {
            echo "Aucun produit disponible";
        }

        // Ajouter un produit au panier
        if (isset($_POST['ajouter_panier'])) {
            $id_produit = $_POST['id_produit'];
            
            // Sécuriser l'ID du produit
            $id_produit = intval($id_produit);
            
            // Récupérer les informations du produit
            $sql = "SELECT nom, prix FROM Produits WHERE id_produit = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $id_produit);
            $stmt->execute();
            $result = $stmt->get_result();
            $produit = $result->fetch_assoc();
            
            if ($produit) {
                // Ajouter le produit au panier ou augmenter la quantité
                if (!isset($_SESSION['panier'][$id_produit])) {
                    $_SESSION['panier'][$id_produit] = [
                        'nom' => $produit['nom'],
                        'prix' => $produit['prix'],
                        'quantite' => 1
                    ];
                } else {
                    $_SESSION['panier'][$id_produit]['quantite'] += 1;  // Augmenter la quantité si le produit existe déjà dans le panier
                }

                echo "<p>Produit ajouté au panier ! <a href='panier.php'>Voir le panier</a></p>";
            } else {
                echo "<p>Produit non trouvé.</p>";
            }
        }

        $conn->close();  // Fermeture de la connexion à la base de données
        ?>
    
    <footer>
        <p>&copy; 2025 ShopLuxe. Tous droits réservés.</p>
        <p><a href="#">Contactez-nous</a></p>
    </footer>

</body>
</html>
