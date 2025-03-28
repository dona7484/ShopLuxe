<?php
session_start();  // Démarrer la session pour pouvoir utiliser les variables de session
ini_set('display_errors', 1);
error_reporting(E_ALL);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Panier - ShopLuxe</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h1>Votre Panier</h1>

<?php
// Vérifier si le panier existe
if (!isset($_SESSION['panier'])) {
    $_SESSION['panier'] = [];
}

// Calcul du total
$total = 0;
foreach ($_SESSION['panier'] as $id => $produit) {
    $total += $produit['prix'] * $produit['quantite'];
}

// Mise à jour de la quantité du produit si le formulaire est soumis
if (isset($_POST['update'])) {
    $id_produit = $_POST['id_produit'];
    $quantite = (int)$_POST['quantite'];

    // S'assurer que la quantité est valide (par exemple, entre 1 et 10)
    if ($quantite > 0 && $quantite <= 10) {
        $_SESSION['panier'][$id_produit]['quantite'] = $quantite;
    }
}

// Si le panier est vide
if (count($_SESSION['panier']) == 0) {
    echo "<p>Votre panier est vide.</p>";
} else {
    // Affichage du panier
    echo "<table>";
    echo "<thead><tr><th>Produit</th><th>Prix</th><th>Quantité</th><th>Total</th></tr></thead><tbody>";

    foreach ($_SESSION['panier'] as $id => $produit) {
        echo "<tr>";
        echo "<td>" . $produit['nom'] . "</td>";
        echo "<td>" . $produit['prix'] . " €</td>";
        echo "<td>
                <form action='panier.php' method='POST'>
                    <input type='number' name='quantite' value='" . $produit['quantite'] . "' min='1' max='10'>
                    <input type='hidden' name='id_produit' value='" . $id . "'>
                    <input type='submit' name='update' value='Mettre à jour'>
                </form>
              </td>";
        echo "<td>" . $produit['prix'] * $produit['quantite'] . " €</td>";
        echo "</tr>";
    }

    echo "</tbody></table>";

    // Affichage du total
    echo "<p><strong>Total : " . $total . " €</strong></p>";

    // Bouton pour passer à la commande
    echo "<a href='commande.php'>Passer à la commande</a>";
}
?>

<!-- Lien pour revenir à la boutique -->
<a href="index.php">Retour à la boutique</a>

</body>
</html>
