<?php
// gammes/create.php

// Inclusion du fichier de configuration de la base de données
require_once 'config/database.php';

// Connexion à la base de données
$conn = dbConnect();

// Gestion de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $gamme_code = $_POST['gamme_code'];
    $Libelle = $_POST['Libelle'];

    // Validation des données (à ajouter)
    // ...

    // Préparation de la requête SQL d'insertion
    $sql = "INSERT INTO Gammes (gamme_code, Libelle) 
            VALUES (:gamme_code, :Libelle)";
    $stmt = $conn->prepare($sql);

    // Exécution de la requête d'insertion
    try {
        $stmt->execute([
            'gamme_code' => $gamme_code,
            'Libelle' => $Libelle
        ]);

        // Redirection vers la page de liste des gammes
        header("Location: ?page=gammes");
        exit();
    } catch (PDOException $e) {
        // Affichage d'un message d'erreur en cas d'échec de l'insertion
        echo "Erreur lors de la création de la gamme : " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Créer une nouvelle gamme</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">                <div class="flex">
        <!-- Barre latérale -->
        <?php include 'components/sidebar.php'; ?>

        <div class="flex-1 p-4 ml-64">
            <!-- Barre de navigation -->
            <?php //include 'components/navbar.php'; ?>
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-4 text-center text-gray-800">Créer une nouvelle gamme</h1>

        <!-- Formulaire de création de gamme -->
        <form method="POST" action="?page=gammes&action=create" class="bg-white p-6 rounded-lg shadow-md">

            <!-- Champ Code Gamme -->
            <div class="mb-4">
                <label for="gamme_code" class="block text-gray-700 text-sm font-bold mb-2">Code Gamme:</label>
                <input type="text" id="gamme_code" name="gamme_code" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <!-- Champ Libellé -->
            <div class="mb-4">
                <label for="Libelle" class="block text-gray-700 text-sm font-bold mb-2">Libellé:</label>
                <input type="text" id="Libelle" name="Libelle" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <!-- Bouton de création -->
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Créer
            </button>
        </form>
    </div>
</div></div></body>
</html>