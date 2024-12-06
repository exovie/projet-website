<?php
include("fonctionInscription.php");
include("../Fonctions.php");

session_start();

//Vérifie si les données sont bien renseignées
if (!isset($_SESSION["role"], $_SESSION["email"])) {
    header("Location: Form1_inscription.php#modal");
    exit();}


// Connexion à la base de données
$pdo = Connexion_base();


//Enregistrement de l'users dans la BDD
$newID = addUser($pdo, $_SESSION["password"], $_SESSION["email"], $_SESSION["role"]);
if (!$newID) {$_SESSION["ErrorCode"] = 8;
    Fermer_base($pdo);
    header("Location: Form1_inscription.php#modal");
    exit();}

//Enregistrement des données dans la base de données selon le role 
$addRoleError = addRole($pdo, $_SESSION["role"], $newID, $_SESSION["reponsesInscription"]);
if (!$addRoleError) {
    $_SESSION["ErrorCode"] = 8;
    Fermer_base($pdo);
    header("Location: Form1_inscription.php#modal");
    exit();
}
else {
    $pdo = null; // Fermer la connexion à la base de données
    session_destroy(); // Réinitialiser la session pour ne pas sauvegarder les données du formulaire 

    session_start(); // Redémarrer la session pour afficher un message de succès
    $_SESSION["SuccessCode"] = 1;
    Fermer_base($pdo);
    header("Location: /projet-website/Homepage.php");
}

?>
