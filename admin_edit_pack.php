<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'administrateur') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Gérer la soumission du formulaire pour modifier un pack publicitaire
    if (isset($_POST['edit_pack'])) {
        $nom = $_POST['nom'];
        $duree = $_POST['duree'];
        $prix = $_POST['prix'];
        $description = $_POST['description'];
        $image = $_POST['image'];

        $sql = "UPDATE packspublicitaires SET nom = ?, duree = ?, prix = ?, description = ?, image = ?, WHERE id_packspublicitaires = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $nom, $duree, $prix, $description, $image, $id);

        if ($stmt->execute()) {
            header("Location: admin_packs.php?success=1");
            exit();
        } else {
            echo "Erreur : " . $stmt->error;
        }

        $stmt->close();
    }

    // Récupérer les détails du pack publicitaire
    $sql = "SELECT * FROM packspublicitaires WHERE id_packspublicitaires = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pack = $result->fetch_assoc();

    $stmt->close();
} else {
    header("Location: admin_packs.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier le Pack Publicitaire</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="admin_edit_pack.css">
</head>

<body>
    <header class="header-custom">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h4">Modifier le Pack Publicitaire</h1>
            <nav class="navbar navbar-expand-lg navbar-dark p-0">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="admin_packs.php">Retour à la Gestion des Packs Publicitaires</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>
    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form action="admin_edit_pack.php?id=<?php echo $id; ?>" method="POST" class="bg-light p-4 rounded shadow">
                    <div class="form-group">
                        <label for="nom">Nom:</label>
                        <input type="text" id="nom" name="nom" class="form-control" value="<?php echo htmlspecialchars($pack['nom']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="duree">Durée (jours):</label>
                        <input type="number" id="duree" name="duree" class="form-control" value="<?php echo htmlspecialchars($pack['duree']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="prix">Tarif:</label>
                        <input type="text" id="prix" name="prix" class="form-control" value="<?php echo htmlspecialchars($pack['prix']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">description:</label>
                        <textarea id="description" name="description" rows="5" class="form-control" required><?php echo htmlspecialchars($pack['description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image">image:</label>
                        <input type="file" id="image" name="image" class="form-control" value="<?php echo htmlspecialchars($pack['image']); ?>" required>
                    </div>
                    <button type="submit" name="edit_pack" class="btn btn-primary btn-block">Modifier Pack Publicitaire</button>
                </form>
            </div>
        </div>
    </main>
    <footer class="footer-custom">
        <div class="container text-center">
            <p>&copy; 2024 Artisanat. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>