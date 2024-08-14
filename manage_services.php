<?php
session_start();
include 'includes/db.php';

if ($_SESSION['role'] != 'artisan') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Vérifier que l'utilisateur existe dans la table Artisans
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

// Récupérer les services de l'artisan
$sql = "SELECT * FROM Services WHERE id_artisans = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_artisan);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Services</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="manage_service.css">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        header {
            background: #343a40;
            color: white;
            padding: 15px 0;
        }

        header h2 {
            margin: 0;
            font-size: 2rem;
        }

        .card {
            margin-bottom: 20px;
            box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .list-group-item {
            border: none;
            padding: 15px;
            transition: background 0.3s ease;
        }

        .list-group-item:hover {
            background-color: #f1f1f1;
        }

        .btn {
            margin-right: 10px;
        }

        .card-title {
            color: #343a40;
        }

        .card-text {
            color: #6c757d;
        }

        .container {
            margin-top: 30px;
        }

        .alert {
            margin-top: 20px;
        }

        a.btn-outline-primary,
        a.btn-outline-primary:hover {
            color: white;
            background-color: #007bff;
        }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-cogs"></i> Gestion des Services</h2>
                <div>
                    <a href="artisan_service_profile.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left"></i> Retour au profil artisan</a>
                    <a href="add_service.php" class="btn btn-success"><i class="fas fa-plus-circle"></i> Ajouter un nouveau service</a>
                </div>
            </div>
        </div>
    </header>

    <div class="container">
        <?php if ($result->num_rows > 0) : ?>
            <div class="card">
                <ul class="list-group list-group-flush">
                    <?php while ($service = $result->fetch_assoc()) : ?>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="card shadow-sm">
                                        <?php if (!empty($service['photo'])) : ?>
                                            <img src="<?php echo htmlspecialchars($service['photo']); ?>" class="card-img-top" alt="Photo de l'article">
                                        <?php endif; ?>
                                        <h5 class="card-title"><?php echo htmlspecialchars($service['nom']); ?></h5>
                                        <p class="card-text"><?php echo htmlspecialchars($service['description']); ?></p>
                                        <p class="card-text"><i class="fas fa-phone"></i> Contact: <?php echo htmlspecialchars($service['contact']); ?></p>
                                        <p class="card-text"><i class="fas fa-map-marker-alt"></i> Localité: <?php echo htmlspecialchars($service['localite']); ?></p>
                                        <p class="card-text"><i class="fas fa-location-arrow"></i> Quartier: <?php echo htmlspecialchars($service['quartier']); ?></p>
                                    </div>
                                    <div>
                                        <a href="edit_service.php?id=<?php echo $service['id_Services']; ?>" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Modifier</a>
                                        <a href="delete_service.php?id=<?php echo $service['id_Services']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce service?');" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Supprimer</a>
                                    </div>
                                </div>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        <?php else : ?>
            <p class="alert alert-info">Aucun service trouvé.</p>
        <?php endif; ?>
    </div>

    <footer class="text-center mt-4">
        <div class="container">
            <p>&copy; 2024 Artisanat Express. Tous droits réservés. Développé par <span>Bah Jaures</span>.</p>
        </div>
    </footer>

</body>

</html>