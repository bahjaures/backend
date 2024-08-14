<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Recherche par spécialité
    $specialite = $_GET['specialite'] ?? '';

// Récupérer les artisans en fonction de la spécialité
$sql = "SELECT Artisans.*, Services.description, Services.prix, Services.disponibilite 
        FROM Artisans 
        INNER JOIN Services ON Artisans.id_Artisans = Services.id_artisans 
        WHERE Artisans.specialite LIKE ?";
$stmt = $conn->prepare($sql);
$specialite = '%' . $specialite . '%';
$stmt->bind_param("s", $specialite);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Services des Artisans</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <h2>Services des Artisans</h2>
    
    <?php if ($result->num_rows > 0) : ?>
        <ul>
            <?php while ($artisan = $result->fetch_assoc()) : ?>
                <li>
                    <p>Nom: <?php echo htmlspecialchars($artisan['nom']); ?></p>
                    <p>Spécialité: <?php echo htmlspecialchars($artisan['specialite']); ?></p>
                    <p>Service: <?php echo htmlspecialchars($artisan['description']); ?></p>
                    <p>prix: <?php echo htmlspecialchars($artisan['prix']); ?>cfa</p>
                    <p>Disponibilité: <?php echo htmlspecialchars($artisan['disponibilite']); ?></p>
                    <p>Quartier: <?php echo htmlspecialchars($artisan['quartier']); ?></p>
                    <p>Contact: <?php echo htmlspecialchars($artisan['contact']); ?></p>
                    <p><a href="send_message.php?to=<?php echo $artisan['id_utilisateur']; ?>">Envoyer un message</a></p>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else : ?>
        <p>Aucun artisan trouvé.</p>
    <?php endif; ?>

    <p><a href="index.php">Retour à l'accueil</a></p>
</body>

</html>