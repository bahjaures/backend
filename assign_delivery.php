<?php
session_start();
include 'includes/db.php';

if ($_SESSION['role'] != 'artisan') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['id_commande']) && isset($_POST['id_livreur'])) {
        $id_commande = $_POST['id_commande'];
        $id_livreur = $_POST['id_livreur'];

        // Update the delivery assignment in the database
        $sql = "UPDATE Commandes SET id_livreur = ?, statut = 'Assigné' WHERE id_Commandes = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ii", $id_livreur, $id_commande);

            if ($stmt->execute()) {
                // Insert into the Livraisons table
                $sql2 = "INSERT INTO Livraisons (id_Commandes, id_Livreur) VALUES (?, ?)";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bind_param("ii", $id_commande, $id_livreur);
                $stmt2->execute();
                $stmt2->close();

                $_SESSION['success'] = "Livreur assigné avec succès.";
            } else {
                $_SESSION['error'] = "Erreur lors de l'exécution de la requête.";
            }

            $stmt->close();
        } else {
            $_SESSION['error'] = "Erreur lors de la préparation de la requête.";
        }
    } else {
        $_SESSION['error'] = "Données de formulaire invalides.";
    }

    $conn->close();
    header("Location: artisan_vente_profile.php");
    exit();
} else {
    header("Location: artisan_vente_profile.php");
    exit();
}
