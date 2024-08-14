<?php
session_start();
include 'includes/db.php';

if ($_SESSION['role'] != 'artisan') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'artisan
$sql = "SELECT * FROM Artisans WHERE id_utilisateur = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$artisan = $result->fetch_assoc();

if (!$artisan) {
    echo "Profil artisan non trouvé.";
    exit();
}

if (isset($_POST['update'])) {
    $nom = $_POST['nom'];
    $specialite = $_POST['specialite'];
    $adresse = $_POST['adresse'];
    $contact = $_POST['contact'];
    $quartier = $_POST['quartier'];
    $experience = $_POST['experience'];
    $localite = $_POST['localite'];

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $uploads_dir = 'uploads';
        if (!is_dir($uploads_dir)) {
            mkdir($uploads_dir, 0777, true);
        }
        $photo_path = $uploads_dir . '/' . basename($_FILES['photo']['name']);
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
            echo "Erreur lors de l'upload de la photo.";
            exit();
        }
    } else {
        $photo_path = $artisan['photo'];
    }

    $sql = "UPDATE Artisans SET nom = ?, specialite = ?, localite = ?, experience = ?, adresse = ?, contact = ?, quartier = ?, photo = ? WHERE id_utilisateur = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi", $nom, $specialite, $localite, $experience, $adresse, $contact, $quartier, $photo_path, $user_id);

    if ($stmt->execute()) {
        echo "Profil artisan mis à jour avec succès.";
    } else {
        echo "Erreur: " . $stmt->error;
    }

    $stmt->close();
    header("Location: artisan_service_profile.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier Profil Artisan</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="edit_artisans.css">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="#">Artisanat</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="artisan_profile.php">Mon Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Déconnexion</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Modifier Profil Artisan</h2>
        <form action="edit_artisan_profile.php" method="post" enctype="multipart/form-data" class="p-4 border rounded bg-light shadow-lg">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="nom">Nom:</label>
                    <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($artisan['nom']); ?>" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="specialite">Spécialité:</label>
                    <input type="text" id="specialite" name="specialite" value="<?php echo htmlspecialchars($artisan['specialite']); ?>" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label for="adresse">Adresse:</label>
                <input type="text" id="adresse" name="adresse" value="<?php echo htmlspecialchars($artisan['adresse']); ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="contact">Téléphone:</label>
                <input type="text" id="contact" name="contact" value="<?php echo htmlspecialchars($artisan['contact']); ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="quartier">Quartier:</label>
                <input type="text" id="quartier" name="quartier" value="<?php echo htmlspecialchars($artisan['quartier']); ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="localite">Localité:</label>
                <input type="text" id="localite" name="localite" value="<?php echo htmlspecialchars($artisan['localite']); ?>" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="experience">Expérience:</label>
                <textarea id="experience" name="experience" rows="5" class="form-control" required><?php echo htmlspecialchars($artisan['experience']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="photo">Photo de profil:</label>
                <input type="file" id="photo" name="photo" class="form-control-file">
            </div>
            <button type="submit" name="update" class="btn btn-primary btn-block">Mettre à jour</button>
        </form>
        <p class="mt-3 text-center"><a href="artisan_profile.php" class="btn btn-secondary">Retour au profil artisan</a></p>
    </div>

    <footer class="footer bg-dark text-light py-10">
        <div class="container text-center">
            &copy; 2024 Artisanat. Tous droits réservés.
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>