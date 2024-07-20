<?php
// commandes/edit.php

require_once 'config/database.php';

$conn = dbConnect();

if (isset($_GET['id'])) {
    $Commande_ID = $_GET['id'];

    // Fetch existing commande details
    $sql = "SELECT * FROM Commande_Entête WHERE Commande_ID = :Commande_ID";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['Commande_ID' => $Commande_ID]);
    $commande = $stmt->fetch(PDO::FETCH_ASSOC);



    if (!$commande) {
        echo "Commande non trouvée.";
        exit();
    }
    $representant_id = $commande['representant_id'];
    $sql = 'SELECT * FROM Users WHERE user_id = :representant_id ';
    $stmt = $conn->prepare($sql);
    $stmt->execute(['representant_id' => $representant_id]);
    $representant = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$representant) {
        echo "Representant non trouvé.";
        exit();
    }   
    $representant_name = $representant['username'];
} else {
    echo "ID de la commande manquant.";
    exit();
}


// Handle ligne_commande additions or updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    if (isset($_POST['add_articles'])) {
        $selectedArticles = isset($_POST['selected_articles']) ? $_POST['selected_articles'] : [];

        foreach ($selectedArticles as $Article_Code) {
            $Quantité = $_POST['quantite_' . $Article_Code];

            $sqlArticle = "SELECT Prix_Unitaire, TVA FROM Articles WHERE Article_Code = '" . $Article_Code . "'";
            $stmtArticle = $conn->prepare($sqlArticle);
            $stmtArticle->execute();
            $article = $stmtArticle->fetch(PDO::FETCH_ASSOC);

            $TVA = ($Quantité * $article['Prix_Unitaire']) * ($article['TVA'] / 100);

            // Check if the article already exists in the order lines
            $sqlCheck = "SELECT Ligne_ID FROM Commande_Ligne WHERE Commande_ID = '". $Commande_ID ."' AND Article_Code = '". $Article_Code ."'";
            $stmtCheck = $conn->prepare($sqlCheck);
            $stmtCheck->execute();

            if ($stmtCheck->rowCount() > 0) {
                // If the article already exists, update the quantity
                $sqlLigne = "UPDATE Commande_Ligne SET Quantité = Quantité + '". $Quantité ."', TVA = TVA + '". $TVA ."' WHERE Commande_ID = '". $Commande_ID ."' AND Article_Code = '". $Article_Code ."'";
               
            } else {
                // If it's a new article, insert a new line
                $sqlLigne = "INSERT INTO Commande_Ligne (Commande_ID, Article_Code, Quantité, Prix_Unitaire, TVA) 
                             VALUES ('". $Commande_ID ."', '". $Article_Code ."', '". $Quantité ."', '". $article['Prix_Unitaire'] ."', '". $TVA ."')";
            }

            $stmtLigne = $conn->prepare($sqlLigne);

            try {
                $stmtLigne->execute();
                // if article added or updated successfully remove quantity from stock
                $sqlStock = "UPDATE Articles SET Quantité_Stock = Quantité_Stock - '". $Quantité ."' WHERE Article_Code = '". $Article_Code ."'";
                $stmtStock = $conn->prepare($sqlStock);
                $stmtStock->execute();
                
            } catch (PDOException $e) {
                echo "Erreur lors de l'ajout/modification de la ligne de commande : " . $e->getMessage();
            }
        }

        header("Location: ?page=commandes&action=edit&id=$Commande_ID");
        exit();
    } elseif (isset($_POST['update_quantities'])) {
        $ligne_id = $_POST['ligne_id'];
        $nouvelle_quantite = $_POST['nouvelle_quantite'];

        // Get the current article details to recalculate TVA
        $sqlArticle = "SELECT a.Prix_Unitaire, a.TVA FROM Articles a
                        JOIN Commande_Ligne cl ON a.Article_Code = cl.Article_Code 
                        WHERE cl.Ligne_ID = '" . $ligne_id . "'";
        $stmtArticle = $conn->prepare($sqlArticle); 
        $stmtArticle->execute(); 
        $article = $stmtArticle->fetch(PDO::FETCH_ASSOC);

        // Calculate new TVA
        echo $article['Prix_Unitaire'];
        echo $article['TVA'];
        $TVA = ($nouvelle_quantite * $article['Prix_Unitaire']) * ($article['TVA'] / 100);

        // Update quantity and TVA for the order line
        $sqlUpdate = "UPDATE Commande_Ligne SET Quantité = '" . $nouvelle_quantite . "', TVA = '" . $TVA . "' WHERE Ligne_ID = '" . $ligne_id . "'";
        $stmtUpdate = $conn->prepare($sqlUpdate);

        try {
            $stmtUpdate->execute();
        } catch (PDOException $e) {
            echo "Erreur lors de la mise à jour de la quantité: " . $e->getMessage(); 
        }

        header("Location: ?page=commandes&action=edit&id=$Commande_ID"); 
        exit(); 
    } elseif (isset($_POST['create_facture'])) {
        // Check if a facture already exists for this commande
        $sqlFactureExists = "SELECT 1 FROM Factures WHERE Commande_ID = :Commande_ID";
        $stmtFactureExists = $conn->prepare($sqlFactureExists);
        $stmtFactureExists->execute(['Commande_ID' => $Commande_ID]);
        $factureExists = $stmtFactureExists->fetchColumn();
        


        if (!$factureExists) {
            // If a facture does not exist, calculate the total and create it
            $sqlTotal = "SELECT SUM(Quantité * Prix_Unitaire + TVA) AS Total 
                        FROM Commande_Ligne 
                        WHERE Commande_ID = :Commande_ID";
            $stmtTotal = $conn->prepare($sqlTotal);
            $stmtTotal->execute(['Commande_ID' => $Commande_ID]);
            $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['Total'];

            $sqlFacture = "INSERT INTO Factures (Commande_ID, Total) VALUES (:Commande_ID, :Total)";
            $stmtFacture = $conn->prepare($sqlFacture);

            try {
                $stmtFacture->execute(['Commande_ID' => $Commande_ID, 'Total' => $total]);

                echo "Facture créée avec succès.";
            } catch (PDOException $e) {
                echo "Erreur lors de la création de la facture : " . $e->getMessage();
            }
        } else {
            // If a facture already exists delete it and create a new one
            $sqlDelete = "DELETE FROM Factures WHERE Commande_ID = :Commande_ID";
            $stmtDelete = $conn->prepare($sqlDelete);
            $stmtDelete->execute(['Commande_ID' => $Commande_ID]);

            $sqlTotal = "SELECT SUM(Quantité * Prix_Unitaire + TVA) AS Total 
                        FROM Commande_Ligne 
                        WHERE Commande_ID = :Commande_ID";
            $stmtTotal = $conn->prepare($sqlTotal);
            $stmtTotal->execute(['Commande_ID' => $Commande_ID]);
            $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['Total'];

            $sqlFacture = "INSERT INTO Factures (Commande_ID, Total) VALUES (:Commande_ID, :Total)";
            $stmtFacture = $conn->prepare($sqlFacture);


            try {
                $stmtFacture->execute(['Commande_ID' => $Commande_ID, 'Total' => $total]);

            } catch (PDOException $e) {
                echo "Erreur lors de la création de la facture : " . $e->getMessage();
            }

        }


    }
    elseif(isset($_POST['delete_commande'])) {
        $sqlDelete = "DELETE FROM Commande_Ligne WHERE Commande_ID = '". $Commande_ID ."'";

        $stmtDelete = $conn->prepare($sqlDelete);

        try {

            $stmtDelete->execute();

            $sqlDelete = "DELETE FROM Commande_Entête WHERE Commande_ID = '". $Commande_ID ."'";

            $stmtDelete = $conn->prepare($sqlDelete);

            $stmtDelete->execute();

            header("Location: ?page=commandes");

        } catch (PDOException $e) {
            echo "Erreur lors de la suppression de la commande : " . $e->getMessage();
        }
    }
}

