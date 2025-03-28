<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    header('Location: login.php');
    exit;
}

// Vérifier si le panier existe
if (!isset($_SESSION['panier']) || count($_SESSION['panier']) == 0) {
    echo "<p>Votre panier est vide. Veuillez ajouter des produits avant de passer la commande.</p>";
    echo "<p><a href='index.php'><button class='btn'>Retour à la boutique</button></a></p>";
    exit;
}

// Sauvegarder le panier avant de le vider
$panier = $_SESSION['panier'];

// Calcul du total
$total = 0;
foreach ($_SESSION['panier'] as $id => $produit) {
    $total += $produit['prix'] * $produit['quantite'];
}

// Traitement du formulaire de commande
if (isset($_POST['commander'])) {
    $nom = $_POST['nom'];
    $adresse = $_POST['adresse'];
    $email = $_POST['email'];

    $_SESSION['nom'] = $nom;
    $_SESSION['adresse'] = $adresse;
    $_SESSION['email'] = $email;
    $id_utilisateur = $_SESSION['id_utilisateur'];

    require_once('../dataAccess.php');
    $dataAccess = new DataAccess();
    $pdo = $dataAccess->getPDO();

    // Créer la commande avec PDO
    $orderId = $dataAccess->createCommande($id_utilisateur, $total);

    if ($orderId) {
        // Insérer les lignes de commande
        foreach ($_SESSION['panier'] as $id => $produit) {
            $stmt = $pdo->prepare("INSERT INTO Lignes_de_commande (id_commande, id_produit, quantité, prix_unitaire) VALUES (:orderId, :id_produit, :quantite, :prix)");
            $stmt->execute([
                'orderId'    => $orderId,
                'id_produit' => $id,
                'quantite'   => $produit['quantite'],
                'prix'       => $produit['prix']
            ]);
        }
        $_SESSION['commande_confirmée'] = true;
        unset($_SESSION['panier']);
    } else {
        echo "<p>Erreur lors de la création de la commande.</p>";
    }
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
if (isset($_SESSION['commande_confirmée']) && $_SESSION['commande_confirmée'] === true) {
    echo "<p class='confirmation-msg'>Votre commande a été confirmée avec succès. Merci de votre achat !</p>";
    echo "<p>Nous vous remercions, <strong>" . htmlspecialchars($_SESSION['nom']) . "</strong>. Votre commande est en cours de traitement.</p>";
    echo "<h2>Récapitulatif de votre commande :</h2>";
    echo "<table class='table'>";
    echo "<thead><tr><th>Produit</th><th>Prix</th><th>Quantité</th><th>Total</th></tr></thead><tbody>";
    foreach ($panier as $id => $produit) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($produit['nom']) . "</td>";
        echo "<td>" . htmlspecialchars($produit['prix']) . " €</td>";
        echo "<td>" . htmlspecialchars($produit['quantite']) . "</td>";
        echo "<td>" . htmlspecialchars($produit['prix'] * $produit['quantite']) . " €</td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
    echo "<p><strong>Total : " . htmlspecialchars($total) . " €</strong></p>";
    echo "<p>Adresse de livraison : " . htmlspecialchars($_SESSION['adresse']) . "</p>";
    echo "<p><a href='index.php' class='btn'>Retour à la boutique</a></p>";
} else {
    ?>
    <form action="commande.php" method="POST">
        <h2>Vérifiez les informations ci-dessous et confirmez votre commande :</h2>
        <table class="table">
            <thead>
                <tr><th>Produit</th><th>Prix</th><th>Quantité</th><th>Total</th></tr>
            </thead>
            <tbody>
                <?php
                foreach ($panier as $id => $produit) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($produit['nom']) . "</td>";
                    echo "<td>" . htmlspecialchars($produit['prix']) . " €</td>";
                    echo "<td>" . htmlspecialchars($produit['quantite']) . "</td>";
                    echo "<td>" . htmlspecialchars($produit['prix'] * $produit['quantite']) . " €</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
        <p><strong>Total : <?php echo htmlspecialchars($total); ?> €</strong></p>
        
        <h2>Informations de livraison :</h2>
        <div style="background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif;">
            <label for="nom" style="display: block; font-size: 1.1em; margin-bottom: 8px; color: #333;">Nom :</label>
            <input type="text" id="nom" name="nom" required placeholder="Entrez votre nom" style="width: 100%; padding: 14px; border: 1px solid #ccc; border-radius: 6px; margin-bottom: 15px;">
            
            <label for="adresse" style="display: block; font-size: 1.1em; margin-bottom: 8px; color: #333;">Adresse :</label>
            <textarea id="adresse" name="adresse" rows="4" required placeholder="Entrez votre adresse" style="width: 100%; padding: 14px; border: 1px solid #ccc; border-radius: 6px; margin-bottom: 15px;"></textarea>
            
            <label for="email" style="display: block; font-size: 1.1em; margin-bottom: 8px; color: #333;">Email :</label>
            <input type="email" id="email" name="email" required placeholder="Entrez votre email" style="width: 100%; padding: 14px; border: 1px solid #ccc; border-radius: 6px; margin-bottom: 15px;">
            
            <input type="submit" name="commander" value="Confirmer la commande" class="btn" style="width: 100%; padding: 14px; background-color: #007bff; color: #fff; font-size: 1.2em; border: none; border-radius: 6px;">
        </div>
    </form>
    <?php
}
?>
</body>
</html>
