<?php
include("fonctionInscription.php");
include("../Fonctions.php");

session_start();

//Vérifie si les données sont bien renseignées
if (!isset($_SESSION["role"], $_SESSION["email"])) {
    header("Location: Form1_inscription.php#modal");
    exit();}


// Connexion à la base de données
$host = 'localhost';
$dbname = 'website_db';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

//Enregistrement de l'users dans la BDD
$newID = addUser($pdo, $_SESSION["password"], $_SESSION["email"], $_SESSION["role"]);
if (!$newID) {$_SESSION["ErrorCode"] = 8;
    header("Location: Form1_inscription.php#modal");
    exit();}

//Enregistrement des données dans la base de données selon le role 
$addRoleError = addRole($pdo, $_SESSION["role"], $newID, $_SESSION["reponsesInscription"]);
if (!$addRoleError) {
    $_SESSION["ErrorCode"] = 8;
    header("Location: Form1_inscription.php#modal");
    exit();
}
else {
    $pdo = null; // Fermer la connexion à la base de données
    session_destroy(); // Réinitialiser la session pour ne pas sauvegarder les données du formulaire 

    session_start(); // Redémarrer la session pour afficher un message de succès
    $_SESSION["SuccessCode"] = 1;
    header("Location: /projet-website/Homepage.php");
}

?>
