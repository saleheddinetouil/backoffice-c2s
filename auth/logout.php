<?php

// auth/logout.php

// Connexion à la base de données
require_once 'config/database.php';

session_start();

// Suppression des variables de session et de la session
$_SESSION = array();
session_destroy();

// Redirection vers la page d'accueil
header('Location: /');
exit();
?>