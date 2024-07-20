<?php
// circuits/delete.php

// Inclusion du fichier de configuration de la base de données
require_once 'config/database.php';

// Connexion à la base de données
$conn = dbConnect();

// Vérification si l'ID du circuit est présent dans la requête GET
if (isset($_GET['id'])) {
    $circuit_id = $_GET['id'];

    // Préparation de la requête SQL de suppression
    $sql = "DELETE FROM Circuits WHERE circuit_id = :circuit_id";
    $stmt = $conn->prepare($sql);

    // Exécution de la requête de suppression
    try {
        $stmt->execute(['circuit_id' => $circuit_id]);

        // Redirection vers la page de liste des circuits après la suppression
        header("Location: ?page=circuits");
        exit();
    } catch (PDOException $e) {
        // Affichage d'un message d'erreur en cas d'échec de la suppression
        echo "Erreur lors de la suppression du circuit : " . $e->getMessage();
    }
} else {
    // Affichage d'un message d'erreur si l'ID du circuit est manquant
    echo "ID du circuit manquant.";
}

?>