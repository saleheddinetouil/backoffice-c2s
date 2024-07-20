<?php
// familles/delete.php

// Inclusion du fichier de configuration de la base de données
require_once 'config/database.php';

// Connexion à la base de données
$conn = dbConnect();

// Vérification si l'ID de la famille est présent dans la requête GET
if (isset($_GET['id'])) {
    $famille_code = $_GET['id'];

    // Préparation de la requête SQL de suppression
    $sql = "DELETE FROM Familles WHERE famille_code = :famille_code";
    $stmt = $conn->prepare($sql);

    // Exécution de la requête de suppression
    try {
        $stmt->execute(['famille_code' => $famille_code]);

        // Redirection vers la page de liste des familles après la suppression
        header("Location: ?page=familles");
        exit();
    } catch (PDOException $e) {
        // Affichage d'un message d'erreur en cas d'échec de la suppression
        echo "Erreur lors de la suppression de la famille : " . $e->getMessage();
    }
} else {
    // Affichage d'un message d'erreur si l'ID de la famille est manquant
    echo "ID de la famille manquant.";
}

?>