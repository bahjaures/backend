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

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Profil Utilisateur</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <h2>Profil Utilisateur</h2>
    <p>Nom: <?php echo $user['nom']; ?></p>
    <p>Prénom: <?php echo $user['prenom']; ?></p>
    <p>Email: <?php echo $user['email']; ?></p>
    <p>Rôle: <?php echo ucfirst($user['role']); ?></p>
    <p>Statut: <?php echo ucfirst($user['status']); ?></p>
    <p>Date d'inscription: <?php echo $user['date_inscription']; ?></p>
    <p><a href="edit_profile.php">Modifier les informations</a></p>
    <p><a href="index.php">Retour à l'accueil</a></p>
</body>

</html>