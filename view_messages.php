<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les messages envoyés à l'utilisateur
$sql = "SELECT messages.*, Utilisateurs.nom AS sender_name 
        FROM Messages messages 
        JOIN Utilisateurs ON messages.sender_id = id_utilisateurs
        WHERE messages.receiver_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Messages Reçus</title>
    <link rel="stylesheet" href="view_message.css">
</head>

<body>
    <header>
        <h2>Messages Reçus</h2>
    </header>
    <div class="container">
        <?php if ($result->num_rows > 0) : ?>
            <ul>
                <?php while ($message = $result->fetch_assoc()) : ?>
                    <li>
                        <p><strong>De:</strong> <?php echo htmlspecialchars($message['sender_name']); ?></p>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars($message['timestamp']); ?></p>
                        <p><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                        <p><a href="send_message.php?to=<?php echo $message['sender_id']; ?>" class="btn btn-primary">Répondre</a></p>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else : ?>
            <p>Aucun message reçu.</p>
        <?php endif; ?>


    </div>
    <footer>
        <p>&copy; 2024 Votre Site. Tous droits réservés.</p>
    </footer>
</body>

</html>

<?php
$conn->close();
?>