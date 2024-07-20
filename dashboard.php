<?php
// dashboard.php

if (!isset($_SESSION['user_id'])) {
    header('Location: ?page=auth&action=login');
    exit();
}



$sqlTotalClients = "SELECT COUNT(*) as total_clients FROM Clients";
$stmtTotalClients = $conn->query($sqlTotalClients);
$totalClients = $stmtTotalClients->fetch(PDO::FETCH_ASSOC)['total_clients'];

$sqlTotalArticles = "SELECT COUNT(*) as total_articles FROM Articles";
$stmtTotalArticles = $conn->query($sqlTotalArticles);
$totalArticles = $stmtTotalArticles->fetch(PDO::FETCH_ASSOC)['total_articles'];

$sqlTotalCommandes = "SELECT COUNT(*) as total_commandes FROM Commande_Entête";
$stmtTotalCommandes = $conn->query($sqlTotalCommandes);
$totalCommandes = $stmtTotalCommandes->fetch(PDO::FETCH_ASSOC)['total_commandes'];

$sqlTotalFactures = "SELECT COUNT(*) as total_factures FROM Factures";
$stmtTotalFactures = $conn->query($sqlTotalFactures);
$totalFactures = $stmtTotalFactures->fetch(PDO::FETCH_ASSOC)['total_factures'];

$sqlTotalUsers = "SELECT COUNT(*) as total_users FROM Users";
$stmtTotalUsers = $conn->query($sqlTotalUsers);
$totalUsers = $stmtTotalUsers->fetch(PDO::FETCH_ASSOC)['total_users'];




// Data for Charts

// SQL Query to get sales data by client
$sql = "SELECT cl.RSOC AS Client, SUM(f.Total) AS TotalVentes
        FROM Clients cl
        LEFT JOIN Commande_Entête ce ON cl.CODECL = ce.CODECL
        LEFT JOIN Factures f ON ce.Commande_ID = f.Commande_ID
        GROUP BY cl.RSOC"; 
$stmt = $conn->query($sql);
$ventesParClient = $stmt->fetchAll(PDO::FETCH_ASSOC); 

$clientLabels = array_column($ventesParClient, 'Client');
$clientSales = array_column($ventesParClient, 'TotalVentes');

// 1. Articles Vendus (Quantities Sold)
$sqlArticlesVendus = "SELECT a.Designation, SUM(cl.Quantité) AS QuantitéVendue
                      FROM Articles a
                      LEFT JOIN Commande_Ligne cl ON a.Article_Code = cl.Article_Code
                      GROUP BY a.Designation";
$stmtArticlesVendus = $conn->query($sqlArticlesVendus);

$articlesVendus = $stmtArticlesVendus->fetchAll(PDO::FETCH_ASSOC);

$articlesVendusLabels = array_column($articlesVendus, 'Designation');
$articlesVendusQuantities = array_column($articlesVendus, 'QuantitéVendue');

// 2. Ventes par Mois
$sqlVentesParMois = "SELECT MONTH(ce.Date_Commande) AS Mois, SUM(cl.Quantité * cl.Prix_Unitaire + cl.TVA) AS TotalVentes
                      FROM Commande_Entête ce
                      JOIN Commande_Ligne cl ON ce.Commande_ID = cl.Commande_ID
                      WHERE YEAR(ce.Date_Commande) = YEAR(GETDATE())
                      GROUP BY MONTH(ce.Date_Commande)
                      ORDER BY MONTH(ce.Date_Commande)";
$stmtVentesParMois = $conn->query($sqlVentesParMois);
$ventesParMois = $stmtVentesParMois->fetchAll(PDO::FETCH_ASSOC);

$ventesParMoisLabels = array_map(function($row) { 
    return date('F', mktime(0, 0, 0, $row['Mois'], 1)); 
}, $ventesParMois); 
$ventesParMoisSales = array_column($ventesParMois, 'TotalVentes');

// 3. Ventes par Catégorie (Famille)
$sqlVentesParCategorie = "SELECT f.Libelle AS Famille, SUM(cl.Quantité * cl.Prix_Unitaire + cl.TVA) AS TotalVentes
                          FROM Familles f
                          LEFT JOIN Articles a ON f.famille_code = a.famille_code
                          LEFT JOIN Commande_Ligne cl ON a.Article_Code = cl.Article_Code
                          GROUP BY f.Libelle";
$stmtVentesParCategorie = $conn->query($sqlVentesParCategorie);
$ventesParCategorie = $stmtVentesParCategorie->fetchAll(PDO::FETCH_ASSOC);

$ventesParCategorieLabels = array_column($ventesParCategorie, 'Famille');
$ventesParCategorieSales = array_column($ventesParCategorie, 'TotalVentes');

