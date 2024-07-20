<?php
// index.php

// Inclusion du fichier de configuration de la base de données
require_once 'config/database.php';

// Connexion à la base de données
$conn = dbConnect();

// Démarrage de la session
session_start();

// Vérification de l'authentification
$isAuthenticated = isset($_SESSION['user_id']);

// Capturer chaque entré au siteweb 
    $allowedPages = ['auth'];
// Gestion du routage
if (isset($_GET['page'])) {
    $page = $_GET['page']; 
    $action = isset($_GET['action']) ? $_GET['action'] : 'index'; 
    if($isAuthenticated){
    // Selectionner le role de l'Utilisateur
    $sqlUserRole = "SELECT * FROM Users WHERE user_id = '".$_SESSION['user_id']."'";
    $queryUserRole = $conn->prepare($sqlUserRole);
    $queryUserRole->execute();
    $user = $queryUserRole->fetch();
    
    if ($user['role'] == "Administrateur"){
    // Liste des pages autorisées
    $allowedPages = ['profile','charts','clients', 'articles', 'gammes', 'familles', 'zones', 'circuits', 'commandes', 'auth', 'factures','utilisateurs'];
    }
    else{
        // Liste des pages autorisées
     
        $allowedPages = [ 'commandes', 'factures','auth','profile'];
    }
}
    // Vérification si la page demandée est autorisée
    if (in_array($page, $allowedPages)) { 
        // Construction du chemin du fichier à inclure
        $includeFile = $page . '/' . $action . '.php'; 

        // Vérification si le fichier existe
        if (file_exists($includeFile)) { 
            include $includeFile; // Inclusion du fichier
        } else {
            // Gestion de l'erreur 404 si le fichier n'existe pas
            notFound(); 
        }
    } else {
        // Gestion de l'erreur 404 si la page n'est pas autorisée
        notFound(); 
    }

} else {
    // Redirection vers la page de connexion si non authentifié, ou vers le tableau de bord si authentifié
    if (!$isAuthenticated) {
        header('Location: ?page=auth&action=login');
        exit();
    } else {
        include 'dashboard.php'; 
    }
}

// Fonction pour gérer les erreurs 404
function notFound() {
    header("Location: /");

}

?>