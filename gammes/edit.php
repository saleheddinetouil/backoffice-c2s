<?php
// gammes/edit.php

// Inclusion du fichier de configuration de la base de données
require_once 'config/database.php';

// Connexion à la base de données
$conn = dbConnect();

// Récupération de la gamme à modifier
if (isset($_GET['id'])) {
    $gamme_code = $_GET['id'];
    $sql = "SELECT * FROM Gammes WHERE gamme_code = :gamme_code";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['gamme_code' => $gamme_code]);
    $gamme = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification si la gamme existe
    if (!$gamme) {
        echo "Gamme non trouvée.";
        exit();
    }
} else {
    echo "ID de la gamme manquant.";
    exit();
}

// Gestion de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $Libelle = $_POST['Libelle'];

    // Validation des données (à ajouter)
    // ...

    // Préparation de la requête SQL de mise à jour
    $sql = "UPDATE Gammes 
            SET Libelle = :Libelle 
            WHERE gamme_code = :gamme_code";
    $stmt = $conn->prepare($sql);

    // Exécution de la requête de mise à jour
    try {
        $stmt->execute([
            'Libelle' => $Libelle,
            'gamme_code' => $gamme_code
        ]);

        // Redirection vers la page de liste des gammes
        header("Location: ?page=gammes");
        exit();
    } catch (PDOException $e) {
        // Affichage d'un message d'erreur en cas d'échec de la mise à jour
        echo "Erreur lors de la modification de la gamme : " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifier une gamme</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">                <div class="flex">
        <!-- Barre latérale -->
        <?php include 'components/sidebar.php'; ?>

        <div class="flex-1 p-4 ml-64">
            <!-- Barre de navigation -->
            <?php //include 'components/navbar.php'; ?>
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-4 text-center text-gray-800">Modifier la gamme : <?= $gamme['gamme_code'] ?></h1>

        <!-- Formulaire de modification de gamme -->
        <form method="POST" action="?page=gammes&action=edit&id=<?= $gamme['gamme_code'] ?>" class="bg-white p-6 rounded-lg shadow-md">

            <!-- Champ Libellé -->
            <div class="mb-4">
                <label for="Libelle" class="block text-gray-700 text-sm font-bold mb-2">Libellé:</label>
                <input type="text" id="Libelle" name="Libelle" value="<?= $gamme['Libelle'] ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <!-- Bouton de modification -->
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Modifier
            </button>
        </form>
    </div>
</div></div></body>
</html>