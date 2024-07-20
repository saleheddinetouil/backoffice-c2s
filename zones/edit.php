<?php
// zones/edit.php

// Inclusion du fichier de configuration de la base de données
require_once 'config/database.php';

// Connexion à la base de données
$conn = dbConnect();

// Récupération de la zone à modifier
if (isset($_GET['id'])) {
    $zone_id = $_GET['id'];
    $sql = "SELECT * FROM Zones WHERE zone_id = :zone_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['zone_id' => $zone_id]);
    $zone = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification si la zone existe
    if (!$zone) {
        echo "Zone non trouvée.";
        exit();
    }
} else {
    echo "ID de la zone manquant.";
    exit();
}

// Gestion de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $Nom_Zone = $_POST['Nom_Zone'];

    // Validation des données (à ajouter)
    // ...

    // Préparation de la requête SQL de mise à jour
    $sql = "UPDATE Zones 
            SET Nom_Zone = :Nom_Zone 
            WHERE zone_id = :zone_id";
    $stmt = $conn->prepare($sql);

    // Exécution de la requête de mise à jour
    try {
        $stmt->execute([
            'Nom_Zone' => $Nom_Zone,
            'zone_id' => $zone_id
        ]);

        // Redirection vers la page de liste des zones
        header("Location: ?page=zones");
        exit();
    } catch (PDOException $e) {
        // Affichage d'un message d'erreur en cas d'échec de la mise à jour
        echo "Erreur lors de la modification de la zone : " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifier une zone</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">                <div class="flex">
        <!-- Barre latérale -->
        <?php include 'components/sidebar.php'; ?>

        <div class="flex-1 p-4 ml-64">
            <!-- Barre de navigation -->
            <?php //include 'components/navbar.php'; ?>
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-4 text-center text-gray-800">Modifier la zone : <?= $zone['Nom_Zone'] ?></h1>

        <!-- Formulaire de modification de zone -->
        <form method="POST" action="?page=zones&action=edit&id=<?= $zone['zone_id'] ?>" class="bg-white p-6 rounded-lg shadow-md">

            <!-- Champ Nom de la Zone -->
            <div class="mb-4">
                <label for="Nom_Zone" class="block text-gray-700 text-sm font-bold mb-2">Nom de la zone:</label>
                <input type="text" id="Nom_Zone" name="Nom_Zone" value="<?= $zone['Nom_Zone'] ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <!-- Bouton de modification -->
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Modifier
            </button>
        </form>
    </div>
</div></div></body>
</html>