<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'administrateur') {
    header("Location: login.php");
    exit();
}

$user_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $sql = "UPDATE Utilisateurs SET nom = ?, prenom = ?, email = ?, role = ? WHERE id_Utilisateurs = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nom, $prenom, $email, $role, $user_id);

    if ($stmt->execute()) {
        header("Location: admin_users.php");
        exit();
    } else {
        echo "Erreur : " . $stmt->error;
    }
    $stmt->close();
} else {
    $sql = "SELECT * FROM Utilisateurs WHERE id_Utilisateurs = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier Utilisateur</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="admin_edit_user.css">
</head>

<body>
    <header class="header-custom">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h4">Modifier Utilisateur</h1>
            <nav class="navbar navbar-expand-lg navbar-dark p-0">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="admin_users.php">Retour à la Gestion des Utilisateurs</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
    </header>
    <main class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form action="" method="POST" class="bg-light p-4 rounded shadow">
                    <div class="form-group">
                        <label for="nom">Nom :</label>
                        <input type="text" name="nom" id="nom" class="form-control" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="prenom">Prénom :</label>
                        <input type="text" name="prenom" id="prenom" class="form-control" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email :</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="role">Rôle :</label>
                        <select name="role" id="role" class="form-control" required>
                            <option value="client" <?php if ($user['role'] == 'client') echo 'selected'; ?>>Client</option>
                            <option value="artisan" <?php if ($user['role'] == 'artisan') echo 'selected'; ?>>Artisan</option>
                            <option value="administrateur" <?php if ($user['role'] == 'administrateur') echo 'selected'; ?>>Administrateur</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Modifier</button>
                </form>
            </div>
        </div>
    </main>
    <footer class="footer-custom">
        <div class="container text-center">
            <p>&copy; 2024 Artisanat. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>