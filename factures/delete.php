<?php
// factures/delete.php

require_once 'config/database.php';

$conn = dbConnect(); // Connexion à la base de données

// Vérifier si l'ID de la facture est présent
if (isset($_GET['id'])) {
    $Facture_ID = $_GET['id'];

    // Supprimer la facture
    $sql = "DELETE FROM Factures WHERE Facture_ID = :facture_id";
    $stmt = $conn->prepare($sql);

    try {
        $stmt->execute(['facture_id' => $Facture_ID]);
        header("Location: ?page=factures");
        exit();
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression de la facture : " . $e->getMessage();
    }
} else {
    echo "ID de la facture manquant.";
}