// 4. Ventes par Client
$sqlVentesParClient = "SELECT cl.RSOC AS Client, SUM(f.Total) AS TotalVentes
                      FROM Clients cl
                      LEFT JOIN Commande_Entête ce ON cl.CODECL = ce.CODECL
                      LEFT JOIN Factures f ON ce.Commande_ID = f.Commande_ID
                      GROUP BY cl.RSOC";
$stmtVentesParClient = $conn->query($sqlVentesParClient);
$ventesParClient = $stmtVentesParClient->fetchAll(PDO::FETCH_ASSOC);

$ventesParClientLabels = array_column($ventesParClient, 'Client');
$ventesParClientSales = array_column($ventesParClient, 'TotalVentes');

// 5. Ventes par Zone
$sqlVentesParZone = "SELECT z.Nom_Zone AS Zone, SUM(f.Total) AS TotalVentes
                      FROM Zones z
                      LEFT JOIN Clients cl ON z.zone_id = cl.zone_id
                      LEFT JOIN Commande_Entête ce ON cl.CODECL = ce.CODECL
                      LEFT JOIN Factures f ON ce.Commande_ID = f.Commande_ID
                      GROUP BY z.Nom_Zone";
$stmtVentesParZone = $conn->query($sqlVentesParZone);
$ventesParZone = $stmtVentesParZone->fetchAll(PDO::FETCH_ASSOC);

$ventesParZoneLabels = array_column($ventesParZone, 'Zone');
$ventesParZoneSales = array_column($ventesParZone, 'TotalVentes');

// 6. Ventes par Représentant
$sqlVentesParRepresentant = "SELECT u.username AS Représentant, SUM(f.Total) AS TotalVentes
                            FROM Users u
                            LEFT JOIN Commande_Entête ce ON u.user_id = ce.representant_id
                            LEFT JOIN Factures f ON ce.Commande_ID = f.Commande_ID
                            WHERE u.role = 'Représentant'
                            GROUP BY u.username";
$stmtVentesParRepresentant = $conn->query($sqlVentesParRepresentant);
$ventesParRepresentant = $stmtVentesParRepresentant->fetchAll(PDO::FETCH_ASSOC);

$ventesParRepresentantLabels = array_column($ventesParRepresentant, 'Représentant');
$ventesParRepresentantSales = array_column($ventesParRepresentant, 'TotalVentes');


// Retourner le role de l'Utilisateur 
$sqlGetRole = "SELECT * FROM Users WHERE user_id = '" . $_SESSION['user_id'] . "'";
$stmtGetRole = $conn->prepare($sqlGetRole);
$stmtGetRole->execute();
$result = $stmtGetRole->fetch(PDO::FETCH_ASSOC);
$role = $result['role'];



?>

<!DOCTYPE html>
<html>
<head>
    <title>C2S - Tableau de bord</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/charts.js" defer></script>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="bg-gray-100 font-sans">
                    <div class="flex">
        <?php include 'components/sidebar.php'; ?>

                       <div class="flex-1 p-4 ml-64">
            <?php //include 'components/navbar.php'; ?>
    
            <h1 class="text-3xl font-bold mb-4 text-center text-gray-800 p-12">Tableau de bord</h1>
