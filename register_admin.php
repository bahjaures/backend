<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Inscription Administrateur</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="register_admin.css">
</head>

<body>
    <header class="bg-dark text-white p-3">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h3">Inscription Administrateur</h1>
            <nav>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link text-white" href="index.php">Accueil</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="login.php">Connexion</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container mt-5">
        <h2 class="text-center">Créer un compte Administrateur</h2>
        <form action="register_admin.php" method="post" class="needs-validation" novalidate>
            <div class="form-group">
                <label for="nom"> <i class="fas fa-user"></i> Nom:</label>
                <input type="text" class="form-control" id="nom" name="nom" required>
                <div class="invalid-feedback">Veuillez entrer votre nom.</div>
            </div>
            <div class="form-group">
                <label for="prenom"> <i class="fas fa-user"></i> Prénom:</label>
                <input type="text" class="form-control" id="prenom" name="prenom" required>
                <div class="invalid-feedback">Veuillez entrer votre prénom.</div>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
                <div class="invalid-feedback">Veuillez entrer une adresse email valide.</div>
            </div>
            <div class="form-group">
                <label for="mot_de_passe">Mot de passe:</label>
                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
                <div class="invalid-feedback">Veuillez entrer un mot de passe.</div>
            </div>
            <button type="submit" name="register_admin" class="btn btn-primary btn-block">S'inscrire</button>
        </form>
    </main>
    <footer class="bg-dark text-white text-center p-3 mt-5">
        <p>&copy; 2024 Artisanat. Tous droits réservés.</p>
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>

</html>