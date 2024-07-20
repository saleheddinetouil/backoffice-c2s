<?php
// commandes/view.php 

require_once 'config/database.php'; 

$conn = dbConnect(); 

if (isset($_GET['id'])) {
    $Commande_ID = $_GET['id']; 

    // Récupérer les informations de la commande
    $sqlCommande = "SELECT ce.*, cl.RSOC AS NomClient, cl.ADR AS AdresseClient, u.username AS Representant
                    FROM Commande_Entête ce
                    JOIN Clients cl ON ce.CODECL = cl.CODECL
                    JOIN Users u ON ce.representant_id = u.user_id
                    WHERE ce.Commande_ID = :commande_id";
    $stmtCommande = $conn->prepare($sqlCommande);
    $stmtCommande->execute(['commande_id' => $Commande_ID]); 
    $commande = $stmtCommande->fetch(PDO::FETCH_ASSOC); 

    if (!$commande) {
        echo "Commande non trouvée.";
        exit(); 
    }

    // Récupérer les lignes de la commande
    $sqlLignes = "SELECT cl.*, a.Designation, a.TVA as TVA_Article
                    FROM Commande_Ligne cl 
                    JOIN Articles a ON cl.Article_Code = a.Article_Code
                    WHERE cl.Commande_ID = :commande_id";
    $stmtLignes = $conn->prepare($sqlLignes); 
    $stmtLignes->execute(['commande_id' => $Commande_ID]);
    $lignesCommande = $stmtLignes->fetchAll(PDO::FETCH_ASSOC);
} else { 
    echo "ID de la commande manquant."; 
    exit(); 
}
?>

<!DOCTYPE html> 
<html>
<head>
    <title>GestionVente - Bon de commande n°<?= $commande['Commande_ID'] ?></title> 
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
        }
    </style> 
</head>
<body class="bg-white font-sans"> 
    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-8"> 
            <!-- Logo -->
            <img src="assets/logo.jpeg" alt="Logo" class="h-16 w-auto">

            <!-- Titre du bon de commande -->
            <h1 class="text-3xl font-bold text-gray-800">Bon de Commande n°<?= date('Y-m')."/".$commande['Commande_ID'] ?></h1>
        </div> 

        <!-- Informations du client et de la commande -->
        <div class="flex flex-col md:flex-row justify-between mb-8">
            <div class="w-full md:w-1/2">
                <h2 class="text-xl font-semibold text-gray-700 mb-2">Client:</h2> 
                <p class="mb-1"><?= $commande['NomClient'] ?></p>
                <p><?= $commande['AdresseClient'] ?></p>
            </div> 
            <div class="w-full md:w-1/2 mt-4 md:mt-0"> 
                <h2 class="text-xl font-semibold text-gray-700 mb-2">Informations de la commande:</h2>
                <p class="mb-1">ID Commande: <?= $commande['Commande_ID'] ?></p>
                <p>Date Commande: <?= $commande['Date_Commande'] ?></p>
                <p>Représentant: <?= $commande['Representant'] ?></p>
            </div>
        </div>

        <!-- Tableau des articles -->
        <table class="table-auto w-full mb-8"> 
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Article</th>
                    <th class="py-3 px-6 text-right">Prix Unitaire</th>
                    <th class="py-3 px-6 text-right">Quantité</th>
                    <th class="py-3 px-6 text-right">TVA</th>
                    <th class="py-3 px-6 text-right">Total TTC</th>
                </tr> 
            </thead> 
            <tbody class="text-gray-800 text-sm font-light">
                <?php 
                $totalHT = 0; 
                $totalTVA = 0; 
                $totalTTC = 0; 

                foreach ($lignesCommande as $ligne): 
                    $totalLigneHT = $ligne['Quantité'] * $ligne['Prix_Unitaire'];
                    $montantTVA = $totalLigneHT * ($ligne['TVA_Article'] / 100); 
                    $totalLigneTTC = $totalLigneHT + $montantTVA; 

                    $totalHT += $totalLigneHT;
                    $totalTVA += $montantTVA;
                    $totalTTC += $totalLigneTTC; 
                    ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-100"> 
                        <td class="py-3 px-6 text-left whitespace-nowrap"><?= $ligne['Designation'] ?></td>
                        <td class="py-3 px-6 text-right"><?= number_format($ligne['Prix_Unitaire'], 2) ?></td>
                        <td class="py-3 px-6 text-right"><?= $ligne['Quantité'] ?></td>
                        <td class="py-3 px-6 text-right"><?= number_format($montantTVA, 2) ?></td> 
                        <td class="py-3 px-6 text-right"><?= number_format($totalLigneTTC, 2) ?></td>
                    </tr> 
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Totaux --> 
        <div class="flex justify-end">
            <div class="text-right">
                <p class="text-xl font-semibold text-gray-700">Total HT:</p>
                <p class="text-lg"><?= number_format($totalHT, 2) ?></p>

                <p class="text-xl font-semibold text-gray-700">Total TVA:</p> 
                <p class="text-lg"><?= number_format($totalTVA, 2) ?></p>

                <p class="text-xl font-semibold text-gray-700">Total TTC:</p>
                <p class="text-2xl font-bold"><?= number_format($totalTTC, 2) ?></p>
            </div>
        </div>

        <!-- Bouton Imprimer -->
        <div class="mt-8 text-center"> 
            <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Imprimer 
            </button>
        </div> 
    </div>
</body>
</html>