<?php
// utilisateurs/create.php

require_once 'config/database.php';

$conn = dbConnect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $telephone = $_POST['telephone']; 
    $role = $_POST['role'];

    // Hash the password 
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT); 

    // Prepare and execute the SQL query 
    $sql = "INSERT INTO Users (username, password, email, telephone, role) VALUES (:username, :password, :email, :telephone, :role)";
    $stmt = $conn->prepare($sql); 

    try {
        $stmt->execute([
            'username' => $username,
            'password' => $hashedPassword,
            'email' => $email,
            'telephone' => $telephone, 
            'role' => $role 
        ]);

        // Redirect to the users list page 
        header("Location: ?page=utilisateurs");
        exit(); 
    } catch (PDOException $e) {
        echo "Error creating user: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
   <title>Créer un utilisateur</title>
   <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"> 
</head>
<body class="bg-gray-100 font-sans"> 
   <div class="flex h-screen"> 
       <?php include 'components/sidebar.php'; ?> 

       <div class="flex-1 ml-64 p-4 overflow-y-auto">
           <?php include 'components/navbar.php'; ?>

           <h1 class="text-3xl font-bold mb-4 text-center text-gray-800">Créer un utilisateur</h1>

           <form method="POST" action="?page=utilisateurs&action=create" class="bg-white p-6 rounded-lg shadow-md">
               <div class="mb-4">
                   <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username:</label> 
                   <input type="text" id="username" name="username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required> 
               </div>
               <div class="mb-4">
                   <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password:</label>
                   <input type="password" id="password" name="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required> 
               </div>
               <div class="mb-4">
                   <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
                   <input type="email" id="email" name="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
               </div>
               <div class="mb-4">
                   <label for="telephone" class="block text-gray-700 text-sm font-bold mb-2">Téléphone:</label>
                   <input type="tel" id="telephone" name="telephone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"> 
               </div> 
               <div class="mb-4">
                   <label for="role" class="block text-gray-700 text-sm font-bold mb-2">Role:</label> 
                   <select name="role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required> 
                       <option value="Administrateur">Administrateur</option> 
                       <option value="Représentant">Représentant</option> 
                   </select>
               </div>
               <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                   Créer
               </button> 
           </form>
       </div>
   </div>
</body> 
</html>