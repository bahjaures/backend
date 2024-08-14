<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'livreur') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Requête SQL pour récupérer les informations du livreur
$sql = "SELECT * FROM livreurs WHERE id_utilisateur = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$livreur = $result->fetch_assoc();

if (!$livreur) {
    echo "Profil livreur non trouvé.";
    exit();
}

// Requête SQL pour récupérer les assignations du livreur
$sql = "SELECT nom_article, description, adresse_livraison, date_livraison, contact_client FROM assignations WHERE id_livreur = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$assignations_result = $stmt->get_result();

if ($assignations_result === false) {
    echo "Erreur SQL : " . $conn->error;
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Profil Livreur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            color: #333;
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        header {
            background-color: #343a40;
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        header nav ul {
            margin: 0;
            padding: 0;
            list-style: none;
            display: flex;
            gap: 15px;
        }

        header nav ul li {
            margin: 0;
        }

        header nav ul li a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }

        header nav ul li a:hover {
            color: #ffd700;
        }

        main {
            margin-top: 30px;
        }

        .card {
            border: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
        }

        .card-body {
            text-align: center;
            padding: 20px;
        }

        .card-body img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }

        .table {
            margin-top: 20px;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table thead {
            background-color: #343a40;
            color: white;
        }

        .table thead th {
            border: none;
            padding: 15px;
            text-align: center;
        }

        .table tbody tr {
            transition: background-color 0.3s;
        }

        .table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .table tbody td {
            padding: 15px;
            text-align: center;
        }

        footer {
            background-color: #343a40;
            color: white;
            padding: 15px 0;
            position: fixed;
            width: 100%;
            bottom: 0;
            text-align: center;
        }

        footer a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }

        footer a:hover {
            color: #ffd700;
        }

        .btn-primary {
            background-color: #4caf50;
            border: none;
            transition: background-color 0.3s, transform 0.3s;
            padding: 10px 20px;
            border-radius: 50px;
        }

        .btn-primary:hover {
            background-color: #45a049;
            transform: scale(1.05);
        }

        .btn-primary:focus,
        .btn-primary:active {
            background-color: #45a049 !important;
        }
    </style>
</head>

<body>
    <header class="p-3">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h3">Profil Livreur</h1>
            <nav>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link text-white" href="view_messages.php">Messages reçus</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="logout.php">Déconnexion</a></li>
                    <li><a class="btn btn-primary" href="edit_livreur_profile.php">Mettre à jour</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container mt-5">
        <div class="card mb-4">
            <div class="card-body">
                <?php if (!empty($livreur['photo'])) : ?>
                    <img src="<?php echo $livreur['photo']; ?>" alt="Photo de profil">
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($livreur['nom']); ?></h3>
                <p>Contact: <?php echo htmlspecialchars($livreur['contact']); ?></p>
                <p>Email: <?php echo htmlspecialchars($livreur['email']); ?></p>
                <p>Adresse: <?php echo isset($livreur['adresse']) ? htmlspecialchars($livreur['adresse']) : 'Non renseigné'; ?></p>
            </div>
        </div>

        <h4>Mes Assignations de Livraison</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nom de l'Article</th>
                    <th>Description</th>
                    <th>Adresse de Livraison</th>
                    <th>Date de Livraison</th>
                    <th>Contact Client</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($assignation = $assignations_result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($assignation['nom_article']); ?></td>
                        <td><?php echo htmlspecialchars($assignation['description']); ?></td>
                        <td><?php echo htmlspecialchars($assignation['adresse_livraison']); ?></td>
                        <td><?php echo htmlspecialchars($assignation['date_livraison']); ?></td>
                        <td><?php echo htmlspecialchars($assignation['contact_client']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
    <footer>
        <p>&copy; 2024 Artisanat Express. Tous droits réservés. <a href="mentions_legales.php">Mentions Légales</a></p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>