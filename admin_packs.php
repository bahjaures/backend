<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'administrateur') {
    header("Location: login.php");
    exit();
}

// Gérer la soumission du formulaire pour ajouter un nouveau pack publicitaire
if (isset($_POST['add_pack'])) {
    $nom = $_POST['nom'];
    $duree = $_POST['duree'];
    $prix = $_POST['prix'];
    $image = $_POST['image'];
    $description = $_POST['description'];


    $sql = "INSERT INTO packspublicitaires (nom, duree, prix, image, description) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nom, $duree, $prix, $image, $description);

    if ($stmt->execute()) {
        echo "Pack publicitaire ajouté avec succès.";
    } else {
        echo "Erreur : " . $stmt->error;
    }

    $stmt->close();
}

// Récupérer tous les packs publicitaires
$sql = "SELECT * FROM packspublicitaires";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Gérer les Packs Publicitaires</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="admin_packs.css">
</head>

<body>
    <header>
        <h1>Gérer les Packs Publicitaires</h1>
        <nav>
            <ul>
                <li><a href="admin_dashboard.php">Retour au Tableau de Bord</a></li>
            </ul>
        </nav>
    </header>
    <main class="container">
        <h2>Ajouter un Nouveau Pack Publicitaire</h2>
        <form action="admin_packs.php" method="POST">
            <label for="nom">Nom:</label>
            <input type="text" id="nom" name="nom" required><br>
            <label for="duree">Durée (jours):</label>
            <input type="number" id="duree" name="duree" required><br>
            <label for="prix">Tarif:</label>
            <input type="text" id="prix" name="prix" required><br>
            <label for="image">image :</label>
            <input type="file" id="image" name="image" class="form-control-file">
            <label for="description">description</label>
            <textarea name="description" id="description"></textarea>
            <button type="submit" name="add_pack">Ajouter Pack Publicitaire</button>
        </form>
        <h2>Liste des Packs Publicitaires</h2>
        <?php if ($result->num_rows > 0) : ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Nom du Pack</th>
                    <th>Durée (jours)</th>
                    <th>Prix</th>
                    <th>image</th>
                    <th>description</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id_PacksPublicitaires']); ?></td>
                        <td><?php echo htmlspecialchars($row['nom']); ?></td>
                        <td><?php echo htmlspecialchars($row['duree']); ?></td>
                        <td><?php echo htmlspecialchars($row['prix']); ?></td>
                        <td><?php echo htmlspecialchars($row['image']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td>
                            <a href="admin_edit_pack.php?id=<?php echo $row['id_PacksPublicitaires']; ?>">Modifier</a> |
                            <a href="admin_delete_pack.php?id=<?php echo $row['id_PacksPublicitaires']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce pack publicitaire ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else : ?>
            <p>Aucun pack publicitaire trouvé.</p>
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