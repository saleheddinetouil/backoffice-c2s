<?php
// auth/login.php

// Inclusion du fichier de configuration de la base de données
require_once 'config/database.php';

// Connexion à la base de données
$conn = dbConnect();

// Gestion de la soumission du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération du nom d'utilisateur et du mot de passe saisis
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Requête pour récupérer l'utilisateur correspondant au nom d'utilisateur saisi
    $sql = "SELECT * FROM Users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification de l'utilisateur et du mot de passe
    if ($user && password_verify($password, $user['password'])) {
        // Démarrage de la session
        session_start();
        $_SESSION['user_id'] = $user['user_id']; // Stockage de l'ID de l'utilisateur dans la session
        $_SESSION['role'] = $user['role']; // Stockage du rôle de l'utilisateur dans la session

        // Redirection vers la page d'accueil
        header('Location: /');
        exit();
    } else {
        // Message d'erreur en cas d'échec de connexion
        $loginError = "Nom d'utilisateur ou mot de passe incorrect."; 
    }
}

// if authenticated
if (isset($_SESSION['user_id'])) {
    header('Location: /');
    exit();
}


?>

<!DOCTYPE html>
<html>
<head>
    <title>Connexion - C2S</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-r from-blue-500 to-purple-500 font-sans">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <img src="assets/logo.jpeg" alt="Logo" class="h-16 w-auto mb-4 block mx-auto text-center">
            <h1 class="text-2xl font-bold mb-4 text-center text-gray-800">C2S - Connexion</h1>

            <!-- Affichage du message d'erreur si la connexion a échoué -->
            <?php if (isset($loginError)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Erreur!</strong>
                    <span class="block sm:inline"><?= $loginError ?></span>
                </div>
            <?php endif; ?>

            <!-- Formulaire de connexion -->
            <form method="POST" action="?page=auth&action=login">
                <!-- Champ de nom d'utilisateur -->
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Nom d'utilisateur:</label>
                    <input type="text" id="username" name="username" class="shadow appearance-none border border border-blue-500 rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>

                <!-- Champ de mot de passe -->
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Mot de passe:</label>
                    <input type="password" id="password" name="password" class="shadow appearance-none border border-blue-500 rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>

                <!-- Bouton de connexion -->
                <div class="flex items-center justify-center">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Se connecter
                    </button>
                </div>
                <!-- Mot de passe oublie ? -->
                <p class="text-center text-gray-700 mt-4">Mot de passe oublie ? <a href="?page=auth&action=reset" class="text-blue-500 hover:text-blue-700">Cliquez ici</a>.</p>
                
            </form>
        </div>
    </div>
</div></div></body>
</html>