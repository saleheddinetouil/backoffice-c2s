<?php
// factures/create.php

require_once 'config/database.php';

$conn = dbConnect();

// Récupérer la liste des commandes pour le menu déroulant
$sqlCommandes = "SELECT Commande_ID FROM Commande_Entête";
$stmtCommandes = $conn->query($sqlCommandes);
$commandes = $stmtCommandes->fetchAll(PDO::FETCH_ASSOC);

// Gestion de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $Commande_ID = $_POST['Commande_ID'];

    // Calcul du total de la commande
    $sqlTotal = "SELECT SUM(Quantité * Prix_Unitaire + TVA) AS Total 
                 FROM Commande_Ligne 
                 WHERE Commande_ID = :commande_id";
    $stmtTotal = $conn->prepare($sqlTotal);
    $stmtTotal->execute(['commande_id' => $Commande_ID]);
    $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['Total'];

    // Insertion de la facture
    $sqlFacture = "INSERT INTO Factures (Commande_ID, Total) VALUES (:commande_id, :total)";
    $stmtFacture = $conn->prepare($sqlFacture);

    try {
        $stmtFacture->execute([
            'commande_id' => $Commande_ID,
            'total' => $total
        ]);
        header("Location: ?page=factures");
        exit();
    } catch (PDOException $e) {
        echo "Erreur lors de la création de la facture : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Créer une nouvelle facture</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
                    <div class="flex">
        <?php include 'components/sidebar.php'; ?>

        <div class="flex-1 p-4 ml-64">
            <?php //include 'components/navbar.php'; ?>

            <h1 class="text-3xl font-bold mb-4 text-center text-gray-800">Créer une nouvelle facture</h1>

            <form method="POST" action="?page=factures&action=create" class="bg-white p-6 rounded-lg shadow-md">
                <div class="mb-4">
                    <label for="Commande_ID" class="block text-gray-700 text-sm font-bold mb-2">Commande:</label>
                    <select name="Commande_ID" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <?php foreach ($commandes as $commande): ?>
                            <option value="<?= $commande['Commande_ID'] ?>" <?php if(isset($_GET['commande_id'])&&($_GET['commande_id'])==$commande['Commande_ID']){
                                echo 'selected';
                            } ?>><?= $commande['Commande_ID'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Créer
                </button>
            </form>
        </div>
    </div>
</body>
</html>