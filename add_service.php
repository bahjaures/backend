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

if (isset($_POST['add'])) {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $contact = $_POST['contact'];
    $localite = $_POST['localite'];
    $quartier = $_POST['quartier'];
    $prix = $_POST['prix'];

    // Gestion du téléchargement de la photo
    $photo = "";
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $photo_dir = "uploads/services/";

        // Vérifiez si le répertoire existe, sinon créez-le
        if (!file_exists($photo_dir)) {
            mkdir($photo_dir, 0777, true);
        }

        $photo_name = time() . "_" . basename($_FILES["photo"]["name"]);
        $photo_path = $photo_dir . $photo_name;

        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $photo_path)) {
            $photo = $photo_path;
        } else {
            echo "Erreur lors du téléchargement de la photo.";
            exit();
        }
    }

    $sql = "INSERT INTO Services (id_artisans, nom, description, contact, localite, quartier, prix, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssds", $id_artisan, $nom, $description, $contact, $localite, $quartier, $prix, $photo);

    if ($stmt->execute()) {
        echo "Service ajouté avec succès.";
    } else {
        echo "Erreur: " . $stmt->error;
    }

    $stmt->close();
    header("Location: manage_services.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Ajouter Service</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: url('assets/images/background.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Arial', sans-serif;
            color: #333;
        }

        .form-container {
            max-width: 600px;
            margin: 5% auto;
            background: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1s ease-in-out;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 700;
            color: green;
        }

        label {
            font-weight: bold;
            color: #555;
        }

        button[type="submit"] {
            width: 100%;
            background-color: green;
            border: none;
            color: white;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        p a {
            color: #007bff;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="form-container">
        <h2>Ajouter Service</h2>
        <form action="add_service.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nom">Nom:</label>
                <input type="text" class="form-control" id="nom" name="nom" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label for="contact">Contact:</label>
                <input type="text" class="form-control" id="contact" name="contact" required>
            </div>

            <div class="form-group">
                <label for="localite">Localité:</label>
                <input type="text" class="form-control" id="localite" name="localite" required>
            </div>

            <div class="form-group">
                <label for="quartier">Quartier:</label>
                <input type="text" class="form-control" id="quartier" name="quartier" required>
            </div>

            <div class="form-group">
                <label for="prix">Prix:</label>
                <input type="text" class="form-control" id="prix" name="prix" required>
            </div>

            <div class="form-group">
                <label for="photo">Photo de service:</label>
                <input type="file" class="form-control-file" id="photo" name="photo">
            </div>

            <button type="submit" name="add">Ajouter</button>
        </form>
        <p class="text-center"><a href="artisan_service_profile.php">Retour au profil artisan</a></p>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>