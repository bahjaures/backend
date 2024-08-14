<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT Commandes.*, Articles.nom, Articles.prix 
        FROM Commandes
        INNER JOIN Articles ON Commandes.id_Articles = Articles.id_Articles 
        WHERE Commandes.id_utilisateur = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Mes Commandes</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <h2>Mes Commandes</h2>
    <?php if ($result->num_rows > 0) : ?>
        <ul>
            <?php while ($order = $result->fetch_assoc()) : ?>
                <li>
                    <p>Article: <?php echo htmlspecialchars($order['nom']); ?></p>
                    <p>Prix: <?php echo htmlspecialchars($order['prix']); ?>€</p>
                    <p>Quantité: <?php echo htmlspecialchars($order['quantite']); ?></p>
                    <p>Date: <?php echo htmlspecialchars($order['date_commande']); ?></p>
                    <p>Statut: <?php echo htmlspecialchars($order['statut']); ?></p>
                    <?php if ($order['statut'] == 'en attente') : ?>
                        <form method="post" action="cancel_order.php">
                            <input type="hidden" name="order_id" value="<?php echo $order['id_Commandes']; ?>">
                            <button type="submit">Annuler</button>
                        </form>
                    <?php endif; ?>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else : ?>
        <p>Aucune commande trouvée.</p>
    <?php endif; ?>

    <footer>
        <p>&copy; 2024 Artisanat. Tous droits réservés.</p>
    </footer>
</body>

</html>