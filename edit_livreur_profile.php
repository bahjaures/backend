<?php
session_start();
include 'includes/db.php';

if ($_SESSION['role'] != 'livreur') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $contact = $_POST['contact'];
    $email = $_POST['email'];
    $localite = $_POST['localite'];
    $adresse = $_POST['adresse'];
    $photo = $_FILES['photo'];

    // Vérifier si un fichier a été téléchargé
    if ($photo['error'] == 0) {
        $photoPath = 'uploads/' . basename($photo['name']);
        move_uploaded_file($photo['tmp_name'], $photoPath);
    } else {
        $photoPath = NULL;
    }

    // Mettre à jour les informations du livreur
    $sql = "UPDATE Livreurs SET nom = ?, contact = ?, email = ?, localite = ?, adresse = ?, photo = IFNULL(?, photo) WHERE id_utilisateur = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $nom, $contact, $email, $localite, $adresse, $photoPath, $user_id);

    if ($stmt->execute()) {
        header("Location: livreur_profile.php?update=success");
        exit();
    } else {
        echo "Erreur lors de la mise à jour. Veuillez réessayer.";
    }

    $stmt->close();
}

// Récupérer les informations actuelles du livreur
$sql = "SELECT * FROM Utilisateurs WHERE id_utilisateurs = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$livreur = $result->fetch_assoc();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier Profil Livreur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="livreur_profile.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: white;
            color: #333;
        }

        header {
            background-color: black;
            color: white;
            padding: 10px 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        header h1 {
            margin: 0;
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
            margin-top: 80px;
        }

        .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            
        }

        .card-body {
            text-align: center;
        }

        .form-group img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-top: 10px;
        }

        footer {
            background-color: black;
            color: white;
            padding: 15px 0;
            position: fixed;
            width: 100%;
            bottom: 0;
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
            transition: background-color 0.3s;
        }

        .btn-primary:hover {
            background-color: #45a049;
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
            <h1 class="h3">Modifier Profil Livreur</h1>
            <nav>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link text-white" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container mt-5">
        <div class="card mb-4">
            <div class="card-body">
                <form method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nom">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($livreur['nom'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="contact">Contact</label>
                        <input type="text" class="form-control" id="contact" name="contact" value="<?php echo htmlspecialchars($livreur['contact'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($livreur['email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="localite">Localité</label>
                        <input type="text" class="form-control" id="localite" name="localite" value="<?php echo htmlspecialchars($livreur['localite'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="adresse">Adresse</label>
                        <input type="text" class="form-control" id="adresse" name="adresse" value="<?php echo htmlspecialchars($livreur['adresse'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="photo">Photo de profil</label>
                        <input type="file" class="form-control-file" id="photo" name="photo">
                        <?php if (!empty($livreur['photo'])) : ?>
                            <img src="<?php echo htmlspecialchars($livreur['photo']); ?>" alt="Photo de profil actuelle">
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </form>
            </div>
        </div>
    </main>
    <footer class="p-3 text-center">
        <p>&copy; 2024 Artisanat Express. Tous droits réservés. <a class="text-white" href="mentions_legales.php">Mentions Légales</a></p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>