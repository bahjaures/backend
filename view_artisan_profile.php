<?php
session_start();
include 'includes/db.php';

if ($_SESSION['role'] != 'client') {
    header("Location: login.php");
    exit();
}

$artisan_id = $_GET['id'] ?? '';

if (empty($artisan_id)) {
    echo "ID de l'artisan manquant.";
    exit();
}

$sql = "SELECT * FROM Artisans WHERE id_utilisateur = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $artisan_id);
$stmt->execute();
$result = $stmt->get_result();
$artisan = $result->fetch_assoc();

if (!$artisan) {
    echo "Profil artisan non trouvé.";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Profil Artisan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="view_artisan.css">
</head>

<body>
    <header class="bg-dark text-white py-3">
        <div class="container">
            <h1>Artisanat</h1>
           
        </div>
    </header>

    <div class="container mt-5">
        <h2 class="mb-4">Profil Artisan</h2>
        <div class="card">
            <div class="card-body">
                <?php if (!empty($artisan['photo'])) : ?>
                    <img src="<?php echo htmlspecialchars($artisan['photo']); ?>" alt="Photo de profil" class="rounded-circle mb-3" style="width:150px;height:150px;">
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($artisan['nom']); ?></h3>
                <p><strong>Spécialité:</strong> <?php echo htmlspecialchars($artisan['specialite']); ?></p>
                <p><strong>Expérience:</strong> <?php echo htmlspecialchars($artisan['experience']); ?></p>
                <p><strong>Localisation:</strong> <?php echo htmlspecialchars($artisan['localite']); ?></p>
                <p><strong>Quartier:</strong> <?php echo htmlspecialchars($artisan['quartier']); ?></p>
                <p><strong>Contact:</strong> <?php echo htmlspecialchars($artisan['contact']); ?></p>
                <p><strong>Latitude:</strong> <?php echo htmlspecialchars($artisan['latitude']); ?></p>
                <p><strong>Longitude:</strong> <?php echo htmlspecialchars($artisan['longitude']); ?></p>

                <a href="carte.html" class="btn btn-primary mt-3">carte</a>

                <a href="send_message.php?to=<?php echo $artisan_id; ?>" class="btn btn-primary mt-3">Envoyer un message</a>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white py-3 mt-5">
        <div class="container text-center">
            <p>&copy; 2024 Artisanat express. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>