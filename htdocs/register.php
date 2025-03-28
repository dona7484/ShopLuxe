<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['register'])) {
    // Récupérer les données du formulaire
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $nom      = trim($_POST['nom']);
    $adresse  = trim($_POST['adresse']);

    // Inclure la connexion via PDO
    require_once('../dataAccess.php');
    $dataAccess = new DataAccess();
    $pdo = $dataAccess->getPDO();

    // Vérifier si l'email existe déjà
    $sql = "SELECT * FROM Utilisateurs WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);

    if ($stmt->rowCount() > 0) {
        echo "<p>L'email est déjà utilisé.</p>";
    } else {
        // Hacher le mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insérer l'utilisateur dans la base de données avec nom et adresse
        $sql = "INSERT INTO Utilisateurs (nom, email, mot_de_passe, adresse) VALUES (:nom, :email, :mot_de_passe, :adresse)";
        $stmt = $pdo->prepare($sql);
        $success = $stmt->execute([
            'nom'        => $nom,
            'email'      => $email,
            'mot_de_passe' => $hashed_password,
            'adresse'    => $adresse
        ]);

        if ($success) {
            echo "<p>Inscription réussie. Vous pouvez maintenant vous connecter.</p>";
        } else {
            echo "<p>Erreur lors de l'inscription.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - ShopLuxe</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h1>Créer un compte</h1>
<form action="register.php" method="POST">
    <label for="nom">Nom :</label>
    <input type="text" name="nom" required placeholder="Entrez votre nom"><br><br>

    <label for="adresse">Adresse :</label>
    <input type="text" name="adresse" required placeholder="Entrez votre adresse"><br><br>

    <label for="email">Email :</label>
    <input type="email" name="email" required placeholder="Entrez votre email"><br><br>

    <label for="password">Mot de passe :</label>
    <input type="password" name="password" required placeholder="Entrez votre mot de passe"><br><br>

    <input type="submit" name="register" value="S'inscrire">
</form>

<a href="login.php">Déjà un compte ? Connectez-vous ici</a>
</body>
</html>
