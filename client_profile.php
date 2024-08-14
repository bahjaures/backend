<?php
session_start();
include 'includes/db.php';

if ($_SESSION['role'] != 'client') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les informations du client
$sql = "SELECT * FROM Clients WHERE id_utilisateur = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$client = $result->fetch_assoc();

if (!$client) {
    echo "Profil client non trouvé.";
    exit();
}

// Récupérer les commandes en cours
$sql_get_orders = "SELECT Commandes.id_Commandes, Articles.nom, Articles.prix, Articles.photo, Artisans.nom AS artisan_nom, Commandes.quantite, Commandes.statut 
                   FROM Commandes 
                   JOIN Articles ON Commandes.id_Articles = Articles.id_Articles 
                   JOIN Artisans ON Articles.id_artisans = Artisans.id_Artisans
                   WHERE Commandes.id_client = ?";
$stmt_get_orders = $conn->prepare($sql_get_orders);
$stmt_get_orders->bind_param("i", $user_id);
$stmt_get_orders->execute();
$result_orders = $stmt_get_orders->get_result();

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Profil Client</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        header {
            background-color: black;
            color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        h1 {
            font-weight: 600;
        }

        main {
            padding-top: 20px;
            padding-bottom: 40px;
            background: linear-gradient(to right, #f8f9fa, #e9ecef);
        }

        .card {
            border: none;
            border-radius: 15px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }

        .card-body {
            padding: 20px;
        }

        .card-title {
            font-size: 1.5rem;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            border-radius: 30px;
            font-weight: bold;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            box-shadow: 0 8px 16px rgba(0, 91, 187, 0.5);
        }

        footer {
            background-color: #343a40;
            color: #fff;
            box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.1);
        }

        footer a {
            color: #ffc107;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: #fff;
        }

        .rounded-circle {
            border: 4px solid #007bff;
            transition: transform 0.3s ease, border-color 0.3s ease;
        }

        .rounded-circle:hover {
            transform: scale(1.1);
            border-color: #0056b3;
        }

        .nav-link {
            font-size: 1.1rem;
            font-weight: bold;
            color: #ffc107 !important;
        }

        .nav-link:hover {
            color: #fff !important;
        }
    </style>
</head>

<body>
    <header class="p-3">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h3">Profil Client</h1>
            <nav>
                <ul class="nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="menuDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <a class="dropdown-item" href="client_orders.php">Commandes</a>
                            Menu
                        </a>
                        <div class="dropdown-menu" aria-labelledby="menuDropdown">
                            <a class="dropdown-item" href="client_orders.php">Commandes</a>
                            <a class="dropdown-item" href="confier_article.php">Livraison</a>
                            <a class="dropdown-item" href="recherche_livreur.php">Recherche Livraison</a>
                            <a class="dropdown-item" href="view_messages.php">Messages Reçus</a>
                            <a class="dropdown-item" href="sent_messages.php">Messages Envoyés</a>
                            <div class="dropdown-divider"></div>
                            <h6 class="dropdown-header">Recherche d'artisans et de services</h6>
                            <a class="dropdown-item" href="recherche_artisans.php">Recherche Artisans</a>
                            <a class="dropdown-item" href="recherche_articles.php">Recherche Articles</a>
                        </div>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container mt-5">
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="<?php echo $client['photo']; ?>" alt="Photo de profil" class="rounded-circle mb-3" style="width:150px;height:150px;">
                <h2 class="card-title"><?php echo htmlspecialchars($client['nom']); ?></h2>
                <p class="card-text">Contact: <?php echo htmlspecialchars($client['contact']); ?></p>
                <p class="card-text">Localité: <?php echo htmlspecialchars($client['localite']); ?></p>
                <p class="card-text">Quartier: <?php echo htmlspecialchars($client['quartier']); ?></p>
            </div>
        </div>

        <!-- Section Commandes en cours -->
        <div class="card mb-4">
            <div class="card-body">
                <h3 class="card-title"><i class="fas fa-shopping-cart"></i> Commandes en cours</h3>
                <?php if ($result_orders->num_rows > 0) { ?>
                    <ul class="list-group">
                        <?php while ($order = $result_orders->fetch_assoc()) { ?>
                            <li class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo htmlspecialchars($order['photo']); ?>" alt="Article" class="img-fluid rounded-circle mr-3" style="width: 50px; height: 50px;">
                                    <div>
                                        <h5><?php echo htmlspecialchars($order['nom']); ?> (x<?php echo htmlspecialchars($order['quantite']); ?>)</h5>
                                        <p>Artisan : <?php echo htmlspecialchars($order['artisan_nom']); ?></p>
                                        <p>Prix : <?php echo htmlspecialchars($order['prix']); ?> F CFA</p>
                                        <p>Statut : <?php echo htmlspecialchars($order['statut']); ?></p>
                                    </div>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } else { ?>
                    <p>Aucune commande en cours.</p>
                <?php } ?>
            </div>
        </div>
    </main>
    <footer class="p-3 text-center">
        <p>&copy; 2024 Votre Entreprise - Tous droits réservés. | <a href="#">Mentions légales</a></p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>