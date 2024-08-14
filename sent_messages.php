<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT Messages.*, Utilisateurs.nom AS receiver_name 
        FROM Messages 
        JOIN Utilisateurs ON Messages.receiver_id = id_Utilisateurs 
        WHERE sender_id = ?
        ORDER BY timestamp DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Messages envoyés</title>
    <link rel="stylesheet" href="sent_message.css">
</head>

<body>
    <header>
        <h2>Messages envoyés</h2>
    </header>
    <div class="container">
        <?php if ($result->num_rows > 0) : ?>
            <ul>
                <?php while ($message = $result->fetch_assoc()) : ?>
                    <li>
                        <p><strong>À:</strong> <?php echo htmlspecialchars($message['receiver_name']); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                        <p><small><?php echo htmlspecialchars($message['timestamp']); ?></small></p>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else : ?>
            <p>Aucun message envoyé.</p>
        <?php endif; ?>
        <p><a href="send_message.php" class="btn btn-primary">Envoyer un message</a></p>
        <p><a href="view_messages.php" class="btn btn-primary">Voir les messages reçus</a></p>
        <p><a href="index.php" class="btn btn-primary">Retour à l'accueil</a></p>
    </div>
    <footer>
        <p>&copy; 2024 Artisanat. Tous droits réservés.</p>
    </footer>
</body>

</html>