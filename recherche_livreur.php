<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'artisan' && $_SESSION['role'] != 'client')) {
    header("Location: login.php");
    exit();
}

$livreurs = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $localite = $_POST['localite'];

    // Requête SQL pour récupérer les livreurs correspondant aux critères de recherche
    $sql = "SELECT * FROM livreurs WHERE localite LIKE ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $localite_param = '%' . $localite . '%';
        $stmt->bind_param("s", $localite_param);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $livreurs = $result->fetch_all(MYSQLI_ASSOC);
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Recherche de Livreur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header class="p-3 bg-dark text-white">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h3">Recherche de Livreur</h1>
            <nav>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link text-white" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container mt-5">
        <form method="POST" action="recherche_livreur.php">
            <div class="form-group">
                <label for="localite">Localité</label>
                <input type="text" class="form-control" id="localite" name="localite" placeholder="Entrez la localité" required>
            </div>
            <button type="submit" class="btn btn-primary">Rechercher</button>
        </form>

        <?php if (!empty($livreurs)) : ?>
            <h3 class="mt-5">Résultats de la recherche</h3>
            <div class="row">
                <?php foreach ($livreurs as $livreur) : ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($livreur['nom']); ?></h5>
                                <p class="card-text">Localité: <?php echo htmlspecialchars($livreur['localite']); ?></p>
                                <p class="card-text">Contact: <?php echo htmlspecialchars($livreur['contact']); ?></p>
                                <a href="assign_delivery.php?id_livreur=<?php echo $livreur['id_livreur']; ?>" class="btn btn-primary">Confier un article</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST') : ?>
            <p class="text-center mt-5">Aucun livreur trouvé.</p>
        <?php endif; ?>
    </main>
    <footer class="p-3 bg-dark text-white text-center">
        <p>&copy; 2024 Artisanat express. Tous droits réservés. <a class="text-white" href="mentions_legales.php">Mentions Légales</a></p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>