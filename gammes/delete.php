<?php
// gammes/delete.php

// Inclusion du fichier de configuration de la base de données
require_once 'config/database.php';

// Connexion à la base de données
$conn = dbConnect();

// Vérification si l'ID de la gamme est présent dans la requête GET
if (isset($_GET['id'])) {
    $gamme_code = $_GET['id'];

    // Préparation de la requête SQL de suppression
    $sql = "DELETE FROM Gammes WHERE gamme_code = :gamme_code";
    $stmt = $conn->prepare($sql);

    // Exécution de la requête de suppression
    try {
        $stmt->execute(['gamme_code' => $gamme_code]);

        // Redirection vers la page de liste des gammes après la suppression
        header("Location: ?page=gammes");
        exit();
    } catch (PDOException $e) {
        // Affichage d'un message d'erreur en cas d'échec de la suppression
        echo "Erreur lors de la suppression de la gamme : " . $e->getMessage();
    }
} else {
    // Affichage d'un message d'erreur si l'ID de la gamme est manquant
    echo "ID de la gamme manquant.";
}

?>