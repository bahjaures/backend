<?php
// Inclure le fichier de connexion à la base de données
include 'includes/db.php';

// Requête de test pour vérifier la connexion
$sql = "SHOW TABLES";
$result = $conn->query($sql);

if ($result) {
    echo "Connexion à la base de données réussie!<br>";
    echo "Tables dans la base de données 'artisanat_db':<br>";

    // Afficher les noms des tables
    while ($row = $result->fetch_array()) {
        echo $row[0] . "<br>";
    }
} else {
    echo "Erreur de connexion à la base de données: " . $conn->error;
}

// Fermer la connexion
$conn->close();
