<?php
// charts/data_ventes_par_zone.php

require_once 'config/database.php';

$conn = dbConnect();

// Récupérer les IDs de zone à partir de la requête GET
$zoneIds = isset($_GET['selected_zones']) ? $_GET['selected_zones'] : [];

// Construire la clause WHERE pour filtrer les zones
$whereClause = "";
if (!empty($zoneIds)) {
    $placeholders = implode(',', array_fill(0, count($zoneIds), '?'));
    $whereClause = " WHERE z.zone_id IN ($placeholders)";
}

// Requête SQL pour récupérer les données des ventes par zone
$sql = "SELECT z.Nom_Zone AS Zone, SUM(f.Total) AS TotalVentes
        FROM Zones z
        LEFT JOIN Clients cl ON z.zone_id = cl.zone_id
        LEFT JOIN Commande_Entête ce ON cl.CODECL = ce.CODECL
        LEFT JOIN Factures f ON ce.Commande_ID = f.Commande_ID
        $whereClause
        GROUP BY z.Nom_Zone";
$stmt = $conn->prepare($sql);

// Exécuter la requête avec les IDs de zone comme paramètres
$stmt->execute($zoneIds);

$ventesParZone = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels = array_column($ventesParZone, 'Zone');
$sales = array_column($ventesParZone, 'TotalVentes');

?>
<div class="container mx-auto p-6 flex flex-col justify-center">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Ventes par zone</h1>
<canvas id="zonesChart" class="w-full align-center text-center" width="400" height="200"></canvas>
</div>
<script>
    const ctx = document.getElementById('zonesChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'doughnut',
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
