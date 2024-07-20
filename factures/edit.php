<?php
// factures/edit.php

require_once 'config/database.php';

$conn = dbConnect(); // Connexion à la base de données

// Récupération de la facture à modifier
if (isset($_GET['id'])) {
    $Facture_ID = $_GET['id'];
    $sql = "SELECT * FROM Factures WHERE Facture_ID = :facture_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['facture_id' => $Facture_ID]);
    $facture = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si la facture existe
    if (!$facture) {
        echo "Facture non trouvée.";
        exit();
    }
} else {
    echo "ID de la facture manquant.";
    exit();
}

// Gestion de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $Commande_ID = $_POST['Commande_ID'];

    // Calcul du total de la commande
    $sqlTotal = "SELECT SUM(Quantité * Prix_Unitaire) AS Total 
                 FROM Commande_Ligne 
                 WHERE Commande_ID = :commande_id";
    $stmtTotal = $conn->prepare($sqlTotal);
    $stmtTotal->execute(['commande_id' => $Commande_ID]);
    $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['Total'];

    // Mise à jour de la facture
    $sqlUpdate = "UPDATE Factures SET Commande_ID = :commande_id, Total = :total WHERE Facture_ID = :facture_id";
    $stmtUpdate = $conn->prepare($sqlUpdate);

    try {
        $stmtUpdate->execute([
            'commande_id' => $Commande_ID,
            'total' => $total,
            'facture_id' => $Facture_ID
        ]);

        header("Location: ?page=factures");
        exit();
    } catch (PDOException $e) {
        echo "Erreur lors de la modification de la facture : " . $e->getMessage();
    }
}

// Récupérer les commandes pour le menu déroulant
$sqlCommandes = "SELECT Commande_ID FROM Commande_Entête";
$stmtCommandes = $conn->query($sqlCommandes);
$commandes = $stmtCommandes->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifier la facture n°<?= $facture['Facture_ID'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
                    <div class="flex">
        <?php include 'components/sidebar.php'; ?>

        <div class="flex-1 p-4 ml-64">
            <?php //include 'components/navbar.php'; ?>

            <h1 class="text-3xl font-bold mb-4 text-center text-gray-800">Modifier la facture n°<?= $facture['Facture_ID'] ?></h1>

            <form method="POST" action="?page=factures&action=edit&id=<?= $facture['Facture_ID'] ?>" class="bg-white p-6 rounded-lg shadow-md">
                <div class="mb-4">
                    <label for="Commande_ID" class="block text-gray-700 text-sm font-bold mb-2">Commande:</label>
                    <select name="Commande_ID" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <?php foreach ($commandes as $commande): ?>
                            <option value="<?= $commande['Commande_ID'] ?>" <?= ($commande['Commande_ID'] == $facture['Commande_ID']) ? 'selected' : '' ?>><?= $commande['Commande_ID'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Modifier
                </button>
            </form>
        </div>
    </div>
</body>
</html>