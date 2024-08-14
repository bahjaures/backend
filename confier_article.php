<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'artisan' && $_SESSION['role'] != 'client')) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_livreur = $_POST['id_livreur']; // Assurez-vous que ce champ est correct
    $id_utilisateur = $_SESSION['id_utilisateur']; // Utilisateur actuel, supposant que l'ID utilisateur est stocké dans la session
    $nom_article = $_POST['nom_article'];
    $description = $_POST['description'];
    $adresse_livraison = $_POST['adresse_livraison'];
    $date_livraison = $_POST['date_livraison'];
    $contact_client = $_POST['contact_client'];

    // Insérer l'assignation
    $sql = "INSERT INTO assignations (id_livreur, id_utilisateur, nom_article, description, adresse_livraison, date_livraison, contact_client) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("iisssss", $id_livreur, $id_utilisateur, $nom_article, $description, $adresse_livraison, $date_livraison, $contact_client);
        if ($stmt->execute()) {
            $message = "Article confié au livreur avec succès.";
        } else {
            $message = "Erreur lors de l'assignation de l'article : " . $stmt->error;
        }
        $stmt->close();
    } else {
        $message = "Erreur de préparation de la requête : " . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Confier un Article</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header class="p-3 bg-dark text-white">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h3">Confier un Article</h1>
            <nav>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link text-white" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container mt-5">
        <?php if (isset($message)) : ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <form method="POST" action="confier_article.php">
            <!-- Assurez-vous que cet ID livreur est transmis correctement -->
            <input type="hidden" name="id_livreur" value="<?php echo htmlspecialchars($_GET['id_livreur']); ?>">

            <div class="form-group">
                <label for="nom_article">Nom de l'Article</label>
                <input type="text" class="form-control" id="nom_article" name="nom_article" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label for="adresse_livraison">Adresse de Livraison</label>
                <input type="text" class="form-control" id="adresse_livraison" name="adresse_livraison" required>
            </div>

            <div class="form-group">
                <label for="date_livraison">Date de Livraison</label>
                <input type="date" class="form-control" id="date_livraison" name="date_livraison" required>
            </div>

            <div class="form-group">
                <label for="contact_client">Contact Client</label>
                <input type="text" class="form-control" id="contact_client" name="contact_client" required>
            </div>

            <button type="submit" class="btn btn-primary">Confier l'Article</button>
        </form>
    </main>
    <footer class="p-3 bg-dark text-white text-center">
        <p>&copy; 2024 Artisanat express. Tous droits réservés. <a class="text-white" href="mentions_legales.php">Mentions Légales</a></p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>