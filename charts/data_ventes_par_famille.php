<?php
// charts/data_ventes_par_famille.php

require_once 'config/database.php';

$conn = dbConnect();

$familleCodes = isset($_GET['selected_familles']) ? $_GET['selected_familles'] : [];

$whereClause = "";
if (!empty($familleCodes)) {
    $placeholders = implode(',', array_fill(0, count($familleCodes), '?'));
    $whereClause = " WHERE f.famille_code IN ($placeholders)";
}

$sql = "SELECT f.Libelle AS Famille, SUM(cl.Quantité * cl.Prix_Unitaire + cl.TVA) AS TotalVentes
        FROM Familles f
        LEFT JOIN Articles a ON f.famille_code = a.famille_code
        LEFT JOIN Commande_Ligne cl ON a.Article_Code = cl.Article_Code
        $whereClause
        GROUP BY f.Libelle";
$stmt = $conn->prepare($sql);

$stmt->execute($familleCodes); 

$ventesParFamille = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels = array_column($ventesParFamille, 'Famille'); 
$sales = array_column($ventesParFamille, 'TotalVentes'); 

?> 

<canvas id="famillesChart" width="400" height="200"></canvas> 
<script>
    const ctx = document.getElementById('famillesChart').getContext('2d'); 
    const myChart = new Chart(ctx, {
        type: 'pie', 
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Total des ventes', 
                data: <?php echo json_encode($sales); ?>,
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