// Handle ligne_commande deletion (Moved outside the POST check)
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['delete_ligne']) && isset($_GET['ligne_id'])) {
    $ligne_id = $_GET['ligne_id'];

    $sqlDeleteLigne = "DELETE FROM Commande_Ligne WHERE Ligne_ID = :ligne_id";
    $stmtDeleteLigne = $conn->prepare($sqlDeleteLigne);

    try {
        $stmtDeleteLigne->execute(['ligne_id' => $ligne_id]);

        // Redirect back to the edit page after deleting the line
        header("Location: ?page=commandes&action=edit&id=$Commande_ID");
        exit();
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression de la ligne de commande : " . $e->getMessage();
    }
}
// Get the client associated with the Commande
$sqlClient = "SELECT cl.RSOC FROM Clients cl JOIN Commande_Entête ce ON cl.CODECL = ce.CODECL WHERE ce.Commande_ID = :Commande_ID";
$stmtClient = $conn->prepare($sqlClient);
$stmtClient->execute(['Commande_ID' => $Commande_ID]);
$clientName = $stmtClient->fetch(PDO::FETCH_ASSOC)['RSOC'];

// Fetch existing ligne_commande for this Commande
$sqlLignes = "SELECT cl.*, a.Designation FROM Commande_Ligne cl JOIN Articles a ON cl.Article_Code = a.Article_Code WHERE Commande_ID = :Commande_ID";
$stmtLignes = $conn->prepare($sqlLignes);
$stmtLignes->execute(['Commande_ID' => $Commande_ID]);
$lignesCommande = $stmtLignes->fetchAll(PDO::FETCH_ASSOC);

