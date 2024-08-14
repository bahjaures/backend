<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'artisan' && $_SESSION['role'] != 'client')) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_livreur = $_POST['id_livreurs'];
    $nom_article = $_POST['nom_article'];
    $description = $_POST['description'];
    $adresse_livraison = $_POST['adresse_livraison'];
    $date_livraison = $_POST['date_livraison'];
    $contact_client = $_POST['contact_client'];
    $id_utilisateurs = $_SESSION['id_utilisateur']; // Assuming you store user ID in the session

    // Logique pour enregistrer l'assignation de l'article
    $sql = "INSERT INTO assignations (id_livreur, id_utilisateurs, nom_article, description, adresse_livraison, date_livraison, contact_client) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("iisssss", $id_livreur, $id_utilisateurs, $nom_article, $description, $adresse_livraison, $date_livraison, $contact_client);
        if ($stmt->execute()) {
            $message = "Article confié au livreur avec succès.";
        } else {
            $message = "Erreur lors de l'assignation de l'article.";
        }
        $stmt->close();
    } else {
        $message = "Erreur de préparation de la requête.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Assignation Livreur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header class="p-3 bg-dark text-white">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h3">Assignation Livreur</h1>
            <nav>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link text-white" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container mt-5">
        <h3>Détails du Livreur</h3>
        <p><strong>Nom:</strong> <?php echo htmlspecialchars($livreur['nom']); ?></p>
        <p><strong>Localité:</strong> <?php echo htmlspecialchars($livreur['localite']); ?></p>
        <p><strong>Contact:</strong> <?php echo htmlspecialchars($livreur['contact']); ?></p>

        <!-- Formulaire ou interface pour confier un article au livreur -->
        <form method="POST" action="confier_article.php">
            <input type="hidden" name="id_livreurs" value="<?php echo $livreur['id_livreurs']; ?>">
            <!-- Ajoutez d'autres champs nécessaires ici -->
            <button type="submit" class="btn btn-primary">Confier un article</button>
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