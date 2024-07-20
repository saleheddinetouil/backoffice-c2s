<?php
// charts/index.php

require_once 'config/database.php';

$conn = dbConnect();

// Fetch data for selection tables
$sqlArticles = "SELECT Article_Code, Designation FROM Articles";
$stmtArticles = $conn->query($sqlArticles);
$articles = $stmtArticles->fetchAll(PDO::FETCH_ASSOC);

$sqlClients = "SELECT CODECL, RSOC FROM Clients";
$stmtClients = $conn->query($sqlClients);
$clients = $stmtClients->fetchAll(PDO::FETCH_ASSOC);

$sqlZones = "SELECT zone_id, Nom_Zone FROM Zones";
$stmtZones = $conn->query($sqlZones);
$zones = $stmtZones->fetchAll(PDO::FETCH_ASSOC);

$sqlUsers = "SELECT user_id, username FROM Users WHERE role = 'Représentant'"; // Get only representatives
$stmtUsers = $conn->query($sqlUsers); 
$users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);

$sqlFamilles = "SELECT famille_code, Libelle FROM Familles";
$stmtFamilles = $conn->query($sqlFamilles);
$familles = $stmtFamilles->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html>
<head>
    <title>GestionVente - Statistiques</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex">
        <?php include 'components/sidebar.php'; ?>

        <div class="flex-1 p-4 ml-64 overflow-y-auto">
            <?php //include 'components/navbar.php'; ?>

            <h1 class="text-3xl font-bold mb-4 text-center text-gray-800 p-12">Statistiques</h1>

            <div class="mb-4">
                <form method="GET" action="?page=charts" id="statsForm" class="bg-white p-6 rounded-lg shadow-md">
                    <input type="hidden" name="page" value="charts">
                    <div class="flex items-center mb-4">
                        <select id="chartType" name="chart" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                            
                        <option value="" >Choisir un filtre</option>
                        <option value="articles" <?php if(isset($_GET['chart'])&&$_GET['chart']=='articles'){echo "selected"; } ?>>Ventes par article</option>
                            <option value="clients" <?php if(isset($_GET['chart'])&&$_GET['chart']=='clients'){echo "selected"; } ?>>Ventes par client</option>
                            <option value="zones" <?php if(isset($_GET['chart'])&&$_GET['chart']=='zones'){echo "selected"; } ?>>Ventes par zone</option>
                            <option value="representants" <?php if(isset($_GET['chart'])&&$_GET['chart']=='representants'){echo "selected"; } ?>>Ventes par représentant</option>
                            <option value="commandes_par_mois" <?php if(isset($_GET['chart'])&&$_GET['chart']=='commandes_par_mois'){echo "selected"; } ?>>Commandes par mois</option>
                            <option value="familles" <?php if(isset($_GET['chart'])&&$_GET['chart']=='familles'){echo "selected"; } ?>>Ventes par famille</option>
                        </select>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline ml-2">Afficher les statistiques</button>
                    </div>

                    <div id="selectionTableContainer" class="<?php
                    if(isset($_GET['search'])&&!empty($_GET['search'])) {
                        echo '';
                    }
                    else {
                        echo 'hidden';
                    }
                    ?>">
                        <h2 class="text-xl font-semibold mb-2 text-gray-800">Choisir les éléments</h2>
                        <div class="mb-4">
                            <label for="search" class="block text-gray-700 text-sm font-bold mb-2">Rechercher:</label>
                            <input type="text" id="search" value="<?php
                            if(isset($_GET['search'])&&!empty($_GET['search'])) {
                                echo $_GET['search'];
                            }
                            ?>" name="search" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Rechercher...">
                        </div>
                        <table class="table-auto w-full" id="selectionTable">
                            <thead>
                                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-left">
                                        <input type="checkbox" id="selectAll" class="form-checkbox h-4 w-4 text-blue-600">
                                    </th>
                                    <th class="py-3 px-6 text-left">ID</th>
                                    <th class="py-3 px-6 text-left">Nom</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-800 text-sm font-light">
                                </tbody>
                        </table>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline mt-4">
                            Afficher les statistiques
                        </button>
                    </div>
                </form>
            </div>
            <div id="chart-container" class="mt-4 w-full">
                <!-- Chart container, dynamically populated with JS -->
                <?php
                if (isset($_GET['chart'])) {
                    $chartType = $_GET['chart'];
                    switch ($chartType) {
                        case 'articles':
                            include 'data_articles_vendus.php';
                            break;
                        case 'clients':
                            include 'data_ventes_par_client.php';
                            break;
                        case 'zones':
                            include 'data_ventes_par_zone.php';
                            break;
                        case 'representants':
                            include 'data_ventes_par_representant.php';
                            break;
                        case 'commandes_par_mois':
                            include 'data_commandes_par_mois.php';
                            break;
                        case 'familles':
                            include 'data_ventes_par_famille.php';
                            break;
                    }
                }
                ?>
            </div>
        </div>
    </div>

    <script>
        const chartTypeSelect = document.getElementById('chartType');
        const selectionTableContainer = document.getElementById('selectionTableContainer');
        const selectionTable = document.getElementById('selectionTable');
        const selectAllCheckbox = document.getElementById('selectAll');
        const statsForm = document.getElementById('statsForm');
        const searchInput = document.getElementById('search');

        // Load Chart.js after the DOM is ready
        window.addEventListener('DOMContentLoaded', function() {
            //  Chart for "Chiffre d'affaires par client"
            fetchClientsSalesData().then(data => {
                const canvas = document.createElement('canvas');
                canvas.id = 'myChart';
                document.getElementById('chart-container').appendChild(canvas);

                const ctx = canvas.getContext('2d');
                new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Total des ventes',
                            data: data.sales,
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
            });


        });

        // Dynamically populate the table and event listeners
        chartTypeSelect.addEventListener('change', function() {
            const selectedType = this.value;
            if (selectedType) {
                selectionTableContainer.classList.remove('hidden'); 
                
                if (selectedType === 'articles') {
                    populateSelectionTable('Article_Code', 'Designation', <?php echo json_encode($articles); ?>, 'selected_articles');
                } else if (selectedType === 'clients') {
                    populateSelectionTable('CODECL', 'RSOC', <?php echo json_encode($clients); ?>, 'selected_clients');
                } else if (selectedType === 'zones') {
                    populateSelectionTable('zone_id', 'Nom_Zone', <?php echo json_encode($zones); ?>, 'selected_zones');
                } else if (selectedType === 'representants') {
                    populateSelectionTable('user_id', 'username', <?php echo json_encode($users); ?>, 'selected_representants');
                } else if (selectedType === 'commandes_par_mois') {
                    const monthLabels = [
                        'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet',
                        'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
                    ];
                    //  Use `Array.from` instead of a loop:
                    //     - It does the same as the loop with only one line of code.  
                    //     - It is an alternative way to generate arrays of data  
                    const months = Array.from({ length: 12 }, (_, index) => index + 1); // Generate an array [1, 2, 3, ..., 12] 
                    // This is now all that's needed (the loop is simplified):
                    populateSelectionTable('', '', months, 'selected_mois', monthLabels); 
                }  else if (selectedType === 'familles') {
                    populateSelectionTable('famille_code', 'Libelle', <?php echo json_encode($familles); ?>, 'selected_familles');
                }
                addListeners();
            } else {
                selectionTableContainer.classList.add('hidden');
                statsForm.reset(); // Reset form for each new selection
                addListeners(); 
            }
        });

        // Helper function to populate the table
        function populateSelectionTable(idColumn, nameColumn, data, inputName, labels = null) {
            selectionTable.querySelector('tbody').innerHTML = ''; // Clear existing rows 
            const tbody = selectionTable.querySelector('tbody');

            data.forEach((item, index) => {
                const row = tbody.insertRow();
                const checkboxCell = row.insertCell();
                const idCell = row.insertCell();
                const nameCell = row.insertCell();

                let checkboxValue = idColumn ? item[idColumn] : index + 1; // Use index + 1 for months
                let name = nameColumn ? item[nameColumn] : (labels ? labels[index] : '');

                checkboxCell.innerHTML = `<input type="checkbox" name="${inputName}[]" value="${checkboxValue}" class="form-checkbox h-4 w-4 text-blue-600">`;
                idCell.textContent = checkboxValue; // Or display another identifier if needed
                nameCell.textContent = name;
            });

            addListeners(); 
        }

        // Add listeners for selectAll and individual checkboxes 
        function addListeners() { 
            const checkboxes = selectionTable.querySelectorAll('.form-checkbox'); 
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() { 
                    // Update the selectAll checkbox state based on individual checkboxes:
                    selectAllCheckbox.checked = Array.from(checkboxes).every(checkbox => checkbox.checked); 

                    // Handle chart loading based on selected checkboxes (we'll add this logic later)
                    if (this.checked) { 
                        // ... (If you have an "Afficher" button in the form, you'd re-enable it). 
                    } else { 
                        //  ... (If you have an "Afficher" button in the form, you'd re-enable it). 
                    } 
                }); 
            }); 

            searchInput.addEventListener('keyup', filterSelectionTable); // Search functionality for the selection table (same as before - update the search logic).
        } 


        function loadChartVentesParClient(clients) {
            fetch('data_ventes_par_client.php?selected_clients=' + encodeURIComponent(clients.join(',')))
                .then(response => response.json())
                .then(data => {
                    // ... Chart for "Chiffre d'affaires par client"

                    const chartVentesParClient = document.getElementById('chart-ventes-par-client').getContext('2d');
                    new Chart(chartVentesParClient, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Total des ventes',
                                data: data.sales,
                                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                                borderColor: 'rgba(153, 102, 255, 1)',
                                borderWidth: 1
                            }]
                        },
                    });
                });
        }

        function loadChartVentesParZone(zones) {
            fetch('data_ventes_par_zone.php?selected_zones=' + encodeURIComponent(zones.join(',')))
                .then(response => response.json())
                .then(data => {
                    const canvas = document.createElement('canvas');
                    canvas.id = 'chart-ventes-par-zone';
                    canvas.classList.add('w-full', 'mt-4');
                    document.getElementById('chart-container').appendChild(canvas);

                    const ctx = canvas.getContext('2d');
                    new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Ventes par zone',
                                data: data.sales,
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
                        }
                    });
                });
        }

        // You will need to implement the functions below based on your SQL queries
        function loadChartCommandesParMois(months) {
            // ... Chart for "Commandes par mois"
        }
        
        function loadChartVentesParRepresentant(representants) {
            // ... Chart for "Ventes par Représentant"
        }
        
        function loadChartVentesParFamille(familles) {
            // ... Chart for "Ventes par famille"
        }
 
        // Add the new script function below that was mentioned in previous response:

        function filterSelectionTable() { 
            const searchTerm = searchInput.value.toLowerCase();
            // Get all table rows
            const rows = selectionTable.querySelectorAll('tbody tr');

            // Loop through each row
            rows.forEach(row => {
                const nameCell = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                // Check if the row contains the search term
                if (nameCell.includes(searchTerm)) {
                    row.style.display = ''; // Show the row
                } else {
                    row.style.display = 'none'; // Hide the row
                }
            });
        }
        
        // (Same Javascript for checking all boxes - from previous responses -  you should have this!) 


        
       
    </script>
</body>
</html>