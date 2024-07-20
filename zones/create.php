<?php
// zones/create.php

// Inclusion du fichier de configuration de la base de données
require_once 'config/database.php';

// Connexion à la base de données
$conn = dbConnect();

// Gestion de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $Nom_Zone = $_POST['Nom_Zone'];

    // Validation des données (à ajouter)
    // ...

    // Préparation de la requête SQL d'insertion
    $sql = "INSERT INTO Zones (Nom_Zone) 
            VALUES (:Nom_Zone)";
    $stmt = $conn->prepare($sql);

    // Exécution de la requête d'insertion
    try {
        $stmt->execute([
            'Nom_Zone' => $Nom_Zone
        ]);

        // Redirection vers la page de liste des zones
        header("Location: ?page=zones");
        exit();
    } catch (PDOException $e) {
        // Affichage d'un message d'erreur en cas d'échec de l'insertion
        echo "Erreur lors de la création de la zone : " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Créer une nouvelle zone</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">                <div class="flex">
        <!-- Barre latérale -->
        <?php include 'components/sidebar.php'; ?>

        <div class="flex-1 p-4 ml-64">
            <!-- Barre de navigation -->
            <?php //include 'components/navbar.php'; ?>
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-4 text-center text-gray-800">Créer une nouvelle zone</h1>

        <!-- Formulaire de création de zone -->
        <form method="POST" action="?page=zones&action=create" class="bg-white p-6 rounded-lg shadow-md">

            <!-- Champ Nom de la Zone -->
            <div class="mb-4">
                <label for="Nom_Zone" class="block text-gray-700 text-sm font-bold mb-2">Nom de la zone:</label>
                <input type="text" id="Nom_Zone" name="Nom_Zone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <!-- Bouton de création -->
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Créer
            </button>
        </form>
    </div>
</div></div></body>
</html>