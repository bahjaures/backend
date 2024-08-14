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
    $prix = $_POST['prix'];
    $contact = $_POST['contact'];

    // Gérer l'upload de la photo
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $photo = $_FILES['photo'];
        $photo_name = $photo['name'];
        $photo_tmp_name = $photo['tmp_name'];
        $photo_size = $photo['size'];
        $photo_error = $photo['error'];
        $photo_type = $photo['type'];

        $photo_ext = explode('.', $photo_name);
        $photo_actual_ext = strtolower(end($photo_ext));

        $allowed = array('jpg', 'jpeg', 'png', 'gif');

        if (in_array($photo_actual_ext, $allowed)) {
            if ($photo_error === 0) {
                if ($photo_size < 5000000) { // Limite de taille à 5MB
                    $photo_new_name = uniqid('', true) . "." . $photo_actual_ext;
                    $photo_destination = 'uploads/' . $photo_new_name;

                    if (move_uploaded_file($photo_tmp_name, $photo_destination)) {
                        $sql = "INSERT INTO Articles (id_artisans, nom, prix, contact, photo) VALUES (?, ?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("isdss", $id_artisan, $nom, $prix, $contact, $photo_destination);

                        if ($stmt->execute()) {
                            echo "Article ajouté avec succès.";
                        } else {
                            echo "Erreur: " . $stmt->error;
                        }

                        $stmt->close();
                        header("Location: manage_articles.php");
                        exit();
                    } else {
                        echo "Erreur lors de l'upload de la photo.";
                    }
                } else {
                    echo "La taille du fichier est trop grande.";
                }
            } else {
                echo "Erreur lors de l'upload.";
            }
        } else {
            echo "Type de fichier non autorisé.";
        }
    } else {
        echo "Erreur: Fichier non téléchargé.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Ajouter Article</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Roboto', sans-serif;
        }

        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1.5s ease-in-out;
        }

        h2 {
            color: green;
            text-align: center;
            margin-bottom: 20px;

        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        input[type="file"] {
            margin-bottom: 15px;
        }

        button[type="submit"] {
            background-color: #28a745;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #218838;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Ajouter un Article</h2>
        <form action="add_article.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="nom">Nom:</label>
                <input type="text" class="form-control" id="nom" name="nom" required>
            </div>
            <div class="form-group">
                <label for="prix">Prix:</label>
                <input type="text" class="form-control" id="prix" name="prix" required>
            </div>
            <div class="form-group">
                <label for="contact">Contact:</label>
                <input type="text" class="form-control" id="contact" name="contact" required>
            </div>
            <div class="form-group">
                <label for="photo">Photo de l'article:</label>
                <input type="file" class="form-control-file" id="photo" name="photo" accept="image/*" required>
            </div>
            <button type="submit" name="add" class="btn btn-success btn-block">Ajouter</button>
        </form>
        <a href="artisan_vente_profile.php" class="back-link">Retour au profil artisan</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>