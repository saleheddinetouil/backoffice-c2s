<?php
// charts/data_articles_vendus.php

require_once 'config/database.php';

$conn = dbConnect();

// Récupérer les codes d'article à partir de la requête GET
$articleCodes = isset($_GET['selected_articles']) ? $_GET['selected_articles'] : [];

// Construire la clause WHERE pour filtrer les articles
$whereClause = "";
if (!empty($articleCodes)) {
    $placeholders = implode(',', array_fill(0, count($articleCodes), '?'));
    $whereClause = " WHERE a.Article_Code IN ($placeholders)";
}

// Requête SQL pour récupérer les données des articles vendus
$sql = "SELECT a.Designation, SUM(cl.Quantité) AS QuantitéVendue
        FROM Articles a
        LEFT JOIN Commande_Ligne cl ON a.Article_Code = cl.Article_Code
        $whereClause
        GROUP BY a.Designation";
$stmt = $conn->prepare($sql);

// Exécuter la requête avec les codes d'article comme paramètres
$stmt->execute($articleCodes);

$articlesVendus = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels = array_column($articlesVendus, 'Designation');
$quantities = array_column($articlesVendus, 'QuantitéVendue');

// chiffre d'affaire pour chaque article vendu
$sql2 = "SELECT a.Designation, SUM(cl.Quantité*cl.Prix_Unitaire) AS ChiffreAffaire
        FROM Articles a
        LEFT JOIN Commande_Ligne cl ON a.Article_Code = cl.Article_Code
        $whereClause
        GROUP BY a.Designation";
$stmt2 = $conn->prepare($sql2);

// Exécuter la requête avec les codes d'article comme paramètres
$stmt2->execute($articleCodes);

$CAarticlesVendus = $stmt2->fetchAll(PDO::FETCH_ASSOC);

$CAlabels = array_column($CAarticlesVendus, 'Designation');
$CAquantities = array_column($CAarticlesVendus, 'ChiffreAffaire');


?>

<canvas id="articlesChart" width="400" height="200"></canvas>
<script>
    const ctx = document.getElementById('articlesChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Quantité Vendue',
                data: <?php echo json_encode($quantities); ?>,
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
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    const ctx2 = document.getElementById('CAarticlesChart').getContext('2d');
    const myChart2 = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($CAlabels); ?>,
            datasets: [{
                label: 'Chiffre d\'affaires',
                data: <?php echo json_encode($CAquantities); ?>,
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
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    

</script>
