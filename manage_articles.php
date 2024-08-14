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

// Récupérer les articles de l'artisan
$sql = "SELECT * FROM Articles WHERE id_artisans = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_artisan);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gestion des Articles</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="manage_article.css">
</head>

<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Gestion des Articles</h2>
        <p class="text-end"><a href="add_article.php" class="btn btn-primary"><i class="fas fa-plus"></i> Ajouter un nouvel article</a></p>
        <?php if ($result->num_rows > 0) : ?>
            <div class="row">
                <?php while ($article = $result->fetch_assoc()) : ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm">
                            <?php if (!empty($article['photo'])) : ?>
                                <img src="<?php echo htmlspecialchars($article['photo']); ?>" class="card-img-top" alt="Photo de l'article">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-box"></i> <?php echo htmlspecialchars($article['nom']); ?></h5>
                                <p class="card-text"><i class="fas fa-tag"></i> <strong>Prix:</strong> <?php echo htmlspecialchars($article['prix']); ?> cfa</p>
                                <p class="card-text"><i class="fas fa-phone"></i> <strong>Contact:</strong> <?php echo htmlspecialchars($article['contact']); ?></p>
                                <div class="d-flex justify-content-between">
                                    <a href="edit_article.php?id=<?php echo $article['id_Articles']; ?>" class="btn btn-warning"><i class="fas fa-edit"></i> Modifier</a>
                                    <a href="delete_article.php?id=<?php echo $article['id_Articles']; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article?');"><i class="fas fa-trash-alt"></i> Supprimer</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <p class="text-center">Aucun article trouvé.</p>
        <?php endif; ?>
        <p class="text-center mt-4"><a href="artisan_profile.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour au profil artisan</a></p>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>
</body>

</html>