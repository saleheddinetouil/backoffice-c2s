<?php
// articles/edit.php

// Inclusion du fichier de configuration de la base de données
require_once 'config/database.php';

// Connexion à la base de données
$conn = dbConnect();

// Récupération de l'article à modifier
if (isset($_GET['id'])) {
    $Article_Code = $_GET['id'];
    $sql = "SELECT * FROM Articles WHERE Article_Code = :Article_Code";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['Article_Code' => $Article_Code]);
    $article = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification si l'article existe
    if (!$article) {
        echo "Article non trouvé.";
        exit();
    }
} else {
    echo "ID de l'article manquant.";
    exit();
}

// Gestion de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $Designation = $_POST['Designation'];
    $famille_code = $_POST['famille_code'];
    $gamme_code = $_POST['gamme_code'];
    $Prix_Unitaire = $_POST['Prix_Unitaire'];
    $Quantité_Stock = $_POST['Quantité_Stock'];

    // Validation des données (à ajouter)
    // ...

    // Préparation de la requête SQL de mise à jour
    $sql = "UPDATE Articles 
            SET Designation = :Designation, famille_code = :famille_code, gamme_code = :gamme_code, 
                Prix_Unitaire = :Prix_Unitaire, Quantité_Stock = :Quantité_Stock 
            WHERE Article_Code = :Article_Code";
    $stmt = $conn->prepare($sql);

    // Exécution de la requête de mise à jour
    try {
        $stmt->execute([
            'Designation' => $Designation,
            'famille_code' => $famille_code,
            'gamme_code' => $gamme_code,
            'Prix_Unitaire' => $Prix_Unitaire,
            'Quantité_Stock' => $Quantité_Stock,
            'Article_Code' => $Article_Code
        ]);

        // Redirection vers la page de liste des articles
        header("Location: ?page=articles");
        exit();
    } catch (PDOException $e) {
        // Affichage d'un message d'erreur en cas d'échec de la mise à jour
        echo "Erreur lors de la modification de l'article : " . $e->getMessage();
    }
}

// Récupération des familles pour le menu déroulant
$sqlFamilles = "SELECT * FROM Familles";
$stmtFamilles = $conn->query($sqlFamilles);
$familles = $stmtFamilles->fetchAll(PDO::FETCH_ASSOC);

// Récupération des gammes pour le menu déroulant
$sqlGammes = "SELECT * FROM Gammes";
$stmtGammes = $conn->query($sqlGammes);
$gammes = $stmtGammes->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifier un article</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">                <div class="flex">
        <!-- Barre latérale -->
        <?php include 'components/sidebar.php'; ?>

        <div class="flex-1 p-4 ml-64">
            <!-- Barre de navigation -->
            <?php //include 'components/navbar.php'; ?>
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-4 text-center text-gray-800">Modifier l'article : <?= $article['Article_Code'] ?></h1>

        <!-- Formulaire de modification d'article -->
        <form method="POST" action="?page=articles&action=edit&id=<?= $article['Article_Code'] ?>" class="bg-white p-6 rounded-lg shadow-md">

            <!-- Champ Désignation -->
            <div class="mb-4">
                <label for="Designation" class="block text-gray-700 text-sm font-bold mb-2">Désignation:</label>
                <input type="text" id="Designation" name="Designation" value="<?= $article['Designation'] ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <!-- Menu déroulant pour la famille -->
            <div class="mb-4">
                <label for="famille_code" class="block text-gray-700 text-sm font-bold mb-2">Famille:</label>
                <select name="famille_code" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php foreach ($familles as $famille): ?>
                        <option value="<?= $famille['famille_code'] ?>" <?= ($famille['famille_code'] == $article['famille_code']) ? 'selected' : '' ?>><?= $famille['Libelle'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Menu déroulant pour la gamme -->
            <div class="mb-4">
                <label for="gamme_code" class="block text-gray-700 text-sm font-bold mb-2">Gamme:</label>
                <select name="gamme_code" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php foreach ($gammes as $gamme): ?>
                        <option value="<?= $gamme['gamme_code'] ?>" <?= ($gamme['gamme_code'] == $article['gamme_code']) ? 'selected' : '' ?>><?= $gamme['Libelle'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Champ Prix Unitaire -->
            <div class="mb-4">
                <label for="Prix_Unitaire" class="block text-gray-700 text-sm font-bold mb-2">Prix Unitaire:</label>
                <input type="number" id="Prix_Unitaire" name="Prix_Unitaire" step="0.01" value="<?= $article['Prix_Unitaire'] ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <div class="mb-4">
    <label for="TVA" class="block text-gray-700 text-sm font-bold mb-2">TVA (%):</label>
    <input type="number" id="TVA" name="TVA" step="0.01" value="<?= $article['TVA'] ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
</div>

            <!-- Champ Quantité en Stock -->
            <div class="mb-4">
                <label for="Quantité_Stock" class="block text-gray-700 text-sm font-bold mb-2">Quantité en Stock:</label>
                <input type="number" id="Quantité_Stock" name="Quantité_Stock" value="<?= $article['Quantité_Stock'] ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <!-- Bouton de modification -->
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Modifier
            </button>
        </form>
    </div>
</div></div></body>
</html>