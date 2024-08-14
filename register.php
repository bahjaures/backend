<?php
session_start();
include 'includes/db.php';

$error_message = '';

if (isset($_POST['register'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    $contact = $_POST['contact'];
    $role = $_POST['role'];
    $profil_type = ($role === 'artisan') ? $_POST['profil_type'] : '';

    $sql = "INSERT INTO Utilisateurs (nom, prenom, email, mot_de_passe, contact, role, profil_type) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $nom, $prenom, $email, $mot_de_passe, $contact, $role, $profil_type);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['user_role'] = $role;
        $_SESSION['user_profil_type'] = $profil_type;

        if ($role === 'artisan') {
            if ($profil_type === 'service') {
                header("Location: artisan_service_profile.php");
            } else if ($profil_type === 'vente') {
                header("Location: artisan_vente_profile.php");
            }
        } else if ($role === 'client') {
            header("Location: client_profile.php");
        } else if ($role === 'livreur') {
            header("Location: livreur_profile.php");
        }
        exit();
    } else {
        $error_message = "Erreur: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: white;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.4);
        }
        h2{
            color: green;
        }
        button{
            background-color: green;
        }
        .form-group label {
            font-weight: bold;
        }

        .form-group .fas {
            margin-right: 8px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <header class="bg-dark text-white p-3">
        <div class="container">
            <h1 class="text-center">Inscription</h1>
            <nav class="d-flex justify-content-center">
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link text-white" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="login.php">Connexion</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="d-flex align-items-center min-vh-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h2 class="text-center mb-4">Inscription</h2>
                            <?php if (!empty($error_message)) : ?>
                                <div class="alert alert-danger" role="alert">
                                    <?php echo $error_message; ?>
                                </div>
                            <?php endif; ?>
                            <form id="registerForm" action="register.php" method="post">
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="nom"><i class="fas fa-user"></i> Nom</label>
                                        <input type="text" id="nom" name="nom" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="prenom"><i class="fas fa-user"></i> Prénom</label>
                                        <input type="text" id="prenom" name="prenom" class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                                    <input type="email" id="email" name="email" class="form-control" required>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="mot_de_passe"><i class="fas fa-lock"></i> Mot de passe</label>
                                        <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" required>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label for="contact"><i class="fas fa-phone"></i> Contact</label>
                                        <input type="text" id="contact" name="contact" class="form-control" required>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="role"><i class="fas fa-briefcase"></i> Rôle</label>
                                    <select id="role" name="role" class="form-control" required>
                                        <option value="">Sélectionnez un rôle</option>
                                        <option value="artisan">Artisan</option>
                                        <option value="client">Client</option>
                                        <option value="livreur">Livreur</option>
                                    </select>
                                </div>
                                <div class="form-group artisan-only" style="display: none;">
                                    <label for="profil_type"><i class="fas fa-info-circle"></i> Type de profil</label>
                                    <select id="profil_type" name="profil_type" class="form-control">
                                        <option value="">Sélectionnez un type de profil</option>
                                        <option value="service">Proposer un service</option>
                                        <option value="vente">Vendre des articles</option>
                                    </select>
                                </div>
                                <button type="submit" name="register" class="btn btn-primary btn-block">S'inscrire</button>
                            </form>
                            <p class="text-center mt-3"><a href="login.php">Déjà inscrit? Connectez-vous ici.</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer class="bg-dark text-white text-center p-3">
        <p>&copy; 2024 Artisanat express. Tous droits réservés.</p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#role').change(function() {
                var role = $(this).val();
                if (role === 'artisan') {
                    $('.artisan-only').show();
                } else {
                    $('.artisan-only').hide();
                }
            });
        });
    </script>
</body>

</html>