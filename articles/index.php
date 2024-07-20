<?php
// articles/index.php

// Inclusion du fichier de configuration de la base de données
require_once 'config/database.php';

// Connexion à la base de données
$conn = dbConnect();

// Récupération de tous les articles
$sql = "SELECT * FROM Articles";
$stmt = $conn->query($sql);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>C2S - Articles</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">                <div class="flex">
        <!-- Barre latérale -->
        <?php include 'components/sidebar.php'; ?>

        <div class="flex-1 p-4 ml-64">
            <!-- Barre de navigation -->
            <?php //include 'components/navbar.php'; ?>
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-4 text-center text-gray-800">Articles</h1>

        
        <div class="flex justify-end mb-4">
            <a href="?page=articles&action=create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 inline-block">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </a>
        </div>

        <!-- Tableau des articles -->
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
        <table id="table" class="table-auto w-full text-center whitespace-nowrap table-fixed">
                <!-- En-tête du tableau -->
                <thead>
                    <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6 text-left">Code Article</th>
                        <th class="py-3 px-6 text-left">Désignation</th>
                        <th class="py-3 px-6 text-left">Famille</th>
                        <th class="py-3 px-6 text-left">Gamme</th>
                        <th class="py-3 px-6 text-left">Prix Unitaire</th>
                        <th class="py-3 px-6 text-right">TVA</th>
                        <th class="py-3 px-6 text-right">Total TTC</th>
                        <th class="py-3 px-6 text-left">Quantité en Stock</th>
                        <th class="py-3 px-12 text-right">Actions</th>
                    </tr>
                </thead>
                <!-- Corps du tableau -->
                <tbody class="text-gray-800 text-sm font-light">
                    <?php foreach ($articles as $article): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-100">
                            <td class="py-3 px-6 text-center whitespace-nowrap"><?= $article['Article_Code'] ?></td>
                            <td class="py-3 px-6 text-center"><?= $article['Designation'] ?></td>
                            <td class="py-3 px-6 text-center">
                                <?php
                                // Récupération du nom de la famille
                                $sqlFamille = "SELECT Libelle FROM Familles WHERE famille_code = :famille_code";
                                $stmtFamille = $conn->prepare($sqlFamille);
                                $stmtFamille->execute(['famille_code' => $article['famille_code']]);
                                $famille = $stmtFamille->fetch(PDO::FETCH_ASSOC);
                                echo $famille ? $famille['Libelle'] : ''; // Affichage du nom de la famille
                                ?>
                            </td>
                            <td class="py-3 px-6 text-center">
                                <?php
                                // Récupération du nom de la gamme
                                $sqlGamme = "SELECT Libelle FROM Gammes WHERE gamme_code = :gamme_code";
                                $stmtGamme = $conn->prepare($sqlGamme);
                                $stmtGamme->execute(['gamme_code' => $article['gamme_code']]);
                                $gamme = $stmtGamme->fetch(PDO::FETCH_ASSOC);
                                echo $gamme ? $gamme['Libelle'] : ''; // Affichage du nom de la gamme
                                ?>
                            </td>
                            <td class="py-3 px-6 text-center"><?= $article['Prix_Unitaire'] ?></td>
                            <td class="py-3 px-6 text-center"><?= number_format($article['TVA'],0)."%" ?></td>
                            <td class="py-3 px-6 text-center"><?= number_format(($article['Prix_Unitaire'] + $article['Prix_Unitaire'] * 0.01 * $article['TVA']),2) ?></td>
                            <td class="py-3 px-6 text-center"><?= $article['Quantité_Stock'] ?></td>
                            <td class="py-3 px-6 text-center">
                                <a href="?page=articles&action=edit&id=<?= $article['Article_Code'] ?>" class="text-blue-600 hover:text-blue-800 mr-2">Modifier</a>
                                <a href="?page=articles&action=delete&id=<?= $article['Article_Code'] ?>" class="text-red-600 hover:text-red-800" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // search table handler
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