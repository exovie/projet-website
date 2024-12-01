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

//Vérification de l'envoi du formulaire
if (isset($_POST['part2'])) {
    $role = $_POST['role'];
}

// Vérification du format des réponses
$errors = validateResponsesByRole($role,  $_POST['reponses']);
$date = $_POST['reponses'][2];
if ($role == 'patient'){
    $ageErr= Verif_age($date); }

if (!empty($errors) & $ageErr!=true) {
    // S'il y a des erreurs, on les affiche
    session_start();
    $_SESSION['FormsErr'] = $errors+ $ageErr;
        header('Location: /projet-website/Inscription/Form2_inscription.php#modal');
        exit();
    } else {
        // Si pas d'erreur, on passe à la page suivante
        session_start();
        $_SESSION['reponsesInscription'] = $_POST['reponses']; 
        header("Location: /projet-website/Inscription/finalisation_inscription.php");
        exit();
    }
?>