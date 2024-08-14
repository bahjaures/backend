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

if (isset($_POST['update'])) {
    $nom = $_POST['nom'];
    $contact = $_POST['contact'];
    $adresse = $_POST['adresse'];
    $localite = $_POST['localite'];
    $quartier = $_POST['quartier'];

    // Gestion de l'upload de la photo
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
        $photo_path = $client['photo'];
    }

    $sql = "UPDATE Clients SET nom = ?, contact = ?, adresse = ?, localite = ?, quartier = ?, photo = ? WHERE id_utilisateur = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $nom, $contact, $adresse, $localite, $quartier, $photo_path, $user_id);

    if ($stmt->execute()) {
        echo "Profil client mis à jour avec succès.";
    } else {
        echo "Erreur: " . $stmt->error;
    }

    $stmt->close();
    header("Location: client_profile.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier Profil Client</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="edit_client_profile.css">
</head>

<body>
    <header>
        <h1>Modifier Profil Client</h1>
        <nav>
            <ul>
                <li><a href="client_profile.php"><i class="fas fa-arrow-left"></i> Retour au profil client</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h2 class="text-primary">Modifier Profil Client</h2>
        <form action="edit_client_profile.php" method="post" enctype="multipart/form-data" class="bg-light p-4 rounded shadow-sm">
            <div class="form-group mb-3">
                <label for="nom">Nom:</label>
                <input type="text" id="nom" name="nom" class="form-control" value="<?php echo htmlspecialchars($client['nom']); ?>" required>
            </div>

            <div class="form-group mb-3">
                <label for="contact">Téléphone:</label>
                <input type="text" id="contact" name="contact" class="form-control" value="<?php echo htmlspecialchars($client['contact']); ?>" required>
            </div>

            <div class="form-group mb-3">
                <label for="adresse">Adresse:</label>
                <input type="text" id="adresse" name="adresse" class="form-control" value="<?php echo htmlspecialchars($client['adresse']); ?>" required>
            </div>

            <div class="form-group mb-3">
                <label for="localite">Localité:</label>
                <input type="text" id="localite" name="localite" class="form-control" value="<?php echo htmlspecialchars($client['localite']); ?>" required>
            </div>

            <div class="form-group mb-3">
                <label for="quartier">Quartier:</label>
                <input type="text" id="quartier" name="quartier" class="form-control" value="<?php echo htmlspecialchars($client['quartier']); ?>" required>
            </div>

            <div class="form-group mb-4">
                <label for="photo">Photo de profil:</label>
                <input type="file" id="photo" name="photo" class="form-control-file">
            </div>

            <button type="submit" name="update" class="btn btn-success btn-block"><i class="fas fa-save"></i> Mettre à jour</button>
        </form>
    </div>

    <footer class="footer mt-3">
        <div class="container text-center">
            <p>&copy; 2024 Artisanat. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.min.js"></script>
</body>

</html>