<?php
// charts/data_commandes_par_mois.php

require_once 'config/database.php';

$conn = dbConnect();

// Get selected months from the request
$selectedMonths = isset($_GET['selected_mois']) ? $_GET['selected_mois'] : []; 

$whereClause = "";
if (!empty($selectedMonths)) {
    $placeholders = implode(',', array_fill(0, count($selectedMonths), '?'));
    $whereClause = " AND MONTH(ce.Date_Commande) IN ($placeholders)"; 
}

$sql = "SELECT MONTH(ce.Date_Commande) AS Mois, SUM(cl.Quantité * cl.Prix_Unitaire + cl.TVA) AS TotalVentes
        FROM Commande_Entête ce
        JOIN Commande_Ligne cl ON ce.Commande_ID = cl.Commande_ID 
        WHERE YEAR(ce.Date_Commande) = YEAR(GETDATE()) 
        $whereClause
        GROUP BY MONTH(ce.Date_Commande) 
        ORDER BY MONTH(ce.Date_Commande)"; 
$stmt = $conn->prepare($sql); 
$stmt->execute($selectedMonths); 

$ventesParMois = $stmt->fetchAll(PDO::FETCH_ASSOC); 

$labels = array_map(function ($row) {
    return date('F', mktime(0, 0, 0, $row['Mois'], 1)); 
}, $ventesParMois); 
$sales = array_column($ventesParMois, 'TotalVentes'); 

?> 

<canvas id="commandesParMoisChart" width="400" height="200"></canvas>
<script>
    const ctx = document.getElementById('commandesParMoisChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Total des ventes', 
                data: <?php echo json_encode($sales); ?>, 
                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                borderColor: 'rgba(255, 159, 64, 1)',
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