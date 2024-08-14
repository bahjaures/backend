<?php
session_start();
include 'includes/db.php';

if ($_SESSION['role'] != 'client') {
    header("Location: login.php");
    exit();
}

$article_nom = $_GET['article_nom'] ?? '';

// Récupérer les articles en fonction des critères de recherche
$sql_articles = "SELECT * FROM Articles WHERE nom LIKE ?";
$stmt_articles = $conn->prepare($sql_articles);
$article_nom_param = '%' . $article_nom . '%';
$stmt_articles->bind_param("s", $article_nom_param);
$stmt_articles->execute();
$result_articles = $stmt_articles->get_result();
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

        <h2>Recherche Articles</h2>
        <form method="get" action="">
            <div class="form-group">
                <input type="text" name="article_nom" class="form-control" placeholder="Rechercher par nom d'article" value="<?php echo htmlspecialchars($article_nom); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Rechercher Articles</button>
        </form>
        <div class="results mt-4">
            <?php if ($result_articles->num_rows > 0) : ?>
                <ul class="list-group">
                    <?php while ($article = $result_articles->fetch_assoc()) : ?>
                        <li class="list-group-item">
                            <p>Nom: <?php echo htmlspecialchars($article['nom']); ?></p>
                            <p>Description: <?php echo htmlspecialchars($article['description']); ?></p>
                            <p>Prix: <?php echo htmlspecialchars($article['prix']); ?> cfa</p>
                            <p>Contact: <?php echo htmlspecialchars($article['contact']); ?></p>
                            <form method="post" action="client_orders.php">
                                <input type="hidden" name="article_id" value="<?php echo $article['id_Articles']; ?>">
                                <div class="form-group">
                                    <label for="quantite">Quantité:</label>
                                    <input type="number" name="quantite" class="form-control" min="1" value="1" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Acheter</button>
                            </form>
                            <p>
                                <a href="send_message.php?to=<?php echo $article['contact']; ?>" class="btn btn-secondary btn-sm">Envoyer un message</a>
                            </p>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else : ?>
                <p>Aucun article trouvé.</p>
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

