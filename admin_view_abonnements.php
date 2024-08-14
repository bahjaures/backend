<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'administrateur') {
    header("Location: login.php");
    exit();
}

// Gérer la soumission du formulaire pour mettre à jour un abonnement
if (isset($_POST['update_abonnement'])) {
    $id_user_abonnement = $_POST['id_user_abonnement'];
    $abonnement_id = $_POST['abonnement_id'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];

    $sql_update = "UPDATE User_Abonnements SET id_abonnement = ?, date_debut = ?, date_fin = ? WHERE id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("issi", $abonnement_id, $date_debut, $date_fin, $id_user_abonnement);

    if ($stmt_update->execute()) {
        echo "Abonnement mis à jour avec succès.";
    } else {
        echo "Erreur : " . $stmt_update->error;
    }

    $stmt_update->close();
}

// Récupérer tous les abonnements et leurs utilisateurs
$sql = "SELECT id_User_Abonnements AS id_user_abonnement, Utilisateurs.nom AS user_nom, Utilisateurs.prenom AS user_prenom, Utilisateurs.specialite, Utilisateurs.quartier, Utilisateurs.contact, Abonnements.nom AS abonnement_nom, Abonnements.prix, User_Abonnements.date_debut, User_Abonnements.date_fin 
        FROM User_Abonnements 
        JOIN Utilisateurs ON User_Abonnements.id_utilisateur = Utilisateurs.id_Utilisateurs 
        JOIN Abonnements ON User_Abonnements.id_abonnement = Abonnements.id_Abonnements";
$result = $conn->query($sql);

// Récupérer tous les abonnements pour les options de modification
$sql_abonnements = "SELECT id_Abonnements, nom FROM Abonnements";
$result_abonnements = $conn->query($sql_abonnements);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Voir et Modifier les Abonnements</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <header>
        <h1>Voir et Modifier les Abonnements des Artisans</h1>
        <nav>
            <ul>
                <li><a href="admin_dashboard.php">Retour au Tableau de Bord</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Liste des Abonnements des Artisans</h2>
        <?php if ($result->num_rows > 0) : ?>
            <table>
                <tr>
                    <th>Nom de l'Utilisateur</th>
                    <th>Spécialité</th>
                    <th>Quartier</th>
                    <th>Contact</th>
                    <th>Abonnement</th>
                    <th>Date de Début</th>
                    <th>Date de Fin</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['user_nom']) . " " . htmlspecialchars($row['user_prenom']); ?></td>
                        <td><?php echo htmlspecialchars($row['specialite']); ?></td>
                        <td><?php echo htmlspecialchars($row['quartier']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact']); ?></td>
                        <td><?php echo htmlspecialchars($row['abonnement_nom']); ?></td>
                        <td><?php echo htmlspecialchars($row['date_debut']); ?></td>
                        <td><?php echo htmlspecialchars($row['date_fin']); ?></td>
                        <td>
                            <form action="admin_view_edit_abonnements.php" method="POST">
                                <input type="hidden" name="id_user_abonnement" value="<?php echo $row['id_user_abonnement']; ?>">
                                <select name="abonnement_id">
                                    <?php while ($row_abonnements = $result_abonnements->fetch_assoc()) : ?>
                                        <option value="<?php echo $row_abonnements['id_Abonnements']; ?>" <?php if ($row_abonnements['id_Abonnements'] == $row['id_abonnement']) echo 'selected'; ?>>
                                            <?php echo htmlspecialchars($row_abonnements['nom']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select><br>
                                <label for="date_debut">Date de Début:</label>
                                <input type="date" id="date_debut" name="date_debut" value="<?php echo htmlspecialchars($row['date_debut']); ?>"><br>
                                <label for="date_fin">Date de Fin:</label>
                                <input type="date" id="date_fin" name="date_fin" value="<?php echo htmlspecialchars($row['date_fin']); ?>"><br>
                                <button type="submit" name="update_abonnement">Mettre à Jour</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else : ?>
            <p>Aucun abonnement trouvé.</p>
        <?php endif; ?>
    </main>
    <footer>
        <p>&copy; 2024 Artisanat. Tous droits réservés.</p>
    </footer>
</body>

</html>
<?php
$conn->close();
?>