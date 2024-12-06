<?php
session_start();
//import des fonctions
include 'fonctionInscription.php';
include '../Fonctions.php';

// Connexion à la base de données
$pdo = Connexion_base();

// Vérification de l'envoi du formulaire
if (isset($_POST['part1'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

// Appel de la fonction pour vérifier l'email
    if (! Verif_mail($pdo, $email)) {
        // Email déjà utilisé
        $_SESSION['ErrorCode'] = 6;
        header('Location: Form1_inscription.php#modal');
        Fermer_base($pdo);
        exit();
    } else {
        // Si pas d'erreur, on passe à la page suivante
        $_SESSION['email'] = $email;
        $_SESSION['password'] = password_hash($password, PASSWORD_DEFAULT); // Hachage du mot de passe
        if(!isHashedPassword($_SESSION['password'])) {
            $_SESSION['ErrorCode'] = 7;
            header("Location: Form1_inscription.php#modal");
        }
        $_SESSION['role'] = $role;

        // Redirection vers la page de confirmation
        header("Location: /projet-website/Inscription/Form2_inscription.php#modal");
        Fermer_base($pdo);
        exit();
    }
}else {
    header('Location: /projet-website/Homepage.php');
    Fermer_base($pdo);
    exit;
}
?>