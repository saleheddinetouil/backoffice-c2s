<?php
/*
return [
    'host' => 'DESKTOP-8DCSF5J\BASSEM', 
    'database' => 'SO',
    'username' => 'super',  
    'password' => 'bassembassem' 
];
*/
// config/database.php
function dbConnect() {
    $serverName = "DESKTOP-8DCSF5J\BASSEM"; // Remplacez par l'adresse de votre serveur SQL Server si nécessaire
    $database = "GestionVente";
    $uid = "super"; // Remplacez par votre nom d'utilisateur
    $pwd = "bassembassem"; // Remplacez par votre mot de passe

    try {
        $conn = new PDO("sqlsrv:server = $serverName; Database = $database", $uid, $pwd);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        die("Erreur de connexion à SQL Server : " . $e->getMessage());
    }
}