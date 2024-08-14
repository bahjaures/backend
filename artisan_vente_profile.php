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

// Récupérer les informations de l'artisan
$sql = "SELECT Utilisateurs.*, Artisans.* 
        FROM Utilisateurs 
        JOIN Artisans ON Utilisateurs.id_utilisateurs = Artisans.id_utilisateur 
        WHERE Utilisateurs.id_utilisateurs = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$artisan = $result->fetch_assoc();

if (!$artisan) {
    echo "Profil artisan non trouvé.";
    exit();
}

// Récupérer les abonnements de l'artisan
$sql = "SELECT Abonnements.nom, Abonnements.duree, Abonnements.prix, User_Abonnements.date_debut, User_Abonnements.date_fin 
        FROM User_Abonnements 
        JOIN Abonnements ON User_Abonnements.id_abonnement = Abonnements.id_Abonnements 
        WHERE User_Abonnements.id_utilisateur = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$abonnements_result = $stmt->get_result();

// Récupérer les packs publicitaires de l'artisan
$sql = "SELECT Packspublicitaires.nom, Packspublicitaires.duree, Packspublicitaires.prix, User_Packs.date_debut, User_Packs.date_fin 
        FROM User_Packs 
        JOIN Packspublicitaires ON User_Packs.id_pack = Packspublicitaires.id_Packspublicitaires 
        WHERE User_Packs.id_utilisateur = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$packs_result = $stmt->get_result();

// Récupérer les commandes des articles de l'artisan
$sql = "SELECT Commandes.*, Articles.nom AS article_nom, Articles.prix, Clients.nom AS client_nom, Commandes.date_commande, Commandes.statut 
        FROM Commandes
        INNER JOIN Articles ON Commandes.id_Articles = Articles.id_Articles
        INNER JOIN Clients ON Commandes.id_client = Clients.id_Clients
        WHERE Articles.id_artisans = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_artisan);
$stmt->execute();
$commandes_result = $stmt->get_result();

// Récupérer les articles de l'artisan
$sql = "SELECT * FROM Articles WHERE id_artisans = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_artisan);
$stmt->execute();
$articles_result = $stmt->get_result();

// Récupérer les livreurs
$sql = "SELECT id_Livreurs, nom FROM Livreurs";
$stmt = $conn->prepare($sql);
$stmt->execute();
$livreurs_result = $stmt->get_result();
$livreurs = [];
while ($livreur = $livreurs_result->fetch_assoc()) {
    $livreurs[] = $livreur;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Profil Artisan Vente</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="artisan_vente.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f7f8fc;
            color: #333;
        }

        header {
            background: linear-gradient(90deg, black, black);
            color: white;
        }

        h3.card-title {
            color: black;
        }

        .card {
            border: none;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
            transition: transform 0.3s ease;
        }

        .card-img-top:hover {
            transform: scale(1.1);
        }

        .btn-primary {
            background-color: #ff7e5f;
            border: none;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #feb47b;
        }

        footer {
            background-color: #333;
            color: #f7f8fc;
            padding: 20px 0;
        }

        .footer span {
            color: black;
        }
    </style>
</head>

