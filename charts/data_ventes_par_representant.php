<?php 
// charts/data_ventes_par_representant.php

require_once 'config/database.php'; 

$conn = dbConnect(); 

$representantIds = isset($_GET['selected_representants']) ? $_GET['selected_representants'] : []; 

$whereClause = "";
if (!empty($representantIds)) { 
    $placeholders = implode(',', array_fill(0, count($representantIds), '?')); 
    $whereClause = " WHERE u.user_id IN ($placeholders)"; 
} 

$sql = "SELECT u.username AS Representant, SUM(f.Total) AS TotalVentes 
        FROM Users u
        LEFT JOIN Commande_Entête ce ON u.user_id = ce.representant_id
        LEFT JOIN Factures f ON ce.Commande_ID = f.Commande_ID
        $whereClause
        GROUP BY u.username"; 
$stmt = $conn->prepare($sql);

$stmt->execute($representantIds);

$ventesParRepresentant = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels = array_column($ventesParRepresentant, 'Representant');
$sales = array_column($ventesParRepresentant, 'TotalVentes'); 

?>

<canvas id="representantsChart" width="400" height="200"></canvas> 
<script>
    const ctx = document.getElementById('representantsChart').getContext('2d'); 
    const myChart = new Chart(ctx, {
        type: 'bar', 
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{ 
                label: 'Total des ventes', 
                data: <?php echo json_encode($sales); ?>, 
                backgroundColor: 'rgba(75, 192, 192, 0.2)', 
                borderColor: 'rgba(75, 192, 192, 1)',
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
