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

$article_id = $_GET['id'];
$sql = "SELECT * FROM Articles WHERE id_Articles = ? AND id_artisans = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $article_id, $id_artisan);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Article non trouvé.";
    exit();
}

$article = $result->fetch_assoc();

if (isset($_POST['update'])) {
    $nom = $_POST['nom'];
    $prix = $_POST['prix'];
    $contact = $_POST['contact'];

    $sql_update = "UPDATE Articles SET nom = ?, prix = ?, contact = ? WHERE id_Articles = ? AND id_artisans = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sdsii", $nom, $prix, $contact, $article_id, $id_artisan);

    if ($stmt_update->execute()) {
        echo "Article mis à jour avec succès.";
    } else {
        echo "Erreur: " . $stmt_update->error;
    }

    $stmt_update->close();
    header("Location: manage_articles.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier Article</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <h2>Modifier Article</h2>
    <form action="edit_article.php?id=<?php echo $article_id; ?>" method="post">
        <label for="nom">Nom:</label>
        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($article['nom']); ?>" required><br>

        <label for="prix">Prix:</label>
        <input type="text" id="prix" name="prix" value="<?php echo htmlspecialchars($article['prix']); ?>" required><br>

        <label for="contact">Contact:</label>
        <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($article['contact']); ?>" required><br>

        <button type="submit" name="update">Mettre à jour</button>
    </form>
    <p><a href="manage_articles.php">Retour à la gestion des articles</a></p>
</body>

</html>