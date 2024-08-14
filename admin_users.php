<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'administrateur') {
    header("Location: login.php");
    exit();
}

// Récupérer les utilisateurs
$sql = "SELECT * FROM Utilisateurs";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gérer les Utilisateurs</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="admin_use.css">
</head>

<body>
    <header class="bg-primary text-white p-2">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h3">Gérer les Utilisateurs</h1>
            <nav class="navbar navbar-expand-lg navbar-dark p-0">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="admin_dashboard.php">Retour au Tableau de Bord</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>
    <main class="container my-4">
        <h2 class="text-center">Liste des Utilisateurs</h2>
        <?php if ($result->num_rows > 0) : ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Date d'Inscription</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id_Utilisateurs']); ?></td>
                                <td><?php echo htmlspecialchars($row['nom']); ?></td>
                                <td><?php echo htmlspecialchars($row['prenom']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['role']); ?></td>
                                <td><?php echo htmlspecialchars($row['date_inscription']); ?></td>
                                <td>
                                    <a href="admin_edit_user.php?id=<?php echo $row['id_Utilisateurs']; ?>" class="btn btn-sm btn-warning">Modifier</a>
                                    <a href="admin_delete_user.php?id=<?php echo $row['id_Utilisateurs']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <p class="text-center">Aucun utilisateur trouvé.</p>
        <?php endif; ?>
    </main>
    <footer class="bg-dark text-white text-center py-2">
        <div class="container">
            <p class="m-0">&copy; 2024 Artisanat express. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
<?php
$conn->close();
?>