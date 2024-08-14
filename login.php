<?php
session_start();
include 'includes/db.php';

// Activer les erreurs PHP pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $sql = "SELECT * FROM utilisateurs WHERE nom = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('prepare() failed: ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("s", $nom);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if (password_verify($mot_de_passe, $user['mot_de_passe'])) {
            $_SESSION['user_id'] = $user['id_Utilisateurs'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['profil_type'] = $user['profil_type'];

            if ($user['role'] == 'administrateur') {
                header("Location: admin_dashboard.php");
            } else if ($user['role'] == 'artisan') {
                if ($user['profil_type'] == 'service') {
                    header("Location: artisan_service_profile.php");
                } else if ($user['profil_type'] == 'vente') {
                    header("Location: artisan_vente_profile.php");
                }
            } else if ($user['role'] == 'client') {
                header("Location: client_profile.php");
            } else if ($user['role'] == 'livreur') { // Added this case
                header("Location: livreur_profile.php");
            } else {
                echo "Rôle non reconnu.";
            }
            exit();
        } else {
            $error_message = "Mot de passe incorrect.";
        }
    } else {
        $error_message = "Utilisateur non trouvé.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <header class="bg-dark text-white p-3">
        <div class="container">
            <h1 class="text-center">Connexion</h1>
            <nav class="d-flex justify-content-center">
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link text-white" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="register.php">Inscription</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="d-flex align-items-center min-vh-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center">Connexion</h2>
                            <?php if (isset($error_message)) : ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $error_message; ?>
                                </div>
                            <?php endif; ?>
                            <form action="login.php" method="POST">
                                <div class="form-group">
                                    <label for="nom"> <i class="fas fa-user"></i>Nom :</label>
                                    <input type="text" name="nom" id="nom" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="mot_de_passe"> <i class="fas fa-lock"></i>Mot de passe :</label>
                                    <input type="password" name="mot_de_passe" id="mot_de_passe" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">Connexion</button>
                            </form>
                            <p class="text-center mt-3"><a href="forgot_password.php">Mot de passe oublié ?</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer class="bg-dark text-white text-center p-3">
        <p>&copy; 2024 Artisanat. Tous droits réservés.</p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>