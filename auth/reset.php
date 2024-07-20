<?php

// auth/reset.php

// php mailer google mail less secure app
// https://myaccount.google.com/lesssecureapps


function randomPassword() {
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array(); //remember to declare $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string

}

// Connexion à la base de données
require_once 'config/database.php';


$conn = dbConnect();

// Gestion de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des informations du formulaire
    $email = $_POST['email'];
    
    // Validation des informations (à ajouter)

    // Recherche de l'utilisateur dans la base de données
    $query = "SELECT * FROM Users WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Génération d'un nouveau mot de passe aléatoire
        $newPassword = randomPassword();

        // Mise a jour du mot de passe de l'utilisateur dans la base de données
        $query = "UPDATE Users SET password = :password WHERE user_id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':password', $newPassword);
        $stmt->bindParam(':id', $user['id']);
        $stmt->execute();

        // Envoi du nouveau mot de passe par email
        $to = $email;
        $subject = 'Nouveau mot de passe';
        $message = 'Bonjour,\n\nVotre nouveau mot de passe est : ' . $newPassword . "\n\nCordialement,\nL'équipe de C2S";
        $headers = 'From: reset@c2s.tn' . "\r\n" ;

        mail($to, $subject, $message, $headers);
        
        // smtp server requis

        // Redirection vers la page de connexion
        header('Location: /');
        exit;
    } else {
        // Affichage d'un message d'erreur si l'utilisateur n'existe pas
        echo 'Utilisateur non existant';
    }
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
            <h1 class="text-2xl font-bold mb-4 text-center text-gray-800">C2S - Reinitialisation du mot de passe</h1>

            <!-- Affichage des erreurs -->
            <?php if (isset($error)) { ?>
                <p class="text-red-500 mt-4"><?= $error ?></p>
            <?php } ?>
            
            <!-- Formulaire de connexion -->
            <form method="POST">
                <div class="mb-4">
                    <label for="email" class="block text-gray-700 font-bold mb-2">Adresse email</label>
                    <input type="email" id="email" name="email" class="w-full border border-gray-400 p-2 rounded" required>
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded ">Envoyer</button>
                    <a href="/" class="text-blue-500 hover:text-blue-700 font-bold">Retour</a>
                </div>
            </form>


        </div>
    </div>
</div></div></body>
</html>