<?php
// commandes/create.php

require_once 'config/database.php';

$conn = dbConnect();

// Fetch clients and representants for dropdown menus
$sqlClients = "SELECT * FROM Clients";
$stmtClients = $conn->query($sqlClients);
$clients = $stmtClients->fetchAll(PDO::FETCH_ASSOC);

// Fetch representatives from the Users table (with 'Représentant' role)
$sqlRepresentants = "SELECT * FROM Users WHERE role = 'Représentant'"; 
$stmtRepresentants = $conn->query($sqlRepresentants);
$representants = $stmtRepresentants->fetchAll(PDO::FETCH_ASSOC);

// Fetch articles for selection table
$sqlArticles = "SELECT * FROM Articles";
$stmtArticles = $conn->query($sqlArticles);
$articles = $stmtArticles->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $CODECL = $_POST['CODECL'];
    $representant_id = $_POST['representant_id']; // Get representant_id from the form
    $selectedArticles = isset($_POST['selected_articles']) ? $_POST['selected_articles'] : []; 

    try {
        // Start a transaction to ensure atomicity
        $conn->beginTransaction();

        // Insert Commande_Entête
        $sql = "INSERT INTO Commande_Entête (CODECL, representant_id) VALUES (:CODECL, :representant_id)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'CODECL' => $CODECL,
            'representant_id' => $representant_id
        ]);



        $Commande_ID = $conn->lastInsertId();

        // Insert Commande_Ligne for each selected article
        foreach ($selectedArticles as $Article_Code) {
            $Quantité = $_POST['quantite_' . $Article_Code]; 

            $sqlArticle = "SELECT Prix_Unitaire, TVA FROM Articles WHERE Article_Code = :Article_Code";
            $stmtArticle = $conn->prepare($sqlArticle); 
            $stmtArticle->execute(['Article_Code' => $Article_Code]); 
            $article = $stmtArticle->fetch(PDO::FETCH_ASSOC); 

            $TVA = ($Quantité * $article['Prix_Unitaire']) * ($article['TVA'] / 100);

            $sqlLigne = "INSERT INTO Commande_Ligne (Commande_ID, Article_Code, Quantité, Prix_Unitaire, TVA) 
                         VALUES (:Commande_ID, :Article_Code, :Quantité, :Prix_Unitaire, :TVA)";
            $stmtLigne = $conn->prepare($sqlLigne); 
            $stmtLigne->execute([
                'Commande_ID' => $Commande_ID,
                'Article_Code' => $Article_Code,
                'Quantité' => $Quantité, 
                'Prix_Unitaire' => $article['Prix_Unitaire'],
                'TVA' => $TVA 
            ]);




        }

        // Commit transaction if all insertions were successful
        $conn->commit(); 

        header("Location: ?page=commandes&action=edit&id=" . $Commande_ID);
        exit();
    } catch (PDOException $e) {
        // Roll back transaction in case of an error
        $conn->rollBack();

        echo "Erreur lors de la création de la commande : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Créer une nouvelle commande</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen">
        <?php include 'components/sidebar.php'; ?>

        <div class="flex-1 ml-64 p-4 overflow-y-auto">

            <h1 class="text-3xl font-bold mb-4 text-center text-gray-800">Créer une nouvelle commande</h1>

            <form method="POST" action="?page=commandes&action=create" class="bg-white p-6 rounded-lg shadow-md">
                <div class="mb-4">
                    <label for="CODECL" class="block text-gray-700 text-sm font-bold mb-2">Client:</label>
                    <select name="CODECL" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <?php foreach ($clients as $client): ?>
                            <option value="<?= $client['CODECL'] ?>"><?= $client['RSOC'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="representant_id" class="block text-gray-700 text-sm font-bold mb-2">Représentant:</label>
                    <select name="representant_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        <?php foreach ($representants as $representant): ?>
                            <option value="<?= $representant['user_id'] ?>"><?= $representant['username'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

     

                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mt-4">
                    Créer la commande
                </button>
            </form>
        </div>
    </div>

    <script>
        const selectAllCheckbox = document.getElementById('selectAll');
        const articleCheckboxes = document.querySelectorAll('.articleCheckbox');
        
        selectAllCheckbox.addEventListener('change', function() {
            articleCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });
    </script>
</body>
</html>