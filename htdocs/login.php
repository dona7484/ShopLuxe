<?php
session_start();  // Démarrer la session pour pouvoir stocker l'ID utilisateur après la connexion
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Vérifier si le formulaire est soumis
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Connexion à la base de données
    include('../sql/includes/db.php');


    // Vérifier l'email et le mot de passe dans la base de données
    $sql = "SELECT id_utilisateur, mot_de_passe FROM Utilisateurs WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Récupérer l'utilisateur
        $user = $result->fetch_assoc();

        // Vérifier le mot de passe
        if (password_verify($password, $user['mot_de_passe'])) {
            // Si la connexion réussie, enregistrer l'ID utilisateur dans la session
            $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
            header('Location: index.php');  // Rediriger vers la page d'accueil après connexion
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