// Get all articles for the modal
$sqlArticles = "SELECT * FROM Articles";
$stmtArticles = $conn->query($sqlArticles);
$articles = $stmtArticles->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Modifier la commande</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 font-sans">
                    <div class="flex">
        <?php include 'components/sidebar.php'; ?>

        <div class="flex-1 p-4 ml-64">
            <?php //include 'components/navbar.php'; ?>

            <h1 class="text-3xl font-bold mb-4 text-center text-gray-800 ">Modifier la commande n° <?= $Commande_ID; ?></h1>
            <h2 class="text-xl font-semibold mb-2 text-gray-700">Client: <?= $clientName; ?></h2>
            <h3 class="text-lg font-medium mb-2 text-gray-700">Représentant: <?= $representant_name ?></h3>

            <!-- Display Existing Order Lines -->
            <div class="overflow-x-auto mb-4">
                <table class="table-auto w-full">
                    <thead>
                        <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                            <th class="py-3 px-6 text-left">Article</th>
                            <th class="py-3 px-6 text-right">Prix Unitaire</th>
                            <th class="py-3 px-6 text-right">Quantité</th>
                            <th class="py-3 px-6 text-right">TVA</th>
                            <th class="py-3 px-6 text-right">Total TTC</th>
                            <th class="py-3 px-6 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-800 text-sm font-light">
                        <?php foreach ($lignesCommande as $ligne): ?>
                            <tr class="border-b border-gray-200 hover:bg-gray-100">
                                <td class="py-3 px-6 text-left whitespace-nowrap"><?= $ligne['Designation'] ?></td>
                                <td class="py-3 px-6 text-right"><?= number_format($ligne['Prix_Unitaire'], 2) ?></td>
                                <td class="py-3 px-6 text-right">
                                    <form method="POST" action="?page=commandes&action=edit&id=<?= $Commande_ID; ?>">
                                        <input type="hidden" name="ligne_id" value="<?= $ligne['Ligne_ID']; ?>">
                                        <input type="number" name="nouvelle_quantite" min="1" value="<?= $ligne['Quantité'] ?>" class="shadow appearance-none border rounded py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                                        <button type="submit" name="update_quantities" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-2 rounded focus:outline-none focus:shadow-outline ml-2">
                                            Mettre à jour
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3 px-6 text-right"><?= number_format($ligne['TVA'], 2) ?></td>
                                <td class="py-3 px-6 text-right"><?= number_format(($ligne['Quantité'] * $ligne['Prix_Unitaire']) + $ligne['TVA'], 2) ?></td>
                                <td class="py-3 px-6 text-center">
                                    <a href="?page=commandes&action=edit&id=<?= $Commande_ID; ?>&delete_ligne=true&ligne_id=<?= $ligne['Ligne_ID']; ?>" class="text-red-600 hover:text-red-800" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette ligne ?')">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- "Add Articles" button -->
            <button type="button" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" onclick="openModal(<?= $Commande_ID ?>)">
                Ajouter des Articles
            </button>

            <!-- Create Facture Button -->
            <form method="POST" action="?page=commandes&action=edit&id=<?= $Commande_ID; ?>" class="inline-block ml-4">
                <button type="submit" name="create_facture" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Créer Facture
                </button>
            </form>
<?php if(isset($factureID)): ?>
            <!-- Voir facture Button -->
                <a href="?page=factures&action=view&id=<?= $factureID+1; ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Voir Facture
                </a>
<?php endif; ?>

            <!-- Button voir bon de commande -->
            <a href="?page=commandes&action=view&id=<?= $Commande_ID; ?>" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Voir la Commande
            </a>

            <!-- Modal (Included after the table) -->
            <?php include('create_ligne.php'); ?>

            <script>
                //  ...(The JavaScript from the previous responses)
                
                // ...
                // Updated openModal function to set the commande ID in the modal form
                function openModal(commandeId) {
                    document.querySelector('#modalLigneCommande form').action = '?page=commandes&action=edit&id=' + commandeId;
                    document.getElementById('modalLigneCommande').style.display = 'block';
                }

            </script>
        </div>
    </div>
    <script>
        function deleteLigne(commande_id){
            if (confirm('Voulez-vous supprimer cette ligne de commande ?')) {
                window.location.href = '?page=commandes&action=edit&id=' + commande_id +'&delete_ligne=';
            }
        }
    </script>
</body>
</html>