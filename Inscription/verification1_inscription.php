<?php
// Connexion à la base de données
$host = 'localhost';
$dbname = 'website_db';
$user = 'root';
$password = '';

//import des fonctions
include 'fonctionInscription.php';

// Connexion à la base de données
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

// Vérification de l'envoi du formulaire
if (isset($_POST['part1'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

// Appel de la fonction pour vérifier l'email
    if (! Verif_mail($pdo, $email)) {
        // Email déjà utilisé
        session_start();
        $_SESSION['EmailUnicityError'] = true;
        header('Location: Form1_inscription.php#modal');
        exit();
    } else {
        // Si pas d'erreur, on passe à la page suivante
        session_start();
        $_SESSION['email'] = $email;
        $_SESSION['password'] = password_hash($password, PASSWORD_DEFAULT); // Hachage du mot de passe
        if(!isHashedPassword($_SESSION['password'])) {
            $_SESSION['HashedPasswordError'] = true;
            header("Location: Form1_inscription.php#modal");
        }
        $_SESSION['role'] = $role;

        // Redirection vers la page de confirmation
        header("Location: /projet-website/Inscription/Form2_inscription.php#modal");
        exit();
    }
}else {
    header('Location: /projet-website/Homepage.php');
    exit;
}
?>