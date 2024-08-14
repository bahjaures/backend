<?php
session_start();
include 'includes/db.php';

if ($_SESSION['role'] != 'artisan') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Vérifier que l'utilisateur existe dans la table Artisans
$sql_check = "SELECT id_Artisans FROM Artisans WHERE id_utilisateur = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $user_id);
$stmt_check->execute();
$stmt_check->bind_result($id_artisan);
$stmt_check->fetch();
$stmt_check->close();

if (!$id_artisan) {
    echo "Erreur : Utilisateur non trouvé dans les artisans.";
    exit();
}

$service_id = $_GET['id'];

// Supprimer le service
$sql = "DELETE FROM Services WHERE id_Services = ? AND id_artisans = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $service_id, $id_artisan);

if ($stmt->execute()) {
    echo "Service supprimé avec succès.";
} else {
    echo "Erreur: " . $stmt->error;
}

$stmt->close();
header("Location: manage_services.php");
exit();