<body>
    <header class="p-3">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="h3"><i class="fas fa-store"></i> Profil Artisan Vente</h1>
            <nav>
                <ul class="nav">
                    <li class="nav-item"><a class="nav-link text-white" href="abonnements.php">Abonnements</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="edit_artisan_vente.php">Modifier le Profil</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="manage_articles.php">Gestion des Articles</a></li>
                    <li class="nav-item"><a class="btn btn-light" href="recherche_livreur.php">Recherche Livraison</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="view_messages.php">Messages Reçus</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="confier_article.php">Confier un Article</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="logout.php">Déconnexion</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container mt-5">
        <div class="card mb-4">
            <div class="card-body text-center">
                <img src="<?php echo $artisan['photo']; ?>" alt="Photo de profil" class="profile-img mb-3 rounded-circle" style="width: 150px;">
                <h3><?php echo htmlspecialchars($artisan['nom']); ?></h3>
                <p class="text-muted"><?php echo isset($artisan['specialite']) ? htmlspecialchars($artisan['specialite']) : 'Non spécifié'; ?></p>
                <p>Adresse: <?php echo isset($artisan['adresse']) ? htmlspecialchars($artisan['adresse']) : 'Non spécifiée'; ?></p>
                <p>Expérience: <?php echo isset($artisan['experience']) ? htmlspecialchars($artisan['experience']) : 'Non spécifiée'; ?></p>
                <p>Localité: <?php echo isset($artisan['localite']) ? htmlspecialchars($artisan['localite']) : 'Non spécifiée'; ?></p>
                <p>Contact: <?php echo htmlspecialchars($artisan['contact']); ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title"><i class="fas fa-id-card"></i> Abonnements</h3>
                        <ul class="list-group">
                            <?php while ($abonnement = $abonnements_result->fetch_assoc()) : ?>
                                <li class="list-group-item">
                                    <p><strong>Nom :</strong> <?php echo htmlspecialchars($abonnement['nom']); ?></p>
                                    <p><strong>Durée :</strong> <?php echo htmlspecialchars($abonnement['duree']); ?> jours</p>
                                    <p><strong>Prix :</strong> <?php echo htmlspecialchars($abonnement['prix']); ?> CFA</p>
                                    <p><strong>Du :</strong> <?php echo htmlspecialchars($abonnement['date_debut']); ?> au <?php echo htmlspecialchars($abonnement['date_fin']); ?></p>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title"><i class="fas fa-bullhorn"></i> Packs Publicitaires</h3>
                        <ul class="list-group">
                            <?php while ($pack = $packs_result->fetch_assoc()) : ?>
                                <li class="list-group-item">
                                    <p><strong>Nom :</strong> <?php echo htmlspecialchars($pack['nom']); ?></p>
                                    <p><strong>Durée :</strong> <?php echo htmlspecialchars($pack['duree']); ?> jours</p>
                                    <p><strong>Prix :</strong> <?php echo htmlspecialchars($pack['prix']); ?> CFA</p>
                                    <p><strong>Du :</strong> <?php echo htmlspecialchars($pack['date_debut']); ?> au <?php echo htmlspecialchars($pack['date_fin']); ?></p>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title"><i class="fas fa-shopping-cart"></i> Produits en Vente</h3>
                        <?php if ($articles_result->num_rows > 0) : ?>
                            <div class="row">
                                <?php while ($article = $articles_result->fetch_assoc()) : ?>
                                    <div class="col-md-4 mb-4">
                                        <div class="card shadow-sm">
                                            <?php if (!empty($article['photo'])) : ?>
                                                <img src="<?php echo htmlspecialchars($article['photo']); ?>" class="card-img-top" alt="Image de l'article">
                                            <?php endif; ?>
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo htmlspecialchars($article['nom']); ?></h5>
                                                <p class="card-text"><?php echo htmlspecialchars($article['description']); ?></p>
                                                <p class="text-muted"><?php echo htmlspecialchars($article['prix']); ?> CFA</p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else : ?>
                            <p>Aucun produit en vente actuellement.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mb-3">
                <div class="card h-100">
                    <div class="card-body">
                        <h3 class="card-title"><i class="fas fa-box"></i> Commandes</h3>
                        <?php if ($commandes_result->num_rows > 0) : ?>
                            <div class="row">
                                <?php while ($commande = $commandes_result->fetch_assoc()) : ?>
                                    <div class="col-md-4 mb-4">
                                        <div class="card shadow-sm">
                                            <div class="card-body">
                                                <p><strong>Nom du Client :</strong> <?php echo htmlspecialchars($commande['client_nom']); ?></p>
                                                <p><strong>Produit :</strong> <?php echo htmlspecialchars($commande['article_nom']); ?></p>
                                                <p><strong>Quantité :</strong> <?php echo htmlspecialchars($commande['quantite']); ?></p>
                                                <p><strong>Prix Total :</strong> <?php echo htmlspecialchars($commande['prix']); ?> CFA</p>
                                                <p><strong>Date de la Commande :</strong> <?php echo htmlspecialchars($commande['date_commande']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else : ?>
                            <p>Aucune commande reçue.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer class="text-center mt-4">
        <div class="container">
            <p>&copy; 2024 Artisanat Express. Tous droits réservés. Développé par <span>Bah Jaures</span>.</p>
        </div>
    </footer>
</body>

</html>