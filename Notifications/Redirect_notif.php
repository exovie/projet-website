<?php 
session_start();
include("../Notifications/fonction_notif.php");
include("../Fonctions.php");


// // Récupérer les données
$Id_Essai = $_SESSION['Id_Essai'];
$Id_Notif = $_SESSION['Id_Notif']; 
$CodeNotif = $_SESSION['CodeNotif'] ;

unset($_SESSION['Id_Notif']);
unset($_SESSION['CodeNotif']);
unset($_SESSION['Id_Essai']);
// Rediriger l'utilisateur vers la page appropriée
if ($id_notif == 1){
    header('projet-website/Admin/Home_Admin.php');
    exit();
}elseif ($id_notif == 4){
    header('projet-website//Essais.php');
    exit();
}

else {
    echo 'dans le else de fin ';
    $_SESSION['Essai'] = $Id_Essai;
    header('projet-website/Essai_individuel.php');
    exit();
}
?>
