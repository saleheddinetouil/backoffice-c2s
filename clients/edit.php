<?php
// clients/edit.php

// Inclusion du fichier de configuration de la base de données
require_once 'config/database.php';

// Connexion à la base de données
$conn = dbConnect();

// Récupération du client à modifier
if (isset($_GET['id'])) {
    $CODECL = $_GET['id'];
    $sql = "SELECT * FROM Clients WHERE CODECL = :CODECL";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['CODECL' => $CODECL]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification si le client existe
    if (!$client) {
        echo "Client non trouvé.";
        exit();
    }
} else {
    echo "ID du client manquant.";
    exit();
}

// Gestion de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $RSOC = $_POST['RSOC'];
    $ADR = $_POST['ADR'];
    $VILLE = $_POST['VILLE'];
    $PAYS = $_POST['PAYS'];
    $EMAIL = $_POST['EMAIL'];
    $TEL = $_POST['TEL'];
    $FAX = $_POST['FAX'];
    $MF = $_POST['MF'];
    $zone_id = $_POST['zone_id'];
    $circuit_id = $_POST['circuit_id'];
    $client_type = $_POST['client_type'];

    // Validation des données (à ajouter)
    // ...

    // Préparation de la requête SQL de mise à jour
    $sql = "UPDATE Clients 
            SET RSOC = :RSOC, ADR = :ADR, VILLE = :VILLE, PAYS = :PAYS, 
                EMAIL = :EMAIL, TEL = :TEL, FAX = :FAX, MF = :MF, 
                zone_id = :zone_id, circuit_id = :circuit_id, client_type = :client_type 
            WHERE CODECL = :CODECL";
    $stmt = $conn->prepare($sql);

    // Exécution de la requête de mise à jour
    try {
        $stmt->execute([
            'RSOC' => $RSOC,
            'ADR' => $ADR,
            'VILLE' => $VILLE,
            'PAYS' => $PAYS,
            'EMAIL' => $EMAIL,
            'TEL' => $TEL,
            'FAX' => $FAX,
            'MF' => $MF,
            'zone_id' => $zone_id,
            'circuit_id' => $circuit_id,
            'client_type' => $client_type,
            'CODECL' => $CODECL
        ]);

        // Redirection vers la page de liste des clients
        header("Location: ?page=clients");
        exit();
    } catch (PDOException $e) {
        // Affichage d'un message d'erreur en cas d'échec de la mise à jour
        echo "Erreur lors de la modification du client : " . $e->getMessage();
    }
}

// Récupération des zones pour le menu déroulant
$sqlZones = "SELECT * FROM Zones";
$stmtZones = $conn->query($sqlZones);
$zones = $stmtZones->fetchAll(PDO::FETCH_ASSOC);

// Récupération des circuits pour le menu déroulant
$sqlCircuits = "SELECT * FROM Circuits";
$stmtCircuits = $conn->query($sqlCircuits);
$circuits = $stmtCircuits->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifier un client</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">                <div class="flex">
        <!-- Barre latérale -->
        <?php include 'components/sidebar.php'; ?>

        <div class="flex-1 p-4 ml-64">
            <!-- Barre de navigation -->
            <?php //include 'components/navbar.php'; ?>
    <div class="container mx-auto p-4">
        <h1 class="text-3xl font-bold mb-       4 text-center text-gray-800">Modifier le client : <?= $client['CODECL'] ?></h1>

        <!-- Formulaire de modification de client -->
        <form method="POST" action="?page=clients&action=edit&id=<?= $client['CODECL'] ?>" class="bg-white p-6 rounded-lg shadow-md">

            <!-- Champ RSOC -->
            <div class="mb-4">
                <label for="RSOC" class="block text-gray-700 text-sm font-bold mb-2">RSOC:</label>
                <input type="text" id="RSOC" name="RSOC" value="<?= $client['RSOC'] ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
            </div>

            <!-- Champ ADR -->
            <div class="mb-4">
                <label for="ADR" class="block text-gray-700 text-sm font-bold mb-2">Adresse:</label>
                <input type="text" id="ADR" name="ADR" value="<?= $client['ADR'] ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <!-- Champ VILLE -->
            <div class="mb-4">
                <label for="VILLE" class="block text-gray-700 text-sm font-bold mb-2">Ville:</label>
                <input type="text" id="VILLE" name="VILLE" value="<?= $client['VILLE'] ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <!-- Champ PAYS -->
            <div class="mb-4">
                <label for="PAYS" class="block text-gray-700 text-sm font-bold mb-2">Pays:</label>
                <input type="text" id="PAYS" name="PAYS" value="<?= $client['PAYS'] ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <!-- Champ EMAIL -->
            <div class="mb-4">
                <label for="EMAIL" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                <input type="email" id="EMAIL" name="EMAIL" value="<?= $client['EMAIL'] ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <!-- Champ TEL -->
            <div class="mb-4">
                <label for="TEL" class="block text-gray-700 text-sm font-bold mb-2">Téléphone:</label>
                <input type="tel" id="TEL" name="TEL" value="<?= $client['TEL'] ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <!-- Champ FAX -->
            <div class="mb-4">
                <label for="FAX" class="block text-gray-700 text-sm font-bold mb-2">Fax:</label>
                <input type="text" id="FAX" name="FAX" value="<?= $client['FAX'] ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <!-- Champ MF -->
            <div class="mb-4">
                <label for="MF" class="block text-gray-700 text-sm font-bold mb-2">MF:</label>
                <input type="text" id="MF" name="MF" value="<?= $client['MF'] ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <!-- Menu déroulant pour la zone -->
            <div class="mb-4">
                <label for="zone_id" class="block text-gray-700 text-sm font-bold mb-2">Zone:</label>
                <select name="zone_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php foreach ($zones as $zone): ?>
                        <option value="<?= $zone['zone_id'] ?>" <?= ($zone['zone_id'] == $client['zone_id']) ? 'selected' : '' ?>><?= $zone['Nom_Zone'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Menu déroulant pour le circuit -->
            <div class="mb-4">
                <label for="circuit_id" class="block text-gray-700 text-sm font-bold mb-2">Circuit:</label>
                <select name="circuit_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php foreach ($circuits as $circuit): ?>
                        <option value="<?= $circuit['circuit_id'] ?>" <?= ($circuit['circuit_id'] == $client['circuit_id']) ? 'selected' : '' ?>><?= $circuit['Nom_Circuit'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Boutons radio pour le type de client -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Type de client:</label>
                <div class="flex items-center">
                    <input type="radio" id="client" name="client_type" value="C" class="form-radio h-4 w-4 text-blue-600" <?= ($client['client_type'] == 'C') ? 'checked' : '' ?>>
                    <label for="client" class="ml-2 text-gray-700">Client</label>
                </div>
                <div class="flex items-center">
                    <input type="radio" id="supplier" name="client_type" value="F" class="form-radio h-4 w-4 text-blue-600" <?= ($client['client_type'] == 'F') ? 'checked' : '' ?>>
                    <label for="supplier" class="ml-2 text-gray-700">Fournisseur</label>
                </div>
            </div>

            <!-- Bouton de modification -->
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Modifier
            </button>
        </form>
    </div>
</div></div></body>
</html> 