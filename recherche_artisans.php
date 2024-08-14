<?php
session_start();
include 'includes/db.php';

if ($_SESSION['role'] != 'client') {
    header("Location: login.php");
    exit();
}

$specialite = $_GET['specialite'] ?? '';
$localite = $_GET['localite'] ?? '';

// Récupérer les artisans en fonction des critères de recherche
$sql_artisans = "SELECT * FROM Artisans WHERE specialite LIKE ? AND localite LIKE ?";
$stmt_artisans = $conn->prepare($sql_artisans);
$specialite_param = '%' . $specialite . '%';
$localite_param = '%' . $localite . '%';
$stmt_artisans->bind_param("ss", $specialite_param, $localite_param);
$stmt_artisans->execute();
$result_artisans = $stmt_artisans->get_result();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Recherche</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="recherche.css">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="#">Artisanat express</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="recherche_artisans.php">Recherche Artisans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="recherche_articles.php">Recherche Articles</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Déconnexion</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <main class="container mt-4 flex-grow-1">
        <h2>Recherche Artisans</h2>
        <form method="get" action="">
            <div class="form-group">
                <input type="text" name="specialite" class="form-control" placeholder="Rechercher par spécialité" value="<?php echo htmlspecialchars($specialite); ?>">
            </div>
            <div class="form-group">
                <input type="text" name="localite" class="form-control" placeholder="Rechercher par localité" value="<?php echo htmlspecialchars($localite); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Rechercher Artisans</button>
        </form>
        <div class="results mt-4">
            <?php if ($result_artisans->num_rows > 0) : ?>
                <ul class="list-group">
                    <?php while ($artisan = $result_artisans->fetch_assoc()) : ?>
                        <li class="list-group-item">
                            <p>Nom: <?php echo htmlspecialchars($artisan['nom']); ?></p>
                            <p>Spécialité: <?php echo htmlspecialchars($artisan['specialite']); ?></p>
                            <p>Contact: <?php echo htmlspecialchars($artisan['contact']); ?></p>
                            <p>Localisation: <?php echo htmlspecialchars($artisan['localite']); ?></p>
                            <p>
                                <a href="view_artisan_profile.php?id=<?php echo $artisan['id_utilisateur']; ?>" class="btn btn-info btn-sm">Voir Profil</a>
                                <a href="send_message.php?to=<?php echo $artisan['id_utilisateur']; ?>" class="btn btn-secondary btn-sm">Envoyer un message</a>
                            </p>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else : ?>
                <p>Aucun artisan trouvé.</p>
            <?php endif; ?>
        </div>
    </main>
    <footer>
        <div class="container p-4">
            <p>&copy; 2024 Artisanat. Tous droits réservés.</p>
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>