<?php
// factures/view.php

require_once 'config/database.php';

$conn = dbConnect();

if (isset($_GET['id'])) {
    $Facture_ID = $_GET['id'];
    $sql = "SELECT f.*, c.RSOC AS NomClient, c.ADR AS AdresseClient, ce.Date_Commande
            FROM Factures f
            JOIN Commande_Entête ce ON f.Commande_ID = ce.Commande_ID
            JOIN Clients c ON ce.CODECL = c.CODECL
            WHERE f.Facture_ID = :facture_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['facture_id' => $Facture_ID]);
    $facture = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$facture) {
        echo "Facture non trouvée.";
        exit();
    }

    $sqlLignes = "SELECT cl.*, a.Designation, a.TVA as TVA_Article
                  FROM Commande_Ligne cl
                  JOIN Articles a ON cl.Article_Code = a.Article_Code
                  WHERE cl.Commande_ID = :commande_id";
    $stmtLignes = $conn->prepare($sqlLignes);
    $stmtLignes->execute(['commande_id' => $facture['Commande_ID']]);
    $lignesCommande = $stmtLignes->fetchAll(PDO::FETCH_ASSOC);
} else {
    echo "ID de la facture manquant.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>C2S - Facture n°<?= $facture['Facture_ID'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @media print {
            @page {
                size: auto;
                margin: 0;
            }
            body {
                margin: 1cm;
            }
            .hidden-print {
                display: none;
            }

        }
        
    </style>
</head>
<body class="bg-white font-sans">
    <div class="container mx-auto p-6">
        <div class="inline-block flex justify-between items-center mb-8">
            <img src="assets/logo.jpeg" alt="Logo" class="h-16 w-auto">
            <h1 class="text-3xl font-bold text-gray-800">Facture n°<?= date('Y-m')."/"?>
        <form action="" method="get">
            <input type="text" name="id" value="<?= $facture['Facture_ID'] ?>">
            </form>
        </h1>
        </div>

        <div class="flex flex-col md:flex-row justify-between mb-8">
            <div class="w-full md:w-1/2">
                <h2 class="text-xl font-semibold text-gray-700 mb-2">Client:</h2>
                <p class="mb-1"><?= $facture['NomClient'] ?></p>
                <p><?= $facture['AdresseClient'] ?></p>
            </div>
            <div class="w-full md:w-1/2 mt-4 md:mt-0">
                <h2 class="text-xl font-semibold text-gray-700 mb-2">Informations de la commande:</h2>
                <p class="mb-1">ID Commande: <?= $facture['Commande_ID'] ?></p>
                <p>Date Commande: <?= $facture['Date_Commande'] ?></p>
            </div>
        </div>

        <table class="table-auto w-full mb-8">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-center">Article</th>
                    <th class="py-3 px-6 text-center">Prix Unitaire</th>
                    <th class="py-3 px-6 text-center">Quantité</th>
                    <th class="py-3 px-6 text-center">TAX</th>
                    <th class="py-3 px-6 text-center">TVA</th>
                    <th class="py-3 px-6 text-center">Total TTC</th>
                </tr>
            </thead>
            <?php $totalHT = 0; ?>
            <tbody class="text-gray-800 text-sm font-light">
                <?php foreach ($lignesCommande as $ligne): ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-center whitespace-nowrap"><?= $ligne['Designation'] ?></td>
                        <td class="py-3 px-6 text-center"><?= number_format($ligne['Prix_Unitaire'], 2) ?></td>
                        <td class="py-3 px-6 text-center"><?= $ligne['Quantité'] ?></td>
                        <td class="py-3 px-6 text-center"><?= number_format($ligne['TVA_Article'],0) ?>%</td>
                        <td class="py-3 px-6 text-center"><?= number_format($ligne['TVA'], 2) ?></td>
                        <?php 
                        $totalHT += $ligne['Quantité'] * $ligne['Prix_Unitaire']; ?>
                        <td class="py-3 px-6 text-center"><?= number_format(($ligne['Quantité'] * $ligne['Prix_Unitaire']) + $ligne['TVA'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="flex flex-col justify-between">
            <div class="text-right">
                <p class="text-l font-semibold text-gray-700 mr-8">Total HT:</p>
                <p class="text-xl font-bold"><?= number_format($totalHT, 2) ?></p>
            </div>

            <div class="text-right">
                <p class="text-l font-semibold text-gray-700 mr-8">Total TTC:</p>
                <p class="text-xl font-bold"><?= number_format($facture['Total'], 2) ?></p>
            </div>
        </div>

        <div class="mt-8 text-center text-gray-500 hidden-print">
            <button id="print" onclick="window.print()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Imprimer
            </button>
        </div>
    </div>
</body>
</html>