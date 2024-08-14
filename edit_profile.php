<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur
$sql = "SELECT * FROM Utilisateurs WHERE id_Utilisateurs = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $status = $_POST['status'];

    // Mise à jour des informations de l'utilisateur
    $sql = "UPDATE Utilisateurs SET nom = ?, prenom = ?, email = ?, status = ? WHERE id_Utilisateurs = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssii", $nom, $prenom, $email, $status, $user_id);

    if ($stmt->execute()) {
        echo "Informations mises à jour avec succès.";
        header("Location: profile.php");
        exit();
    } else {
        echo "Erreur: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier Profil</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <h2>Modifier Profil</h2>
    <form action="edit_profile.php" method="post">
        <label for="nom">Nom:</label>
        <input type="text" id="nom" name="nom" value="<?php echo $user['nom']; ?>" required><br>

        <label for="prenom">Prénom:</label>
        <input type="text" id="prenom" name="prenom" value="<?php echo $user['prenom']; ?>" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required><br>

        <label for="status">Statut:</label>
        <select id="status" name="status">
            <option value="actif" <?php if ($user['status'] == 'actif') echo 'selected'; ?>>Actif</option>
            <option value="inactif" <?php if ($user['status'] == 'inactif') echo 'selected'; ?>>Inactif</option>
        </select><br>

        <button type="submit">Mettre à jour</button>
    </form>
    <p><a href="profile.php">Retour au profil</a></p>
</body>

</html>