<?php
// articles/create.php

// Inclusion du fichier de configuration de la base de données
require_once 'config/database.php';

// Connexion à la base de données
$conn = dbConnect();

// Récupération des familles pour le menu déroulant
$sqlFamilles = "SELECT * FROM Familles";
$stmtFamilles = $conn->query($sqlFamilles);
$familles = $stmtFamilles->fetchAll(PDO::FETCH_ASSOC);

// Récupération des gammes pour le menu déroulant
$sqlGammes = "SELECT * FROM Gammes";
$stmtGammes = $conn->query($sqlGammes);
$gammes = $stmtGammes->fetchAll(PDO::FETCH_ASSOC);


// Gestion de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST'&&isset($_POST['add_article'])) {
    // Récupération des données du formulaire
    $Article_Code = $_POST['Article_Code'];
    $Designation = $_POST['Designation'];
    $famille_code = $_POST['famille_code'];
    $gamme_code = $_POST['gamme_code'];
    $Prix_Unitaire = $_POST['Prix_Unitaire'];
    $Quantité_Stock = $_POST['Quantité_Stock'];
    $TVA = $_POST['TVA'];

    // Validation des données (à ajouter)
    // sanitisez les données
    $Article_Code = filter_var($Article_Code, FILTER_SANITIZE_STRING);
    $Designation = filter_var($Designation, FILTER_SANITIZE_STRING);
    $famille_code = filter_var($famille_code, FILTER_SANITIZE_STRING);
    $gamme_code = filter_var($gamme_code, FILTER_SANITIZE_STRING);
    $Prix_Unitaire = filter_var($Prix_Unitaire, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $Quantité_Stock = filter_var($Quantité_Stock, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    $TVA = filter_var($TVA, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Gestion des erreurs
    if (empty($Article_Code) || empty($Designation) || empty($famille_code) || empty($gamme_code) || empty($Prix_Unitaire) || empty($Quantité_Stock) || empty($TVA)) {
        $error = "Veuillez remplir tous les champs";
    }

    
    
    // Préparation de la requête SQL d'insertion
    // change sql for sqlserver 
    $sql = "INSERT INTO Articles (Article_Code, Designation, famille_code, gamme_code, Prix_Unitaire, Quantité_Stock, TVA)
            VALUES ('".$Article_Code."', '".$Designation."', '".$famille_code."', '".$gamme_code."', '".$Prix_Unitaire."', '".$Quantité_Stock."', '".$TVA."')";
    $stmt = $conn->prepare($sql);

    // Exécution de la requête d'insertion
    try {
        $stmt->execute();

        // Redirection vers la page de liste des articles
        header("Location: ?page=articles");
        exit();
    } catch (PDOException $e) {
        // Affichage d'un message d'erreur en cas d'échec de l'insertion
        echo "Erreur lors de la création de l'article : " . $e->getMessage();
    }
    
}

// INSERT ARTICLE EROOR à ajouter

// Récupérer le dernier code d'article de la base de données pour l'auto-incrémentation
$sqlGetLastArticleCode = "SELECT MAX(CAST(SUBSTRING(Article_Code, 6, 3) AS INT)) AS LastCode FROM Articles";
$stmtLastArticleCode = $conn->query($sqlGetLastArticleCode);
$lastArticleCode = $stmtLastArticleCode->fetch(PDO::FETCH_ASSOC)['LastCode'];

// Générer le prochain code d'article
$nextArticleCode = "ART00" . str_pad($lastArticleCode + 1, 3, "0", STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Créer un nouvel article</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
</head>
<body class="bg-gray-100 font-sans">                <div class="flex">
        <!-- Barre latérale -->
        <?php include 'components/sidebar.php'; ?>

        <div class="flex-1 p-4 ml-64">
            <!-- Barre de navigation -->
            <?php //include 'components/navbar.php'; ?>
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-4 text-center text-gray-800">Créer un nouvel article</h1>

        <!-- Formulaire de création d'article -->
        <form method="POST" action="?page=articles&action=create" class="bg-white p-6 rounded-lg shadow-md">

            <!-- Champ Code Article -->
            <div class="mb-4">
                <label for="Article_Code" class="block text-gray-700 text-sm font-bold mb-2">Code Article:</label>
                <input type="text" value="<?php echo $nextArticleCode; ?>" id="Article_Code" name="Article_Code" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"  required>
            </div>

            <!-- Champ Désignation -->
            <div class="mb-4">
                <label for="Designation" class="block text-gray-700 text-sm font-bold mb-2">Désignation:</label>
                <input type="text" id="Designation" value="pc" name="Designation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <!-- Menu déroulant pour la famille -->
            <div class="mb-4">
                <label for="famille_code" class="block text-gray-700 text-sm font-bold mb-2">Famille:</label>
                <select name="famille_code" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php foreach ($familles as $famille): ?>
                        <option value="<?= $famille['famille_code'] ?>"><?= $famille['Libelle'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Menu déroulant pour la gamme -->
            <div class="mb-4">
                <label for="gamme_code" class="block text-gray-700 text-sm font-bold mb-2">Gamme:</label>
                <select name="gamme_code" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php foreach ($gammes as $gamme): ?>
                        <option value="<?= $gamme['gamme_code'] ?>"><?= $gamme['Libelle'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Champ Prix Unitaire -->
            <div class="mb-4">
                <label for="Prix_Unitaire" class="block text-gray-700 text-sm font-bold mb-2">Prix Unitaire:</label>
                <input type="number" value="100.00" id="Prix_Unitaire" name="Prix_Unitaire" step="0.01" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>
            <!-- Champ TVA -->
            <div class="mb-4">
    <label for="TVA" class="block text-gray-700 text-sm font-bold mb-2">TVA (%):</label>
    
    <select name="TVA" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        <option value="19.00" selected>19%</option>
        <option value="10.00">10%</option>
        <option value="5.00">5%</option>
        <option value="0.00">0%</option>
    </select>
    
</div>

            <!-- Champ Quantité en Stock -->
            <div class="mb-4">
                <label for="Quantité_Stock" class="block text-gray-700 text-sm font-bold mb-2">Quantité en Stock:</label>
                <input type="number" id="Quantité_Stock" value="100" name="Quantité_Stock" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <!-- Bouton de création -->
            <button name="add_article" type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Créer
            </button>
        </form>
    </div>
</div></div></body>
</html>