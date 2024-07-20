<?php
// clients/delete.php

// Inclusion du fichier de configuration de la base de données
require_once 'config/database.php';

// Connexion à la base de données
$conn = dbConnect();

// Vérification si l'ID du client est présent dans la requête GET
if (isset($_GET['id'])) {
    $CODECL = $_GET['id'];

    // Préparation de la requête SQL de suppression
    $sql = "DELETE FROM Clients WHERE CODECL = :CODECL";
    $stmt = $conn->prepare($sql);

    // Exécution de la requête de suppression
    try {
        $stmt->execute(['CODECL' => $CODECL]);

        // Redirection vers la page de liste des clients après la suppression
        header("Location: ?page=clients"); 
        exit();
    } catch (PDOException $e) {
        // Affichage d'un message d'erreur en cas d'échec de la suppression
        echo "Erreur lors de la suppression du client : " . $e->getMessage();
    }
} else {
    // Affichage d'un message d'erreur si l'ID du client est manquant
    echo "ID du client manquant.";
}

?>