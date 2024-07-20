<?php
// components/sidebar.php

// Requête pour récupérer le nombre total de clients
$sqlTotalClients = "SELECT COUNT(*) as total_clients FROM Clients";
$stmtTotalClients = $conn->query($sqlTotalClients);
$totalClients = $stmtTotalClients->fetch(PDO::FETCH_ASSOC)['total_clients'];

// Requête pour récupérer le nombre total d'articles
$sqlTotalArticles = "SELECT COUNT(*) as total_articles FROM Articles";
$stmtTotalArticles = $conn->query($sqlTotalArticles);
$totalArticles = $stmtTotalArticles->fetch(PDO::FETCH_ASSOC)['total_articles'];

// Requête pour récupérer le nombre total de commandes
$sqlTotalCommandes = "SELECT COUNT(*) as total_commandes FROM Commande_Entête";
$stmtTotalCommandes = $conn->query($sqlTotalCommandes);
$totalCommandes = $stmtTotalCommandes->fetch(PDO::FETCH_ASSOC)['total_commandes'];

// Requête pour récupérer le nombre total de factures
$sqlTotalFactures = "SELECT COUNT(*) as total_factures FROM Factures";
$stmtTotalFactures = $conn->query($sqlTotalFactures);
$totalFactures = $stmtTotalFactures->fetch(PDO::FETCH_ASSOC)['total_factures'];

// Requête pour récupérer le nombre total de gammes
$sqlTotalGammes = 'SELECT COUNT(*) as total_gammes FROM Gammes';
$stmtTotalGammes = $conn->query($sqlTotalGammes);
$totalGammes = $stmtTotalGammes->fetch(PDO::FETCH_ASSOC)['total_gammes'];

// Requête pour récupérer le nombre total de familles
$sqlTotalFamilles = 'SELECT COUNT(*) as total_familles FROM Familles';
$stmtTotalFamilles = $conn->query($sqlTotalFamilles);
$totalFamilles = $stmtTotalFamilles->fetch(PDO::FETCH_ASSOC)['total_familles'];

// requete pour selectionner les utilisateurs
$sqlUsers = "SELECT * FROM Users";
$stmtUsers = $conn->query($sqlUsers);
$users = $stmtUsers->fetchAll(PDO::FETCH_ASSOC);
$totalUsers = count($users);

// Requête pour sélectionner les informations de l'utilisateur connecté
$sqlUser = "SELECT * FROM Users WHERE user_id = :user_id";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->execute(['user_id' => $_SESSION['user_id']]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

// requete pour zones 
$sqlZones = "SELECT * FROM Zones";
$stmtZones = $conn->query($sqlZones);
$zones = $stmtZones->fetchAll(PDO::FETCH_ASSOC);
$totalZones = count($zones);

// Requête pour circuits 
$sqlCircuits = "SELECT * FROM Circuits";
$stmtCircuits = $conn->query($sqlCircuits);
$circuits = $stmtCircuits->fetchAll(PDO::FETCH_ASSOC);
$totalCircuits = count($circuits);


// Retourner le role de l'Utilisateur 
$sqlGetRole = "SELECT * FROM Users WHERE user_id = '" . $_SESSION['user_id'] . "'";
$stmtGetRole = $conn->prepare($sqlGetRole);
$stmtGetRole->execute();
$result = $stmtGetRole->fetch(PDO::FETCH_ASSOC);
$role = $result['role'];



?>

<aside class="bg-gray-800 text-white w-64 fixed h-screen top-0 z-10 transition-transform duration-300 ease-in-out" id="sidebar">
    <div class="p-6 flex items-center justify-between">
        <a href="/" class="text-2xl font-bold text-white">C2S</a>
        <!-- Image de profil arrondie onclick display menu profile and logout -->
        <a href="?page=profile&action=edit" class="flex items-center">
        <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['username']) ?>" alt="Profil" class="rounded-full h-10 w-10 object-cover cursor-pointer" onclick="toggleMenu()">
        </a>
    </div>

    <ul class="list-none">

        <li class="px-6 py-3 hover:bg-gray-700">
            <a href="/" class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Tableau de bord
            </a>
        </li>
        <?php 
    if($role == "Administrateur"){
    ?>
        <li class="px-6 py-3 hover:bg-gray-700">
            <a href="?page=clients" class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
                Clients (<?= $totalClients ?>)
            </a>
        </li>
        <li class="px-6 py-3 hover:bg-gray-700">
    <a href="?page=utilisateurs" class="flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
        Utilisateurs (<?= $totalUsers ?>)
    </a>
</li>


        <li class="px-6 py-3 hover:bg-gray-700">
            <a href="?page=articles" class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                Articles (<?= $totalArticles ?>)
            </a>
        </li>
        <li class="px-6 py-3 hover:bg-gray-700">
            <a href="?page=gammes" class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                Gammes (<?= $totalGammes ?>)
            </a>
        </li>
        <li class="px-6 py-3 hover:bg-gray-700">
            <a href="?page=familles" class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                Familles (<?= $totalFamilles ?>)
            </a>
        </li>
        <li class="px-6 py-3 hover:bg-gray-700">
            <a href="?page=zones" class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Zones (<?= $totalZones ?>)
            </a>
        </li>
        <li class="px-6 py-3 hover:bg-gray-700">
            <a href="?page=circuits" class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Circuits (<?= $totalCircuits ?>)
            </a>
        </li>
        <li class="px-6 py-3 hover:bg-gray-700">
            <a href="?page=commandes" class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                Commandes (<?= $totalCommandes ?>)
            </a>
        </li>
        <li class="px-6 py-3 hover:bg-gray-700">
            <a href="?page=factures" class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Factures (<?= $totalFactures ?>)
            </a>
        </li>
        <li class="px-6 py-3 hover:bg-gray-700 relative group" id="mega-menu-dropdown-container">
            <a href="?page=charts" class="flex items-center" id ="mega-menu-button" >
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                </svg>
                Statistiques
            </a>
            
        </li>
        <?php }
else { ?>

        <li class="px-6 py-3 hover:bg-gray-700">
            <a href="?page=commandes" class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                Commandes (<?= $totalCommandes ?>)
            </a>
        </li>
        <li class="px-6 py-3 hover:bg-gray-700">
            <a href="?page=factures" class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Factures (<?= $totalFactures ?>)
            </a>
        </li>
        <?php } ?>  

    </ul>
    <!-- Footer at the bottom of the sidebar -->
    <div class="flex flex-col items-center justify-center p-6 border-t border-gray-700">
    <a href="?page=profile&action=edit" class="flex items-center mb-4">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        Mon Profil
    </a>

        <a href="?page=auth&action=logout" class="flex items-center mb-2">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            Deconnexion
        </a>
    </div>

</aside>
