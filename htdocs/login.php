<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Inclure la connexion via PDO
    require_once('../dataAccess.php');
    $dataAccess = new DataAccess();
    $pdo = $dataAccess->getPDO();

    // Préparer et exécuter la requête avec PDO
    $sql = "SELECT id_utilisateur, mot_de_passe, nom FROM Utilisateurs WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        if (password_verify($password, $user['mot_de_passe'])) {
            $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
            $_SESSION['nom_utilisateur'] = $user['nom'];
            header('Location: index.php');
            exit;
        } else {
            echo "<p>Mot de passe incorrect.</p>";
        }
    } else {
        echo "<p>Email non trouvé.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - ShopLuxe</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h1>Connexion</h1>
<form action="login.php" method="POST" class="form-connexion">
    <label for="email">Email :</label>
    <input type="email" name="email" required><br><br>
    <label for="password">Mot de passe :</label>
    <input type="password" name="password" required><br><br>
    <input type="submit" name="login" value="Se connecter">
</form>
<a href="register.php">Créer un compte</a>
</body>
</html>
