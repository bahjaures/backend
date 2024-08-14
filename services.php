<?php
session_start();
include 'includes/db.php';

// Récupérer les services et les informations des artisans
$sql = "SELECT 
            Utilisateurs.nom AS artisan_nom, 
            Utilisateurs.prenom AS artisan_prenom,
            Artisans.localite AS artisan_localite,
            Artisans.specialite AS artisan_specialite,
            Services.description AS artisan_description,
            Services.prix AS artisan_prix,
            Artisans.contact AS artisan_contact
        FROM Services
        INNER JOIN Artisans ON Services.id_artisans = Artisans.id_Artisans
        INNER JOIN Utilisateurs ON Artisans.id_utilisateur = Utilisateurs.id_Utilisateurs";

$result = $conn->query($sql);

if ($result === false) {
    echo "Erreur dans la requête : " . $conn->error;
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Services</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css"> <!-- Votre fichier CSS personnalisé -->
</head>

<body>
    <header class="bg-dark text-white p-3">
        <div class="container">
            <h1 class="h3">Services Offerts</h1>
            <nav class="navbar navbar-expand-lg navbar-dark">
                <a class="navbar-brand" href="index.php">Artisanat</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
                        <li class="nav-item"><a class="nav-link" href="login.php">Connexion</a></li>

                    </ul>
                </div>
            </nav>
        </div>
    </header>

    <main class="container my-5">
        <h2 class="mb-4">Liste des Services</h2>
        <?php if ($result->num_rows > 0) : ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nom de l'Artisan</th>
                            <th>Localisation</th>
                            <th>Spécialité</th>
                            <th>Description du Service</th>
                            <th>Tarif</th>
                            <th>Contact</th>
                            <th>message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['artisan_nom']) . " " . htmlspecialchars($row['artisan_prenom']); ?></td>
                                <td><?php echo htmlspecialchars($row['artisan_localite']); ?></td>
                                <td><?php echo htmlspecialchars($row['artisan_specialite']); ?></td>
                                <td><?php echo htmlspecialchars($row['artisan_description']); ?></td>
                                <td><?php echo htmlspecialchars($row['artisan_prix']); ?></td>
                                <td><?php echo htmlspecialchars($row['artisan_contact']); ?></td>
                                <td><button><a href="send_message.php">envoi message</a></button></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <div class="alert alert-info" role="alert">Aucun service disponible pour le moment.</div>
        <?php endif; ?>
    </main>

    <footer class="bg-dark text-white text-center py-3">
        <p>&copy; 2024 Artisanat express. Tous droits réservés.</p>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>

<?php
$conn->close();
?>