<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclure le module ou fichier PHP
include 'module.php';
session_start();
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
$role = $_SESSION["role"];

// Vérification du format des réponses
$errorMessages = '';
$date = $_POST['reponses'][2];
if ($role == 'patient'){
    $ageErr= Verif_age($date); 
    if ($ageErr == false) {
        //$errorMessages= $errorMessages."Vous devez être majeur pour vous inscrire.";
    }
}

$errors = validateResponsesByRole($role,  $_POST['reponses']);
if (!empty($errors)) {
    // S'il y a des erreurs, on les affiche
    $errorMessages = '';
    if (!empty($errors)) {
        foreach ($errors as $error) {
            $errorMessages .= $error;
        }
    }


    $_SESSION['FormsErr'] = $errorMessages;
        header('Location: /projet-website/Inscription/Form2_inscription.php#modal');
    } else {
        // Si pas d'erreur, on passe à la page suivante
        $_SESSION['reponsesInscription'] = ($_POST['reponses']); 
        header("Location: /projet-website/Inscription/finalisation_inscription.php");

    }
?>