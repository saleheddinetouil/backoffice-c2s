<?php
// commandes/delete.php

// Inclusion du fichier de configuration de la base de données
require_once 'config/database.php';

// Connexion à la base de données
$conn = dbConnect();

// Vérification si l'ID de la commande est présent dans la requête GET
if (isset($_GET['id'])) {
    $Commande_ID = $_GET['id'];

    // Préparation de la requête SQL de suppression
    $sql = "DELETE FROM Commande_Entête WHERE Commande_ID = :Commande_ID";
    $stmt = $conn->prepare($sql);

    // Exécution de la requête de suppression
    try {
        $stmt->execute(['Commande_ID' => $Commande_ID]);

        // Redirection vers la page de liste des commandes après la suppression
        header("Location: ?page=commandes");
        exit();
    } catch (PDOException $e) {
        // Affichage d'un message d'erreur en cas d'échec de la suppression
        echo "Erreur lors de la suppression de la commande : " . $e->getMessage();
    }
} else {
    // Affichage d'un message d'erreur si l'ID de la commande est manquant
    echo "ID de la commande manquant.";
}

?>