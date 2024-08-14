<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'administrateur') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Tableau de Bord Administrateur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="admin.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="d-flex flex-column min-vh-100">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="#">Tableau de Bord</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="admin_users.php">Gérer les Utilisateurs</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_abonnements.php">Gérer les Abonnements</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="admin_packs.php">Gérer les Packs Publicitaires</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Déconnexion</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div class="container mt-5 flex-grow-1">
            <div class="jumbotron text-center">
                <h1 class="display-4">Bienvenue, Administrateur</h1>
                <p class="lead">Sélectionnez une option dans le menu pour gérer le site.</p>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <h3 class="card-title">Gérer les Utilisateurs</h3>
                            <a href="admin_users.php" class="btn btn-primary">Accéder</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-calendar-alt fa-3x mb-3"></i>
                            <h3 class="card-title">Gérer les Abonnements</h3>
                            <a href="admin_abonnements.php" class="btn btn-primary">Accéder</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-ad fa-3x mb-3"></i>
                            <h3 class="card-title">Gérer les Packs Publicitaires</h3>
                            <a href="admin_packs.php" class="btn btn-primary">Accéder</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="bg-dark text-white p-3 text-center mt-5">
            <p>&copy; 2024 Artisanat express. Tous droits réservés. <a class="text-white" href="mentions_legales.php">Mentions Légales</a></p>
        </footer>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>