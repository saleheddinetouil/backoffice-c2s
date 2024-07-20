<?php
// zones/delete.php

// Inclusion du fichier de configuration de la base de données
require_once 'config/database.php';

// Connexion à la base de données
$conn = dbConnect();

// Vérification si l'ID de la zone est présent dans la requête GET
if (isset($_GET['id'])) {
    $zone_id = $_GET['id'];

    // Préparation de la requête SQL de suppression
    $sql = "DELETE FROM Zones WHERE zone_id = :zone_id";
    $stmt = $conn->prepare($sql);

    // Exécution de la requête de suppression
    try {
        $stmt->execute(['zone_id' => $zone_id]);

        // Redirection vers la page de liste des zones après la suppression
        header("Location: ?page=zones");
        exit();
    } catch (PDOException $e) {
        // Affichage d'un message d'erreur en cas d'échec de la suppression
        echo "Erreur lors de la suppression de la zone : " . $e->getMessage();
    }
} else {
    // Affichage d'un message d'erreur si l'ID de la zone est manquant
    echo "ID de la zone manquant.";
}

?>