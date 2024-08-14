<?php
session_start();
include 'includes/db.php';

if ($_SESSION['role'] != 'artisan') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Vérifier l'identifiant de l'artisan
$sql_check = "SELECT id_Artisans FROM Artisans WHERE id_utilisateur = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $user_id);
$stmt_check->execute();
$stmt_check->bind_result($id_artisan);
$stmt_check->fetch();
$stmt_check->close();

if (!$id_artisan) {
    echo "Erreur : Utilisateur non trouvé dans les artisans.";
    exit();
}

// Récupérer les informations de l'artisan
$sql = "SELECT Utilisateurs.*, Artisans.* 
        FROM Utilisateurs 
        JOIN Artisans ON Utilisateurs.id_utilisateurs = Artisans.id_utilisateur 
        WHERE Utilisateurs.id_utilisateurs = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$artisan = $result->fetch_assoc();

if (!$artisan) {
    echo "Profil artisan non trouvé.";
    exit();
}

// Récupérer les abonnements de l'artisan
$sql = "SELECT Abonnements.nom, Abonnements.duree, Abonnements.prix, User_Abonnements.date_debut, User_Abonnements.date_fin 
        FROM User_Abonnements 
        JOIN Abonnements ON User_Abonnements.id_abonnement = Abonnements.id_Abonnements 
        WHERE User_Abonnements.id_utilisateur = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$abonnements_result = $stmt->get_result();

// Récupérer les packs publicitaires de l'artisan
$sql = "SELECT Packspublicitaires.nom, Packspublicitaires.duree, Packspublicitaires.prix, User_Packs.date_debut, User_Packs.date_fin 
        FROM User_Packs 
        JOIN Packspublicitaires ON User_Packs.id_pack = Packspublicitaires.id_Packspublicitaires 
        WHERE User_Packs.id_utilisateur = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$packs_result = $stmt->get_result();

// Récupérer les services de l'artisan
$sql = "SELECT * FROM Services WHERE id_artisans = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_artisan);
$stmt->execute();
$services_result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artisan Services</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }

        header {
            background: linear-gradient(45deg, black, black);
            padding: 20px 0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            font-weight: 600;
            color: #fff;
            margin: 0;
        }

        header .nav-link {
            color: #fff;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        header .nav-link:hover {
            color: #ddd;
        }

        .profile-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .profile-img:hover {
            transform: scale(1.05);
        }

        .card-title {
            color: #ff6347;
            font-weight: 600;
            font-size: 1.3rem;
        }

        .card-body p {
            margin: 0;
            color: #555;
        }

        .card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        footer {
            background-color: black;
            color: #fff;
            padding: 20px;
            font-size: 0.9rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        footer p {
            margin: 0;
        }

        footer a {
            color: #ff6347;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: #ffa07a;
        }

        footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(45deg, black, black);
        }

        @media (max-width: 768px) {
            header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>

    <header>
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h3">Artisan Services</h1>
            <nav>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link" href="abonnements.php">Abonnements</a></li>
                    <li class="nav-item"><a class="nav-link" href="edit_artisan_profile.php">Modifier le Profil</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_services.php">Gestion des Services</a></li>
                    <li class="nav-item"><a class="btn btn-light" href="recherche_livreur.php">Recherche Livreur</a></li>
                    <li class="nav-item"><a class="nav-link" href="view_messages.php">Messages Reçus</a></li>
                    <li class="nav-item"><a class="nav-link" href="confier_service.php">Confier Service</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container mt-5">
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="<?php echo $artisan['photo']; ?>" alt="Photo de profil" class="profile-img mb-3">
                <h3><?php echo htmlspecialchars($artisan['nom']); ?></h3>
                <p><?php echo htmlspecialchars($artisan['specialite']); ?></p>
                <p>Adresse: <?php echo htmlspecialchars($artisan['adresse']); ?></p>
                <p>Expérience: <?php echo htmlspecialchars($artisan['experience']); ?></p>
                <p>Localité: <?php echo htmlspecialchars($artisan['localite']); ?></p>
                <p>Contact: <?php echo htmlspecialchars($artisan['contact']); ?></p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title">Abonnements</h3>
                        <ul class="list-group">
                            <?php while ($abonnement = $abonnements_result->fetch_assoc()) : ?>
                                <li class="list-group-item">
                                    <p>Nom : <?php echo htmlspecialchars($abonnement['nom']); ?></p>
                                    <p>Durée : <?php echo htmlspecialchars($abonnement['duree']); ?> jours</p>
                                    <p>Prix : <?php echo htmlspecialchars($abonnement['prix']); ?> CFA</p>
                                    <p>Du : <?php echo htmlspecialchars($abonnement['date_debut']); ?> au <?php echo htmlspecialchars($abonnement['date_fin']); ?></p>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title">Packs Publicitaires</h3>
                        <ul class="list-group">
                            <?php while ($pack = $packs_result->fetch_assoc()) : ?>
                                <li class="list-group-item">
                                    <p>Nom : <?php echo htmlspecialchars($pack['nom']); ?></p>
                                    <p>Durée : <?php echo htmlspecialchars($pack['duree']); ?> jours</p>
                                    <p>Prix : <?php echo htmlspecialchars($pack['prix']); ?> CFA</p>
                                    <p>Du : <?php echo htmlspecialchars($pack['date_debut']); ?> au <?php echo htmlspecialchars($pack['date_fin']); ?></p>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title">Services Disponibles</h3>
                        <?php if ($services_result->num_rows > 0) : ?>
                            <div class="row">
                                <?php while ($service = $services_result->fetch_assoc()) : ?>
                                    <div class="col-md-4 mb-4">
                                        <div class="card shadow-sm">
                                            <?php if (!empty($service['photo'])) : ?>
                                                <img src="<?php echo htmlspecialchars($service['photo']); ?>" class="card-img-top img-fluid" alt="Image du service">
                                            <?php endif; ?>
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo htmlspecialchars($service['nom']); ?></h5>
                                                <p class="card-text">Description: <?php echo htmlspecialchars($service['description']); ?></p>
                                                <p class="card-text">Contact: <?php echo htmlspecialchars($service['contact']); ?></p>
                                                <p class="card-text">Localité: <?php echo htmlspecialchars($service['localite']); ?></p>
                                                <p class="card-text">Quartier: <?php echo htmlspecialchars($service['quartier']); ?></p>
                                                <p class="card-text">Prix: <?php echo htmlspecialchars($service['prix']); ?> CFA</p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else : ?>
                            <p>Aucun service disponible actuellement.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-dark text-white p-3 text-center">
        <p>&copy; 2024 Artisanat express. Tous droits réservés. <a class="text-white" href="mentions_legales.php">Mentions Légales</a></p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>

</html>