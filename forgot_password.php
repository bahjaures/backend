<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $contact = $_POST['contact'];

    $sql = "SELECT * FROM utilisateurs WHERE nom = ? AND contact = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nom, $contact);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Générer un token unique pour la réinitialisation du mot de passe
        $token = bin2hex(random_bytes(50));
        $user_id = $user['id_Utilisateurs'];

        // Enregistrer le token dans la base de données avec une expiration
        $sql_token = "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))";
        $stmt_token = $conn->prepare($sql_token);
        $stmt_token->bind_param("is", $user_id, $token);
        $stmt_token->execute();
        $stmt_token->close();

        // Envoyer le token par email ou SMS (ici, nous imprimons pour simplifier)
        echo "Utilisez ce lien pour réinitialiser votre mot de passe : ";
        echo "<a href='reset_password.php?token=$token'>Réinitialiser le mot de passe</a>";
    } else {
        echo "Utilisateur non trouvé ou contact incorrect.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>
    <link rel="stylesheet" href="forgot.css">
    <style>

    </style>
</head>

<body>
    <header>
        <h1>Mot de passe oublié</h1>
    </header>
    <main>
        <form action="forgot_password.php" method="POST">
            <label for="nom">Nom :</label>
            <input type="text" name="nom" id="nom" required>

            <label for="contact">Contact :</label>
            <input type="text" name="contact" id="contact" required>

            <button type="submit">Envoyer</button>
        </form>
    </main>
    <footer>
        <p>&copy; 2024 Artisanat. Tous droits réservés.</p>
    </footer>
</body>

</html>