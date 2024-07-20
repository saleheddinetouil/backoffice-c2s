<?php
// articles/delete.php

// Inclusion du fichier de configuration de la base de données
require_once 'config/database.php';

// Connexion à la base de données
$conn = dbConnect();

// Vérification si l'ID de l'article est présent dans la requête GET
if (isset($_GET['id'])) {
    $Article_Code = $_GET['id'];

    // Préparation de la requête SQL de suppression
    $sql = "DELETE FROM Articles WHERE Article_Code = :Article_Code";
    $stmt = $conn->prepare($sql);

    // Exécution de la requête de suppression
    try {
        $stmt->execute(['Article_Code' => $Article_Code]);

        // Redirection vers la page de liste des articles après la suppression
        header("Location: ?page=articles"); 
        exit();
    } catch (PDOException $e) {
        // Affichage d'un message d'erreur en cas d'échec de la suppression
        echo "Erreur lors de la suppression de l'article : " . $e->getMessage();
    }
} else {
    // Affichage d'un message d'erreur si l'ID de l'article est manquant
    echo "ID de l'article manquant.";
}

?>