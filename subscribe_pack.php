<?php
session_start();
include 'includes/db.php';

if ($_SESSION['role'] != 'artisan') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les packs publicitaires disponibles
$sql = "SELECT * FROM Packspublicitaires";
$packs_result = $conn->query($sql);

// Gérer la soumission du formulaire pour s'abonner à un pack publicitaire
if (isset($_POST['subscribe'])) {
    $id_pack = $_POST['id_pack'];
    $duree = $_POST['duree'];
    $date_debut = date('Y-m-d');
    $date_fin = date('Y-m-d', strtotime("+$duree days"));

    $sql = "INSERT INTO User_Packs (id_utilisateur, id_pack, date_debut, date_fin) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $user_id, $id_pack, $date_debut, $date_fin);

    if ($stmt->execute()) {
        echo "Abonnement au pack publicitaire réussi.";
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
    <title>Packs Publicitaires</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <h2>Packs Publicitaires Disponibles</h2>
    <nav>
            <ul>
                <li><a href="abonnements.php">Retour à la Gestion des a bonnements</a></li>
            </ul>
        </nav>
        <form action="subscribe_pack.php" method="POST">
            <label for="id_packs">Sélectionnez un pack publicitaire:</label>
            <select id="id_packs" name="id_pack" required>
                <?php while ($pack = $packs_result->fetch_assoc()) : ?>
                    <option value="<?php echo $pack['id_PacksPublicitaires']; ?>" data-duree="<?php echo $pack['duree']; ?>">
                        <?php echo htmlspecialchars($pack['nom']); ?> - <?php echo htmlspecialchars($pack['duree']); ?> jours - <?php echo htmlspecialchars($pack['prix']); ?> cfa
                    </option>
                <?php endwhile; ?>
            </select><br>
            <input type="hidden" id="duree" name="duree" value="">
            <button type="submit" name="subscribe">S'abonner</button>
        </form>

        <script>
            document.getElementById('id_PacksPublicitaires').addEventListener('change', function() {
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