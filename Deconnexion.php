<?php
session_start();

$_SESSION = [];  // Vider toutes les variables de session

session_destroy();  // Détruire complètement la session

// Rediriger vers la page d'accueil
header("Location: Homepage.php?unloggedSuccess=true");
exit();
?>