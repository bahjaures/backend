<?php
session_start();
include 'includes/db.php';

if ($_SESSION['role'] != 'artisan') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer tous les packs publicitaires et abonnements disponibles
$sql_packs = "SELECT * FROM packspublicitaires";
$result_packs = $conn->query($sql_packs);

$sql_abonnements = "SELECT * FROM abonnements";
$result_abonnements = $conn->query($sql_abonnements);

// Gérer la soumission du formulaire pour s'abonner à un abonnement
if (isset($_POST['subscribe_abonnement'])) {
    $id_abonnement = $_POST['id_abonnement'];
    $date_debut = date('Y-m-d');
    $date_fin = date('Y-m-d', strtotime("+$_POST[duree] days"));

    $sql = "INSERT INTO User_Abonnements (id_utilisateur, id_abonnement, date_debut, date_fin) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $user_id, $id_abonnement, $date_debut, $date_fin);

    if ($stmt->execute()) {
        $message = "Abonnement réussi.";
    } else {
        $message = "Erreur : " . $stmt->error;
    }

    $stmt->close();
}

// Gérer la soumission du formulaire pour s'abonner à un pack publicitaire
if (isset($_POST['subscribe_pack'])) {
    $id_pack = $_POST['id_pack'];
    $duree = $_POST['duree'];
    $date_debut = date('Y-m-d');
    $date_fin = date('Y-m-d', strtotime("+$duree days"));

    $sql = "INSERT INTO User_Packs (id_utilisateur, id_pack, date_debut, date_fin) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiss", $user_id, $id_pack, $date_debut, $date_fin);

    if ($stmt->execute()) {
        $message = "Abonnement au pack publicitaire réussi.";
    } else {
        $message = "Erreur : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Abonnements et Packs Publicitaires</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="abonnements.css">
</head>

<body>
    <header class="bg-dark text-white p-3">
        <div class="container">
            <h1 class="h3">Abonnements et Packs Publicitaires</h1>
            <nav>
                <ul class="nav">
                    <li class="nav-item">
                        <a href="artisan_profile.php" class="nav-link text-white">Retour au Profil</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container mt-5">
        <h2 class="mb-4">Packs Publicitaires Disponibles</h2>
        <?php if ($result_packs->num_rows > 0) : ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nom</th>
                            <th>Durée (jours)</th>
                            <th>Prix</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($pack = $result_packs->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($pack['nom']); ?></td>
                                <td><?php echo htmlspecialchars($pack['duree']); ?></td>
                                <td><?php echo htmlspecialchars($pack['prix']); ?></td>
                                <td>
                                    <form action="" method="POST" class="d-inline">
                                        <input type="hidden" name="id_pack" value="<?php echo $pack['id_PacksPublicitaires']; ?>">
                                        <input type="hidden" id="duree-pack-<?php echo $pack['id_PacksPublicitaires']; ?>" name="duree" value="<?php echo $pack['duree']; ?>">
                                        <button type="submit" name="subscribe_pack" class="btn btn-primary btn-sm">S'abonner</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <p>Aucun pack publicitaire disponible.</p>
        <?php endif; ?>

        <h2 class="mb-4">Abonnements Disponibles</h2>
        <?php if ($result_abonnements->num_rows > 0) : ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nom</th>
                            <th>Durée (jours)</th>
                            <th>Prix</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($abonnement = $result_abonnements->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($abonnement['nom']); ?></td>
                                <td><?php echo htmlspecialchars($abonnement['duree']); ?></td>
                                <td><?php echo htmlspecialchars($abonnement['prix']); ?></td>
                                <td>
                                    <form action="" method="POST" class="d-inline">
                                        <input type="hidden" name="id_abonnement" value="<?php echo $abonnement['id_Abonnements']; ?>">
                                        <input type="hidden" id="duree-abonnement-<?php echo $abonnement['id_Abonnements']; ?>" name="duree" value="<?php echo $abonnement['duree']; ?>">
                                        <button type="submit" name="subscribe_abonnement" class="btn btn-primary btn-sm">S'abonner</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <p>Aucun abonnement disponible.</p>
        <?php endif; ?>

        <?php if (isset($message)) : ?>
            <div class="alert alert-info mt-4"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
    </main>
    <footer class="footer bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <p>&copy; 2024 Artisanat. Tous droits réservés.</p>
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.querySelectorAll('select[id^="id_pack"]').forEach(select => {
            select.addEventListener('change', function() {
                var selectedOption = this.options[this.selectedIndex];
                var duree = selectedOption.getAttribute('data-duree');
                document.querySelector('#duree-pack-' + this.value).value = duree;
            });
        });

        document.querySelectorAll('select[id^="id_abonnement"]').forEach(select => {
            select.addEventListener('change', function() {
                var selectedOption = this.options[this.selectedIndex];
                var duree = selectedOption.getAttribute('data-duree');
                document.querySelector('#duree-abonnement-' + this.value).value = duree;
            });
        });
    </script>
</body>

</html>