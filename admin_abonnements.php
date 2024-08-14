<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'administrateur') {
    header("Location: login.php");
    exit();
}

// Gérer la soumission du formulaire pour ajouter un nouvel abonnement
if (isset($_POST['add_abonnements'])) {
    $nom = $_POST['nom'];
    $duree = $_POST['duree'];
    $prix = $_POST['prix'];

    $sql = "INSERT INTO abonnements (nom, duree, prix) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nom, $duree, $prix);

    if ($stmt->execute()) {
        echo "Abonnement ajouté avec succès.";
    } else {
        echo "Erreur : " . $stmt->error;
    }

    $stmt->close();
}

// Récupérer tous les abonnements et leurs utilisateurs
$sql = "SELECT Abonnements.*, Utilisateurs.nom AS user_nom, Utilisateurs.prenom AS user_prenom 
        FROM Abonnements 
        LEFT JOIN User_Abonnements ON Abonnements.id_Abonnements = User_Abonnements.id_abonnement 
        LEFT JOIN Utilisateurs ON User_Abonnements.id_utilisateur = Utilisateurs.id_Utilisateurs";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gérer les Abonnements</title>
    <link rel="stylesheet" href="admin_abonnement.css">
</head>

<body>
    <header>
        <h1>Gérer les Abonnements</h1>
        <nav>
            <ul>
                <li><a href="admin_dashboard.php">Retour au Tableau de Bord</a></li>
                <li><a href="admin_view_abonnements.php">Voir Abonnements des Artisans</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <h2>Ajouter un Nouvel Abonnement</h2>
        <form action="admin_abonnements.php" method="POST">
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" required>
            <label for="duree">Durée (jours):</label>
            <input type="number" id="duree" name="duree" required>
            <label for="prix">Tarif:</label>
            <input type="text" id="prix" name="prix" required>
            <button type="submit" name="add_abonnements">Ajouter Abonnement</button>
        </form>
        <h2>Liste des Abonnements</h2>
        <?php if ($result->num_rows > 0) : ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nom de l'Abonnement</th>
                    <th>Durée (jours)</th>
                    <th>prix</th>
                    <th>Nom de l'Utilisateur</th>
                    <th>Date de Début</th>
                    <th>Date de Fin</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id_Abonnements']); ?></td>
                        <td><?php echo htmlspecialchars($row['nom']); ?></td>
                        <td><?php echo htmlspecialchars($row['duree']); ?></td>
                        <td><?php echo htmlspecialchars($row['prix']); ?></td>
                        <td><?php echo htmlspecialchars($row['user_nom']) . " " . htmlspecialchars($row['user_prenom']); ?></td>
                        <td><?php echo htmlspecialchars($row['date_debut']); ?></td>
                        <td><?php echo htmlspecialchars($row['date_fin']); ?></td>
                        <td>
                            <a href="admin_edit_abonnement.php?id=<?php echo $row['id_Abonnements']; ?>">Modifier</a> |
                            <a href="admin_delete_abonnement.php?id=<?php echo $row['id_Abonnements']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet abonnement ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else : ?>
            <p>Aucun abonnement trouvé.</p>
        <?php endif; ?>
    </div>
    <footer>
        <p>&copy; 2024 Artisanat. Tous droits réservés.</p>
    </footer>
</body>

</html>
<?php
$conn->close();
?>