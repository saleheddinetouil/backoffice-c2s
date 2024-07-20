<?php
// components/navbar.php

// Vérification de l'authentification
if (!isset($_SESSION['user_id'])) {
    header('Location: ?page=auth&action=login');
    exit();
}

// Récupération des informations de l'utilisateur
$sqlUser = "SELECT * FROM Users WHERE user_id = :user_id";
$stmtUser = $conn->prepare($sqlUser);
$stmtUser->execute(['user_id' => $_SESSION['user_id']]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

?>

<nav class="bg-white shadow-lg">
    <div class="container mx-auto px-6 py-4 flex justify-between items-center">
        <!-- Logo ou nom de l'application -->
        <a href="/" class="text-2xl font-bold text-gray-800">
            <img src="assets/logo.jpeg" alt="Logo" class="h-10">
            
        </a>

        <!-- Profil de l'utilisateur -->
        <div class="flex items-center">
            <!-- Image de profil arrondie onclick display menu profile and logout -->
            <img src="https://ui-avatars.com/api/?name=<?= urlencode($user['username']) ?>" alt="Profil" class="rounded-full h-10 w-10 object-cover cursor-pointer" onclick="toggleMenu()">
           
        </div>
    </div>
</nav>
<script>


</script>