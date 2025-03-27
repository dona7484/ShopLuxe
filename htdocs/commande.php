<?php
session_start();  // Démarrer la session pour pouvoir utiliser les variables de session
ini_set('display_errors', 1);
error_reporting(E_ALL);  // Activer l'affichage des erreurs PHP

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: login.php');  // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
    exit;
}

// Vérifier si le panier existe
if (!isset($_SESSION['panier']) || count($_SESSION['panier']) == 0) {
    echo "<p>Votre panier est vide. Veuillez ajouter des produits avant de passer la commande.</p>";
    echo "<p><a href='index.php'><button class='btn'>Retour à la boutique</button></a></p>";
    exit;
}

// Sauvegarder les détails du panier avant de le vider
$panier = $_SESSION['panier'];

// Calcul du total de la commande
$total = 0;
foreach ($_SESSION['panier'] as $id => $produit) {
    $total += $produit['prix'] * $produit['quantite'];
}

// Vérification si le formulaire de commande est soumis
if (isset($_POST['commander'])) {
    // Sauvegarder les informations de la commande (nom, adresse, etc.)
    $nom = $_POST['nom'];
    $adresse = $_POST['adresse'];
    $email = $_POST['email'];

    // Sauvegarder les informations de l'utilisateur dans la session
    $_SESSION['nom'] = $nom;
    $_SESSION['adresse'] = $adresse;
    $_SESSION['email'] = $email;

    // Récupérer l'ID de l'utilisateur connecté
    $id_utilisateur = $_SESSION['id_utilisateur'];  // Utiliser l'ID de l'utilisateur connecté

    // Connexion à la base de données
    include('../sql/includes/db.php');  // Inclure la connexion à la base de données

    // Insérer la commande dans la table Commandes
    $sql = "INSERT INTO Commandes (id_utilisateur, date_commande, total) VALUES (?, NOW(), ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("id", $id_utilisateur, $total);  // Passer l'ID de l'utilisateur et le total de la commande
    $stmt->execute();

    // Récupérer l'ID de la commande
    $orderId = $stmt->insert_id;

    // Insérer les lignes de commande dans la table Lignes_de_commande
    foreach ($_SESSION['panier'] as $id => $produit) {
        $sqlLine = "INSERT INTO Lignes_de_commande (id_commande, id_produit, quantité, prix_unitaire) 
                    VALUES (?, ?, ?, ?)";
        $stmtLine = $conn->prepare($sqlLine);
        $stmtLine->bind_param("iiii", $orderId, $id, $produit['quantite'], $produit['prix']);
        $stmtLine->execute();
    }

    // Marquer la commande comme confirmée
    $_SESSION['commande_confirmée'] = true;

    // Vider le panier après la commande
    unset($_SESSION['panier']);
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passer la commande - ShopLuxe</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h1>Passer la commande</h1>

<?php
// Vérifier si la commande a été confirmée pour ne pas afficher le formulaire à nouveau
if (isset($_SESSION['commande_confirmée']) && $_SESSION['commande_confirmée'] === true) {
    // Affichage du message de confirmation
    echo "<p class='confirmation-msg'>Votre commande a été confirmée avec succès. Merci de votre achat !</p>";
    echo "<p>Nous vous remercions, <strong>" . htmlspecialchars($_SESSION['nom']) . "</strong>. Votre commande est en cours de traitement.</p>";

    // Détails de la commande
    echo "<h2>Récapitulatif de votre commande :</h2>";
    echo "<table class='table'>";
    echo "<thead><tr><th>Produit</th><th>Prix</th><th>Quantité</th><th>Total</th></tr></thead><tbody>";
    foreach ($panier as $id => $produit) {
        echo "<tr>";
        echo "<td>" . $produit['nom'] . "</td>";
        echo "<td>" . $produit['prix'] . " €</td>";
        echo "<td>" . $produit['quantite'] . "</td>";
        echo "<td>" . $produit['prix'] * $produit['quantite'] . " €</td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
    echo "<p><strong>Total : " . $total . " €</strong></p>";
    echo "<p>Adresse de livraison : " . htmlspecialchars($_SESSION['adresse']) . "</p>";

    // Bouton pour revenir à la boutique
    echo "<p><a href='index.php' class='btn'>Retour à la boutique</a></p>";
} else {
    // Formulaire de commande
    echo "<form action='commande.php' method='POST'>";
    echo "<h2>Vérifiez les informations ci-dessous et confirmez votre commande :</h2>";
    echo "<table class='table'>";
    echo "<thead><tr><th>Produit</th><th>Prix</th><th>Quantité</th><th>Total</th></tr></thead><tbody>";
    foreach ($_SESSION['panier'] as $id => $produit) {
        echo "<tr>";
        echo "<td>" . $produit['nom'] . "</td>";
        echo "<td>" . $produit['prix'] . " €</td>";
        echo "<td>" . $produit['quantite'] . "</td>";
        echo "<td>" . $produit['prix'] * $produit['quantite'] . " €</td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
    echo "<p><strong>Total : " . $total . " €</strong></p>";

    // Affichage du formulaire de livraison
    echo "<h2>Informations de livraison :</h2>";
    echo "<form action='commande.php' method='POST' style='background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif;'>";

    // Nom
    echo "<label for='nom' style='display: block; font-size: 1.1em; margin-bottom: 8px; color: #333; margin-left: 10px;'>Nom :</label>";
    echo "<input type='text' id='nom' name='nom' required placeholder='Entrez votre nom' style='width: 100%; padding: 14px; border: 1px solid #ccc; border-radius: 6px; font-size: 1em; margin-bottom: 15px; box-sizing: border-box; transition: border 0.3s ease;'>";

    // Adresse
    echo "<label for='adresse' style='display: block; font-size: 1.1em; margin-bottom: 8px; color: #333; margin-left: 10px;'>Adresse :</label>";
    echo "<textarea id='adresse' name='adresse' rows='4' required placeholder='Entrez votre adresse' style='width: 100%; padding: 14px; border: 1px solid #ccc; border-radius: 6px; font-size: 1em; margin-bottom: 15px; box-sizing: border-box; transition: border 0.3s ease;'></textarea>";

    // Email
    echo "<label for='email' style='display: block; font-size: 1.1em; margin-bottom: 8px; color: #333; margin-left: 10px;'>Email :</label>";
    echo "<input type='email' id='email' name='email' required placeholder='Entrez votre email' style='width: 100%; padding: 14px; border: 1px solid #ccc; border-radius: 6px; font-size: 1em; margin-bottom: 15px; box-sizing: border-box; transition: border 0.3s ease;'>";

    // Bouton de soumission
    echo "<input type='submit' name='commander' value='Confirmer la commande' class='btn' style='width: 100%; padding: 14px; background-color: #007bff; color: #fff; font-size: 1.2em; border: none; border-radius: 6px; cursor: pointer; transition: background-color 0.3s ease;'>";

    echo "</form>";
}
?>

</body>
</html>
