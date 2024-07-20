<?php
// factures/index.php

require_once 'config/database.php';

$conn = dbConnect();

$sql = "SELECT f.*, c.RSOC AS NomClient 
        FROM Factures f
        JOIN Commande_Entête ce ON f.Commande_ID = ce.Commande_ID
        JOIN Clients c ON ce.CODECL = c.CODECL";
$stmt = $conn->query($sql);
$factures = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>GestionVente - Factures</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
                    <div class="flex">
        <?php include 'components/sidebar.php'; ?>

        <div class="flex-1 p-4 ml-64">

            <h1 class="text-3xl font-bold mb-4 text-center text-gray-800 p-12">Factures</h1>
        
        <div class="flex justify-end mb-4">
            <a href="?page=factures&action=create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 inline-block">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </a>
        </div>
            <div class="overflow-x-auto">
                 <!-- search bar -->
                 <div class="flex justify-end mb-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9 2a7 7 0 100 14 7 7 0 000-14m0 1a6 6 0 110 12 6 6 0 010-12Z" clip-rule="evenodd" />
                                <path fill-rule="evenodd" d="M9 4a5 5 0 100 10 5 5 0 000-10m0 1a4 4 0 110 8 4 4 0 010-8Z" clip-rule="evenodd" />
                                <path fill-rule="evenodd" d="M13 14a1 1 0 10-2 0v2a1 1 0 102 0v-2Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" id="search" class="border border-gray-300 rounded-full py-2 pl-10 pr-4 focus:outline-none focus:border-blue-500" placeholder="Rechercher">
                    </div>
                </div>
                <table class="table-auto w-full">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">ID Facture</th>
                            <th class="py-3 px-6 text-left">ID Commande</th>
                            <th class="py-3 px-6 text-left">Date Facture</th>
                            <th class="py-3 px-6 text-left">Client</th>
                            <th class="py-3 px-6 text-left">Total</th>
                            <th class="py-3 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-800 text-sm font-light">
                        <?php foreach ($factures as $facture): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left whitespace-nowrap"><?= $facture['Facture_ID'] ?></td>
                                <td class="py-3 px-6 text-left"><?= $facture['Commande_ID'] ?></td>
                                <td class="py-3 px-6 text-left"><?= $facture['Date_Facture'] ?></td>
                                <td class="py-3 px-6 text-left"><?= $facture['NomClient'] ?></td>
                                <td class="py-3 px-6 text-left"><?= $facture['Total'] ?></td>
                                <td class="py-3 px-6 text-center">
                                    <a href="?page=factures&action=view&id=<?= $facture['Facture_ID'] ?>" class="text-blue-600 hover:text-blue-800 mr-2">Voir</a>
                                    <a href="?page=factures&action=delete&id=<?= $facture['Facture_ID'] ?>" class="text-red-600 hover:text-red-800" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette facture ?')">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script >
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.querySelector('table');

        //search table handler 
        const searchInput = document.getElementById('search');
        searchInput.addEventListener('input', function() {
            const searchValue = searchInput.value.toLowerCase();
            const rows = table.getElementsByTagName('tr');
            for (let i = 1; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                let found = false;
                for (let j = 0; j < cells.length; j++) {
                    const cellText = cells[j].textContent.toLowerCase();
                    if (cellText.indexOf(searchValue) > -1) {
                        found = true;
                        break;
                    }
                }
                if (found) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        });
        });

    </script>
</body>
</html>