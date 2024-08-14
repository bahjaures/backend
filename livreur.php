<?php
session_start();
include 'includes/db.php';

// Retirer la vérification de session ici
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }

// Initialisation de la variable de recherche
$search_localite = '';

if (isset($_GET['search'])) {
    $search_localite = $_GET['search'];
    // Modifier la requête SQL pour filtrer par localité
    $sql = "SELECT * FROM Livreurs WHERE localite LIKE ?";
    $stmt = $conn->prepare($sql);
    $search_param = "%" . $search_localite . "%";
    $stmt->bind_param("s", $search_param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Récupérer tous les livreurs si aucune recherche n'est effectuée
    $sql = "SELECT * FROM Livreurs";
    $result = $conn->query($sql);
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Liste des Livreurs</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .card-img-top {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
        }

        footer {
            background-color: #343a40;
            color: #fff;
        }

        footer p {
            margin: 0;
        }

        .search-bar {
            max-width: 400px;
            margin: 20px auto;
        }

        .search-bar input {
            border-radius: 20px;
        }

        .search-bar button {
            border-radius: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2 class="my-4 text-center">Liste des Livreurs</h2>

        <!-- Barre de recherche -->
        <div class="search-bar mb-4">
            <form action="" method="GET" class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Rechercher par localité" value="<?php echo htmlspecialchars($search_localite); ?>">
                <div class="input-group-append">
                    <button class="btn btn-primary" type="submit">Rechercher</button>
                </div>
            </form>
        </div>

        <?php if ($result->num_rows > 0) : ?>
            <div class="row">
                <?php while ($livreur = $result->fetch_assoc()) : ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <?php if (!empty($livreur['photo'])) : ?>
                                <img src="<?php echo htmlspecialchars($livreur['photo']); ?>" class="card-img-top" alt="Photo du livreur">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($livreur['nom']); ?></h5>
                                <p class="card-text">Contact: <?php echo htmlspecialchars($livreur['contact']); ?></p>
                                <p class="card-text">Localité: <?php echo htmlspecialchars($livreur['localite']); ?></p>
                                <p class="card-text">Adresse: <?php echo htmlspecialchars($livreur['adresse']); ?></p>
                                <a href="send_message.php" class="btn btn-success">Envoyer un message</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <p>Aucun livreur trouvé pour cette localité.</p>
        <?php endif; ?>

        <p><a href="index.php" class="btn btn-secondary mt-4">Retour à l'accueil</a></p>
    </div>

    <footer class="text-center p-4 mt-4">
        <p>&copy; 2024 Artisanat. Tous droits réservés.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>