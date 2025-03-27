<?php
// Vérifier si le formulaire est soumis
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (isset($_POST['register'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Connexion à la base de données
    include('../sql/includes/db.php');

    // Vérifier si l'email existe déjà
    $sql = "SELECT * FROM Utilisateurs WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<p>L'email est déjà utilisé.</p>";
    } else {
        // Si l'email n'existe pas, on insère l'utilisateur dans la base de données
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);  // Hasher le mot de passe

        $sql = "INSERT INTO Utilisateurs (email, mot_de_passe) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $hashed_password);
        $stmt->execute();

        echo "<p>Inscription réussie. Vous pouvez maintenant vous connecter.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - ShopLuxe</title>
</head>
<body>

<h1>Créer un compte</h1>

<form action="register.php" method="POST">
    <label for="email">Email :</label>
    <input type="email" name="email" required><br><br>

    <label for="password">Mot de passe :</label>
    <input type="password" name="password" required><br><br>

    <input type="submit" name="register" value="S'inscrire">
</form>

<a href="login.php">Déjà un compte ? Connectez-vous ici</a>

</body>
</html>
