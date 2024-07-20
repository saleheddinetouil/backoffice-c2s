<?php
// charts/data_ventes_par_client.php

require_once 'config/database.php';

$conn = dbConnect();

// Récupérer les codes client à partir de la requête GET
$clientCodes = isset($_GET['selected_clients']) ? $_GET['selected_clients'] : [];

// Construire la clause WHERE pour filtrer les clients
$whereClause = "";
if (!empty($clientCodes)) {
    $placeholders = implode(',', array_fill(0, count($clientCodes), '?'));
    $whereClause = " WHERE cl.CODECL IN ($placeholders)";
}

// Requête SQL pour récupérer les données des ventes par client
$sql = "SELECT cl.RSOC AS Client, SUM(f.Total) AS TotalVentes
        FROM Clients cl
        LEFT JOIN Commande_Entête ce ON cl.CODECL = ce.CODECL
        LEFT JOIN Factures f ON ce.Commande_ID = f.Commande_ID
        $whereClause
        GROUP BY cl.RSOC";
$stmt = $conn->prepare($sql);

// Exécuter la requête avec les codes client comme paramètres
$stmt->execute($clientCodes);

$ventesParClient = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels = array_column($ventesParClient, 'Client');
$sales = array_column($ventesParClient, 'TotalVentes');

?>

<canvas id="clientsChart" width="200" height="200"></canvas>
<script>
    const ctx = document.getElementById('clientsChart').getContext('2d');
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
    });
</script>