<?php if ($role == 'Administrateur') { ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                <a href="?page=clients" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transform transition duration-300 ease-in-out">
                    <h2 class="text-xl font-semibold mb-2 text-blue-600">Clients</h2>
                    <p class="text-gray-700 text-2xl font-bold"><?= $totalClients ?></p>
                </a>
                <a href="?page=utilisateurs" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transform transition duration-300 ease-in-out">
                    <h2 class="text-xl font-semibold mb-2 text-indigo-600 mb-4">Utilisateurs</h2>
                    <p class="text-gray-700 text-2xl font-bold"><?= $totalUsers ?></p>
                </a>
                <a href="?page=circuits" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transform transition duration-300 ease-in-out">
                    <h2 class="text-xl font-semibold mb-2 text-red-600">Circuits</h2>
                    <p class="text-gray-700 text-2xl font-bold"><?= $totalCircuits ?></p>
                </a>

                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                
                <a href="?page=articles" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transform transition duration-300 ease-in-out">
                    <h2 class="text-xl font-semibold mb-2 text-green-600">Articles</h2>
                    <p class="text-gray-700 text-2xl font-bold"><?= $totalArticles ?></p>
                </a> 
                <a href="?page=gammes" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transform transition duration-300 ease-in-out">
                    <h2 class="text-xl font-semibold mb-2 text-purple-600">Gammes</h2>
                    <p class="text-gray-700 text-2xl font-bold"><?= $totalGammes ?></p>
                </a>
                <a href="?page=familles" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transform transition duration-300 ease-in-out">
                    <h2 class="text-xl font-semibold mb-2 text-pink-600">Familles</h2>
                    <p class="text-gray-700 text-2xl font-bold"><?= $totalFamilles ?></p>
                </a>
                </div>
                <?php } ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                <a href="?page=commandes" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transform transition duration-300 ease-in-out">
                    <h2 class="text-xl font-semibold mb-2 text-yellow-600">Commandes</h2>
                    <p class="text-gray-700 text-2xl font-bold"><?= $totalCommandes ?></p>
                </a>
                <a href="?page=factures" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transform transition duration-300 ease-in-out">
                    <h2 class="text-xl font-semibold mb-2 text-red-600">Factures</h2>
                    <p class="text-gray-700 text-2xl font-bold"><?= $totalFactures ?></p>
                </a>
                <?php if ($role == 'Administrateur') { ?>
                <a href="?page=zones" class="bg-white p-6 rounded-lg shadow-md hover:shadow-lg transform transition duration-300 ease-in-out">
                    <h2 class="text-xl font-semibold mb-2 text-green-600">Zones</h2>
                    <p class="text-gray-700 text-2xl font-bold"><?= $totalZones ?></p>
                </a>
<?php } ?>
            </div>

            <div class="mt-4">
                <h1 class="text-3xl font-semibold mb-4 text-gray-800 text-center">Statistiques</h1>
                <div class="container mx-auto">
                    <h2 class="text-xl font-semibold mb-2 text-gray-700">#Ventes par article</h2>
                <canvas id="chart-articles-vendus" class="w-full" onload="fetchArticlesSoldData()"></canvas>
                </div>
                <div class="container mx-auto">
                <h2 class="text-xl font-semibold mb-2 text-gray-700">#Ventes par mois</h2>
                <canvas id="chart-ventes-par-mois" class="w-full mt-4"></canvas>
                </div>
                <div class="container mx-auto">
                <h2 class="text-xl font-semibold mb-2 text-gray-700">#Ventes par client</h2>
                <canvas id="chart-ventes-par-client" class="w-full mt-4"></canvas>
                </div>
                <!--
                <canvas id="chart-ventes-par-categorie"></canvas>
                <canvas id="chart-ventes-par-zone"></canvas>-->
                
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script lang="javascript" >


    document.addEventListener('DOMContentLoaded', function() {
    // Graphique des articles vendus
        // make it descending
        // what to add to make it descending ?
        //  
        const chartArticlesVendus = document.getElementById('chart-articles-vendus').getContext('2d');
        new Chart(chartArticlesVendus, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($articlesVendusLabels); ?>, // Pass PHP data to JS 
                datasets: [{
                    label: 'Quantité vendue',
                    data: <?php echo json_encode($articlesVendusQuantities); ?>, // Pass PHP data to JS
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                responsive: true,
                descending: true

            }
        });

    // Graphique des ventes par mois

        const chartVentesParMois = document.getElementById('chart-ventes-par-mois').getContext('2d');
        new Chart(chartVentesParMois, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($ventesParMoisLabels); ?>, // Pass PHP data to JS
                datasets: [{
                    label: 'Ventes par mois',
                    data: <?php echo json_encode($ventesParMoisSales); ?>, // Pass PHP data to JS
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    // Graphique des ventes par clients

    const chartVentesParClient = document.getElementById('chart-ventes-par-client').getContext('2d');
        new Chart(chartVentesParClient, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($clientLabels); ?>, // Pass PHP data to JS
                datasets: [{
                    label: 'Ventes par client',
                    data: <?php echo json_encode($clientSales); ?>, // Pass PHP data to JS
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                    borderColor: 'rgba(153, 102, 255, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

    // Graphique des ventes par catégorie

        const chartVentesParCategorie = document.getElementById('chart-ventes-par-categorie').getContext('2d');
        new Chart(chartVentesParCategorie, {
            type: 'pie',
            data: {
                labels:  <?php echo json_encode($ventesParCategorieLabels); ?>, // Pass PHP data to JS
                datasets: [{
                    data: <?php echo json_encode($ventesParCategorieSales); ?>, // Pass PHP data to JS
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });





    // Graphique des ventes par zone

        const chartVentesParZone = document.getElementById('chart-ventes-par-zone').getContext('2d');
        new Chart(chartVentesParZone, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($ventesParZoneLabels); ?>, // Pass PHP data to JS
                datasets: [{
                    data: <?php echo json_encode($ventesParZoneSales); ?>, // Pass PHP data to JS
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)',
                        'rgba(153, 102, 255, 0.2)',
                        'rgba(255, 159, 64, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
    });

    </script>
</body>
</html>