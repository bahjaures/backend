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

$service_id = $_GET['id'];
$sql = "SELECT * FROM Services WHERE id_Services = ? AND id_artisans = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $service_id, $id_artisan);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Service non trouvé.";
    exit();
}

$service = $result->fetch_assoc();

if (isset($_POST['update'])) {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $contact = $_POST['contact'];
    $localite = $_POST['localite'];
    $quartier = $_POST['quartier'];

    $sql_update = "UPDATE Services SET nom = ?, description = ?, contact = ?, localite = ?, quartier = ? WHERE id_Services = ? AND id_artisans = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssssii", $nom, $description, $contact, $localite, $quartier, $service_id, $id_artisan);

    if ($stmt_update->execute()) {
        echo "Service mis à jour avec succès.";
    } else {
        echo "Erreur: " . $stmt_update->error;
    }

    $stmt_update->close();
    header("Location: manage_services.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier Service</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="edit_service.css">
</head>

<body>
    <header class="bg-success text-white text-left py-3">
        <h1>Modifier Service</h1>
        <p class="mt-3"><a href="manage_services.php" class="btn btn-secondary ">Retour à la gestion des services</a></p>

    </header>
    <div class="container">
        <div class="card">
            <form action="edit_service.php?id=<?php echo $service_id; ?>" method="post">
                <div class="form-group">
                    <label for="nom">Nom:</label>
                    <input type="text" class="form-control" id="nom" name="nom" value="<?php echo htmlspecialchars($service['nom']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($service['description']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="contact">Contact:</label>
                    <input type="text" class="form-control" id="contact" name="contact" value="<?php echo htmlspecialchars($service['contact']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="localite">Localité:</label>
                    <input type="text" class="form-control" id="localite" name="localite" value="<?php echo htmlspecialchars($service['localite']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="quartier">Quartier:</label>
                    <input type="text" class="form-control" id="quartier" name="quartier" value="<?php echo htmlspecialchars($service['quartier']); ?>" required>
                </div>

                <button type="submit" class="btn btn-success btn-block" name="update">Mettre à jour</button>
            </form>
        </div>
    </div>
    <footer>
        <p>&copy; 2024 Artisanat. Tous droits réservés.</p>
    </footer>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>