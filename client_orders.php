<?php
session_start();
include 'includes/db.php';

if ($_SESSION['role'] != 'client') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Vérifier que l'utilisateur existe dans la table Clients
$sql_check_client = "SELECT id_Clients FROM Clients WHERE id_utilisateur = ?";
$stmt_check_client = $conn->prepare($sql_check_client);
$stmt_check_client->bind_param("i", $user_id);
$stmt_check_client->execute();
$stmt_check_client->bind_result($id_client);
$stmt_check_client->fetch();
$stmt_check_client->close();

if (!$id_client) {
    echo "Erreur : Utilisateur non trouvé dans les clients.";
    exit();
}

// Passer une commande
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order'])) {
    $article_id = $_POST['article_id'];
    $quantite = $_POST['quantite'];

    $sql = "INSERT INTO Commandes (id_client, id_Articles, quantite, statut) VALUES (?, ?, ?, 'en attente')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $id_client, $article_id, $quantite);

    if ($stmt->execute()) {
        echo "Commande passée avec succès.";
    } else {
        echo "Erreur: " . $stmt->error;
    }

    $stmt->close();
    header("Location: client_orders.php");
    exit();
}

// Annuler une commande
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel'])) {
    $commande_id = $_POST['commande_id'];

    $sql_cancel = "DELETE FROM Commandes WHERE id_Commandes = ? AND id_client = ?";
    $stmt_cancel = $conn->prepare($sql_cancel);
    $stmt_cancel->bind_param("ii", $commande_id, $id_client);

    if ($stmt_cancel->execute()) {
        echo "Commande annulée avec succès.";
    } else {
        echo "Erreur: " . $stmt_cancel->error;
    }

    $stmt_cancel->close();
    header("Location: client_orders.php");
    exit();
}

// Récupérer les articles disponibles
$sql_get_articles = "SELECT id_Articles, nom, prix FROM Articles";
$result_articles = $conn->query($sql_get_articles);

// Récupérer les commandes en cours
$sql_get_orders = "SELECT Commandes.id_Commandes, Articles.nom, Articles.prix, Articles.photo, Artisans.nom AS artisan_nom, Commandes.quantite, Commandes.statut 
                   FROM Commandes 
                   JOIN Articles ON Commandes.id_Articles = Articles.id_Articles 
                   JOIN Artisans ON Articles.id_artisans = Artisans.id_Artisans
                   WHERE Commandes.id_client = ?";
$stmt_get_orders = $conn->prepare($sql_get_orders);
$stmt_get_orders->bind_param("i", $id_client);
$stmt_get_orders->execute();
$result_orders = $stmt_get_orders->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Passer et Gérer Commandes</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="client_orders.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="#">commandes</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="recherche_artisans.php">Recherche Artisans</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="recherche_articles.php">Recherche Articles</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Déconnexion</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>

    <style>
        body {
            background-color: white;
            font-family: Arial, sans-serif;
            color: #333;
        }

        h2 {
            color: black;
            margin-bottom: 20px;
        }

        form {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        select,
        input[type="number"],
        button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            background-color: #4caf50;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: black;
            color: white;
        }

        td img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: black;
            color: white;
            position: fixed;
            width: 100%;
            bottom: 0;
        }

        .container {
            margin-top: 50px;
        }

        .btn-cancel {
            background-color: green;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-cancel:hover {
            background-color: #e53935;
        }

        .order-table {
            margin-top: 20px;
        }

        .order-table th,
        .order-table td {
            text-align: center;
        }

        .order-table img {
            max-width: 70px;
            border-radius: 5px;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #ff7f00;
            text-decoration: none;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
    </head>

    <body>
        <div class="container">
            <h2>Passer une Commande</h2>
            <form action="client_orders.php" method="post">
                <label for="article_id">Article:</label>
                <select id="article_id" name="article_id" required>
                    <?php while ($row = $result_articles->fetch_assoc()) { ?>
                        <option value="<?php echo htmlspecialchars($row['id_Articles']); ?>"><?php echo htmlspecialchars($row['nom']) . " - " . htmlspecialchars($row['prix']) . " cfa"; ?></option>
                    <?php } ?>
                </select><br>

                <label for="quantite">Quantité:</label>
                <input type="number" id="quantite" name="quantite" min="1" required><br>

                <button type="submit" name="order">Commander</button>
            </form>

            <h2>Commandes en Cours</h2>
            <?php if ($result_orders->num_rows > 0) { ?>
                <table class="order-table table table-striped">
                    <thead>
                        <tr>
                            <th>Article</th>
                            <th>Photo</th>
                            <th>Prix</th>
                            <th>Quantité</th>
                            <th>Artisan</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result_orders->fetch_assoc()) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['nom']); ?></td>
                                <td>
                                    <?php if (!empty($row['photo'])) : ?>
                                        <img src="<?php echo htmlspecialchars($row['photo']); ?>" alt="Photo de l'article">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['prix']); ?> cfa</td>
                                <td><?php echo htmlspecialchars($row['quantite']); ?></td>
                                <td><?php echo htmlspecialchars($row['artisan_nom']); ?></td>
                                <td><?php echo htmlspecialchars($row['statut']); ?></td>
                                <td>
                                    <form action="client_orders.php" method="post" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette commande ?');">
                                        <input type="hidden" name="commande_id" value="<?php echo $row['id_Commandes']; ?>">
                                        <button type="submit" name="cancel" class="btn-cancel">Annuler</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } else { ?>
                <p>Aucune commande en cours.</p>
            <?php } ?>

            <p><a href="client_profile.php" class="back-link"><i class="fas fa-arrow-left"></i> Retour au Profil</a></p>
        </div>

        <footer>
            &copy; 2024 Artisanat Express. Tous droits réservés.
        </footer>
    </body>

</html>