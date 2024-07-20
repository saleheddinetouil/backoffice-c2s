<?php
// profile/edit.php

require_once 'config/database.php';

$conn = dbConnect();

// Check if the user is logged in 
if (!isset($_SESSION['user_id'])) { 
    header('Location: ?page=auth&action=login'); 
    exit();
} 

// Get user details
$userId = $_SESSION['user_id']; 
$sql = "SELECT * FROM Users WHERE user_id = :user_id";
$stmt = $conn->prepare($sql); 
$stmt->execute(['user_id' => $userId]); 
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) { 
    echo "Utilisateur non trouvé."; 
    exit();
}

// Handle form submission 
if ($_SERVER['REQUEST_METHOD'] === 'POST'&& isset($_POST['submit'])) {
    $username = $_POST['username']; 
    $email = $_POST['email']; 
    $telephone = $_POST['telephone'];
    $currentPassword = $_POST['current_password']; 
    $newPassword = $_POST['new_password']; 
    $confirmPassword = $_POST['confirm_password']; 

    // Validation 
    $errors = [];

    // 1. Check if the username is already taken (except for the current user) 
    $sqlCheckUsername = "SELECT 1 FROM Users WHERE username = :username AND user_id != :user_id";
    $stmtCheckUsername = $conn->prepare($sqlCheckUsername); 
    $stmtCheckUsername->execute(['username' => $username, 'user_id' => $userId]); 

    if ($stmtCheckUsername->rowCount() > 0) {
        $errors[] = "Ce nom d'utilisateur est déjà utilisé.";
    }

    // 2. Basic Email Validation (you might need more robust validation) 
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { 
        $errors[] = "Veuillez saisir une adresse e-mail valide."; 
    }

    // 3. Password validation (if the user wants to change it) 
    if (!empty($newPassword)) {
        if (strlen($newPassword) < 8) {
            $errors[] = "Le mot de passe doit comporter au moins 8 caractères."; 
        }

        if ($newPassword !== $confirmPassword) {
            $errors[] = "Les mots de passe ne correspondent pas."; 
        }

        // Verify current password 
        if (!password_verify($currentPassword, $user['password'])) { 
            $errors[] = "Le mot de passe actuel est incorrect.";
        }
    }

    // If there are no errors, update the user profile 
    if (empty($errors)) { 
        // Update the SQL query based on whether the password is being changed 
        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); 
            $sqlUpdate = "UPDATE Users SET username = '" . $username . "', email = '" . $email . "', telephone = '" . $telephone . "', password = '" . $hashedPassword . "' WHERE user_id = " . $userId;
            
        } else { 
            $sqlUpdate = "UPDATE Users SET username = '" . $username . "', email = '" . $email . "', telephone = '" . $telephone . "' WHERE user_id = " . $userId;
        }

        $stmtUpdate = $conn->prepare($sqlUpdate);

        try {
            $stmtUpdate->execute();
            // Optionally update the session username after successful update
            $_SESSION['username'] = $username; 
            $successMessage = "Profil mis à jour avec succès!";
        } catch (PDOException $e) {
            $errors[] = "Erreur lors de la mise à jour du profil: " . $e->getMessage(); 
        } 
    } 
} 
?>


<!DOCTYPE html>
<html>
<head>
    <title>Modifier mon profil</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> 
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex h-screen">
        <?php include 'components/sidebar.php'; ?> 

        <div class="flex-1 ml-64 p-4 overflow-y-auto">

            <h1 class="text-3xl font-bold mb-4 text-center text-gray-800">Modifier mon profil</h1> 

            <!-- Display success message -->
            <?php if (isset($successMessage)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?= $successMessage ?></span> 
                </div> 
            <?php endif; ?>

            <!-- Display errors --> 
            <?php if (!empty($errors)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <strong class="font-bold">Erreur!</strong> 
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?> 
                    </ul>
                </div> 
            <?php endif; ?> 

            <form method="POST" action="?page=profile&action=edit" class="bg-white p-6 rounded-lg shadow-md">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Nom d'utilisateur:</label>
                    <input type="text" id="username" name="username" value="<?= $user['username'] ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                    <input type="email" id="email" name="email" value="<?= $user['email'] ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required> 
                </div>
                <div class="mb4">
                    <label for="telephone" class="block text-gray-700 text-sm font-bold mb-2">Telephone:</label>
                    <input type="text" id="phone" name="telephone" value="<?= $user['telephone'] ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" length="8">
                </div>
                <div class="mb-4"> 
                    <label for="current_password" class="block text-gray-700 text-sm font-bold mb-2">Mot de passe actuel:</label> 
                    <input type="password" id="current_password" name="current_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div> 
                <div class="mb-4">
                    <label for="new_password" class="block text-gray-700 text-sm font-bold mb-2">Nouveau mot de passe:</label>
                    <input type="password" id="new_password" name="new_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <div class="mb-4"> 
                    <label for="confirm_password" class="block text-gray-700 text-sm font-bold mb-2">Confirmer le nouveau mot de passe:</label> 
                    <input type="password" id="confirm_password" name="confirm_password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                </div>
                <button type="submit" name="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Enregistrer les modifications
                </button>
            </form> 
        </div>
    </div>
</body> 
</html>