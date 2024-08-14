<?php
session_start();
include 'includes/db.php';

if ($_SESSION['role'] != 'artisan') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les abonnements disponibles
$sql = "SELECT * FROM Abonnements";
$abonnements_result = $conn->query($sql);

// Gérer la soumission du formulaire pour s'abonner à un abonnement
if (isset($_POST['subscribe'])) {
    $id_abonnement = $_POST['id_abonnement'];
    $date_debut = date('Y-m-d');
    $date_fin = date('Y-m-d', strtotime("+$_POST[duree] days"));

    $sql = "INSERT INTO User_Abonnements (id_utilisateur, id_abonnement, date_debut, date_fin) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $user_id, $id_abonnement, $date_debut, $date_fin);

    if ($stmt->execute()) {
        echo "Abonnement réussi.";
    } else {
        echo "Erreur : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Abonnements et Packs Publicitaires</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <h2>Abonnements Disponibles</h2>
    <nav>
        <ul>
            <li><a href="abonnements.php">Retour à la Gestion des a bonnements</a></li>
        </ul>
    </nav>
    <form action="subscribe_abonnement.php" method="POST">
        <label for="id_abonnement">Sélectionnez un abonnement:</label>
        <select id="id_abonnement" name="id_abonnement" required>
            <?php while ($abonnement = $abonnements_result->fetch_assoc()) : ?>
                <option value="<?php echo $abonnement['id_Abonnements']; ?>" data-duree="<?php echo $abonnement['duree']; ?>">
                    <?php echo htmlspecialchars($abonnement['nom']); ?> - <?php echo htmlspecialchars($abonnement['duree']); ?> jours - <?php echo htmlspecialchars($abonnement['prix']); ?> cfa
                </option>
            <?php endwhile; ?>
        </select><br>
        <input type="hidden" id="duree" name="duree" value="">
        <button type="submit" name="subscribe">S'abonner</button>
    </form>

    <script>
        document.getElementById('id_abonnement').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var duree = selectedOption.getAttribute('data-duree');
            document.getElementById('duree').value = duree;
        });
    </script>

    <footer>
        <p>&copy; 2024 Artisanat. Tous droits réservés.</p>
    </footer>
</body>

</html>