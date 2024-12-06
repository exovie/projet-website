<?php
session_start();

//import des fonctions
include 'fonctionInscription.php';
include '../Fonctions.php';

// Connexion à la base de données
$pdo = Connexion_base();
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
    Fermer_base($pdo);
        header('Location: /projet-website/Inscription/Form2_inscription.php#modal');
    } else {
        // Si pas d'erreur, on passe à la page suivante
        $_SESSION['reponsesInscription'] = ($_POST['reponses']); 
        Fermer_base($pdo);
        header("Location: /projet-website/Inscription/finalisation_inscription.php");

    }
?>