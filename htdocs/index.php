<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Inclure la connexion PDO via DataAccess
require_once('../dataAccess.php');
$dataAccess = new DataAccess();

// Récupérer tous les produits via PDO
$produits = $dataAccess->getAllProduits();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShopLuxe - Produits de Luxe</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <h1>Bienvenue sur ShopLuxe</h1>
        <?php if(isset($_SESSION['id_utilisateur'])): ?>
            <p>Bonjour, <?php echo htmlspecialchars($_SESSION['nom_utilisateur'] ?? 'Utilisateur'); ?></p>
            <a href="logout.php">Déconnexion</a>
        <?php else: ?>
            <a href="login.php">Se connecter</a>
            <a href="register.php">Inscription</a>
        <?php endif; ?>
    </header>

    <h1>Nos produits de luxe</h1>
    <h2>Découvrez notre collection exclusive</h2>
    
    <ul>
        <?php
        if ($produits && count($produits) > 0) {
            foreach($produits as $row) {
                echo "<li>";
                echo "<h3>" . htmlspecialchars($row["nom"]) . "</h3>";
                echo "<p class='prix'>" . htmlspecialchars($row["prix"]) . " €</p>";
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

        // Traitement de l'ajout d'un produit au panier
        if (isset($_POST['ajouter_panier'])) {
            $id_produit = intval($_POST['id_produit']);
            $produit = $dataAccess->getProduitById($id_produit);
            
            if ($produit) {
                if (!isset($_SESSION['panier'][$id_produit])) {
                    $_SESSION['panier'][$id_produit] = [
                        'nom' => $produit['nom'],
                        'prix' => $produit['prix'],
                        'quantite' => 1
                    ];
                } else {
                    $_SESSION['panier'][$id_produit]['quantite'] += 1;
                }
                echo "<p>Produit ajouté au panier ! <a href='panier.php'>Voir le panier</a></p>";
            } else {
                echo "<p>Produit non trouvé.</p>";
            }
        }
        ?>
    </ul>
    
    <footer>
        <p>&copy; 2025 ShopLuxe. Tous droits réservés.</p>
        <p><a href="#">Contactez-nous</a></p>
    </footer>
</body>
</html>
