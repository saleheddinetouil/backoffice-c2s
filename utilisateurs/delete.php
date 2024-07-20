<?php 
// utilisateurs/delete.php

require_once 'config/database.php'; 

$conn = dbConnect();

if (isset($_GET['id'])) {
    $user_id = $_GET['id']; 

    // Important: Prevent deleting the currently logged-in user! 
    if ($user_id == $_SESSION['user_id']) {
        echo "Vous ne pouvez pas supprimer votre propre compte.";
        exit(); 
    }

    $sql = "DELETE FROM Users WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);

    try {
        $stmt->execute(['user_id' => $user_id]);
        header("Location: ?page=utilisateurs");
        exit();
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression de l'utilisateur : " . $e->getMessage(); 
    }
} else { 
    echo "ID de l'utilisateur manquant.";
}

?>