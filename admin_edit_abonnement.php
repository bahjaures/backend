<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'administrateur') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Gérer la soumission du formulaire pour modifier un abonnement
    if (isset($_POST['edit_abonnement'])) {
        $nom = $_POST['nom'];
        $duree = $_POST['duree'];
        $prix = $_POST['prix'];

        $sql = "UPDATE abonnements SET nom = ?, duree = ?, prix = ? WHERE id_Abonnements = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nom, $duree, $prix, $id);

        if ($stmt->execute()) {
            echo "Abonnement modifié avec succès.";
        } else {
            echo "Erreur : " . $stmt->error;
        }

        $stmt->close();
        header("Location: admin_abonnements.php");
        exit();
    }

    // Récupérer les détails de l'abonnement
    $sql = "SELECT * FROM abonnements WHERE id_Abonnements = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $abonnement = $result->fetch_assoc();

    $stmt->close();
} else {
    header("Location: admin_abonnements.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier l'Abonnement</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <header>
        <h1>Modifier l'Abonnement</h1>
        <nav>
            <ul>
                <li><a href="admin_abonnements.php">Retour à la Gestion des Abonnements</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <form action="admin_edit_abonnement.php?id=<?php echo $id; ?>" method="POST">
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($abonnement['nom']); ?>" required><br>
            <label for="duree">Durée (jours):</label>
            <input type="number" id="duree" name="duree" value="<?php echo htmlspecialchars($abonnement['duree']); ?>" required><br>
            <label for="prix">Tarif:</label>
            <input type="text" id="prix" name="prix" value="<?php echo htmlspecialchars($abonnement['prix']); ?>" required><br>
            <button type="submit" name="edit_abonnement">Modifier Abonnement</button>
        </form>
    </main>
    <footer>
        <p>&copy; 2024 Artisanat. Tous droits réservés.</p>
    </footer>
</body>

</html>
<?php
$conn->close();
?>