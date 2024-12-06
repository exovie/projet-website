<?php
session_start();

$_SESSION = [];  // Vider toutes les variables de session

session_destroy();  // Détruire la session

$pdo = null; // Fermer la connexion à la base de données

// Rediriger vers la page d'accueil
session_start();
$_SESSION['SuccessCode'] = 3;
$_SESSION['role'] = 'visiteur';
header("Location: Homepage.php?unloggedSuccess=true");
exit();
?>