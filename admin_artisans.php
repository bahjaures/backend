<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'administrateur') {
    header("Location: login.php");
    exit();
}

// Récupérer les artisans
$sql = "SELECT Artisans.*, Utilisateurs.nom, Utilisateurs.prenom FROM Artisans INNER JOIN Utilisateurs ON Artisans.id_utilisateur = Utilisateurs.id_Utilisateurs";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gérer les Artisans</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <header>
        <h1>Gérer les Artisans</h1>
        <nav>
            <ul>
                <li><a href="admin_dashboard.php">Retour au Tableau de Bord</a></li>
                <li><a href="logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h2>Liste des Artisans</h2>
        <?php if ($result->num_rows > 0) : ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Spécialité</th>
                    <th>Localisation</th>
                    <th>Contact</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id_Artisans']); ?></td>
                        <td><?php echo htmlspecialchars($row['nom']); ?></td>
                        <td><?php echo htmlspecialchars($row['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($row['specialite']); ?></td>
                        <td><?php echo htmlspecialchars($row['localisation']); ?></td>
                        <td><?php echo htmlspecialchars($row['contact']); ?></td>
                        <td>
                            <a href="admin_edit_artisan.php?id=<?php echo $row['id_Artisans']; ?>">Modifier</a> |
                            <a href="admin_delete_artisan.php?id=<?php echo $row['id_Artisans']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet artisan ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else : ?>
            <p>Aucun artisan trouvé.</p>
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