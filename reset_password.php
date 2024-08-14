<?php
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $token = $_POST['token'];
    $nouveau_mot_de_passe = $_POST['nouveau_mot_de_passe'];

    // Vérifier le token
    $sql = "SELECT user_id FROM password_resets WHERE token = ? AND expires_at > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $reset = $result->fetch_assoc();

    if ($reset) {
        $user_id = $reset['user_id'];
        $hash = password_hash($nouveau_mot_de_passe, PASSWORD_BCRYPT);

        // Mettre à jour le mot de passe de l'utilisateur
        $sql_update = "UPDATE utilisateurs SET mot_de_passe = ? WHERE id_Utilisateurs = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("si", $hash, $user_id);

        if ($stmt_update->execute()) {
            // Supprimer le token après utilisation
            $sql_delete = "DELETE FROM password_resets WHERE token = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("s", $token);
            $stmt_delete->execute();

            echo "Mot de passe réinitialisé avec succès.";
        } else {
            echo "Erreur: " . $stmt_update->error;
        }

        $stmt_update->close();
    } else {
        echo "Token invalide ou expiré.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Réinitialiser le mot de passe</title>
    <link rel="stylesheet" href="reset_password.css">
</head>

<body>
    <div class="container">
        <header>
            <h1>Réinitialiser le mot de passe</h1>
        </header>
        <main>
            <form action="reset_password.php" method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">

                <label for="nouveau_mot_de_passe">Nouveau mot de passe :</label>
                <input type="password" name="nouveau_mot_de_passe" id="nouveau_mot_de_passe" required>

                <button type="submit">Réinitialiser le mot de passe</button>
            </form>
        </main>
    </div>
    <footer>
        <p>&copy; 2024 Artisanat. Tous droits réservés.</p>
    </footer>
</body>

</html>