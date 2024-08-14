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

// Requête SQL pour récupérer les commandes des articles de l'artisan
$sql = "SELECT Commandes.*, Articles.nom AS article_nom, Articles.prix, Clients.nom AS client_nom 
        FROM Commandes
        INNER JOIN Articles ON Commandes.id_Articles = Articles.id_Articles
        INNER JOIN Clients ON Commandes.id_client = Clients.id_Clients
        WHERE Articles.id_artisans = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_artisan);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Commandes des Articles</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="order_article.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Commandes des Articles</h2>
        <?php if ($result->num_rows > 0) : ?>
            <div class="row">
                <?php while ($order = $result->fetch_assoc()) : ?>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-box"></i> <?php echo htmlspecialchars($order['article_nom']); ?></h5>
                                <p class="card-text"><i class="fas fa-tag"></i> <strong>Prix:</strong> <?php echo htmlspecialchars($order['prix']); ?> cfa</p>
                                <p class="card-text"><i class="fas fa-user"></i> <strong>Client:</strong> <?php echo htmlspecialchars($order['client_nom']); ?></p>
                                <p class="card-text"><i class="fas fa-boxes"></i> <strong>Quantité:</strong> <?php echo htmlspecialchars($order['quantite']); ?></p>
                                <p class="card-text"><i class="fas fa-calendar-alt"></i> <strong>Date:</strong> <?php echo htmlspecialchars($order['date_commande']); ?></p>
                                <p class="card-text"><i class="fas fa-info-circle"></i> <strong>Statut:</strong> <span class="badge bg-info"><?php echo htmlspecialchars($order['statut']); ?></span></p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <p class="text-center">Aucune commande trouvée.</p>
        <?php endif; ?>

        <footer class="mt-5 text-center">
            <p>&copy; 2024 Artisanat. Tous droits réservés.</p>
        </footer>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>
</body>

</html>