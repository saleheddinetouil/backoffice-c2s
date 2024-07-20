<?php
// utilisateurs/edit.php

require_once 'config/database.php';

$conn = dbConnect();

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $sql = "SELECT * FROM Users WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "Utilisateur non trouvé.";
        exit();
    }
} else {
    echo "ID de l'utilisateur manquant.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone'];
    $role = $_POST['role'];
    $currentPassword = $_POST['current_password']; // Get current password
    $newPassword = $_POST['new_password']; // Get new password 
    $confirmPassword = $_POST['confirm_password']; // Get confirm password

    // Validation
    $errors = [];

    // 1. Check if the username is already taken (except for the current user)
    $sqlCheckUsername = "SELECT 1 FROM Users WHERE username = :username AND user_id != :user_id";
    $stmtCheckUsername = $conn->prepare($sqlCheckUsername);
    $stmtCheckUsername->execute(['username' => $username, 'user_id' => $user_id]);

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
            $sqlUpdate = "UPDATE Users SET username = :username, email = :email, telephone = :telephone, role = :role, password = :password WHERE user_id = :user_id"; 
            $params = [ 
                'username' => $username,
                'email' => $email,
                'telephone' => $telephone,
                'role' => $role, 
                'password' => $hashedPassword, // Use the new hashed password
                'user_id' => $user_id 
            ]; 
        } else { 
            $sqlUpdate = "UPDATE Users SET username = :username, email = :email, telephone = :telephone, role = :role WHERE user_id = :user_id"; 
            $params = [
                'username' => $username,
                'email' => $email,
                'telephone' => $telephone, 
                'role' => $role, 
                'user_id' => $user_id
            ];
        }

        $stmtUpdate = $conn->prepare($sqlUpdate); 

        try {
            $stmtUpdate->execute($params); 
            // Optionally update session variables after a successful update 
            $_SESSION['username'] = $username;
            // ... other session variables

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
   <title>Modifier l'utilisateur</title>
   <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> 
</head>
<body class="bg-gray-100 font-sans"> 
   <div class="flex h-screen"> 
       <?php include 'components/sidebar.php'; ?> 

       <div class="flex-1 ml-64 p-4 overflow-y-auto">
           <?php include 'components/navbar.php'; ?>

           <h1 class="text-3xl font-bold mb-4 text-center text-gray-800">Modifier l'utilisateur</h1>

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

           <form method="POST" action="?page=utilisateurs&action=edit&id=<?= $user['user_id'] ?>" class="bg-white p-6 rounded-lg shadow-md">
               <div class="mb-4">
                   <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Nom d'utilisateur:</label>
                   <input type="text" id="username" name="username" value="<?= $user['username'] ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required> 
               </div>
               <div class="mb-4">
                   <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                   <input type="email" id="email" name="email" value="<?= $user['email'] ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required> 
               </div>
               <div class="mb-4">
                   <label for="telephone" class="block text-gray-700 text-sm font-bold mb-2">Téléphone:</label>
                   <input type="tel" id="telephone" name="telephone" value="<?= $user['telephone'] ?>" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
               </div>
               <div class="mb-4"> 
                   <label for="role" class="block text-gray-700 text-sm font-bold mb-2">Role:</label>
                   <select name="role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required> 
                       <option value="Administrateur" <?php if ($user['role'] == 'Administrateur') echo 'selected'; ?>>Administrateur</option>
                       <option value="Représentant" <?php if ($user['role'] == 'Représentant') echo 'selected'; ?>>Représentant</option> 
                   </select> 
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
               <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"> 
                   Enregistrer les modifications 
               </button>
           </form>
       </div>
   </div> 
</body> 
</html>