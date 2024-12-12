<?php 

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclure le module ou fichier PHP
include 'module.php';

session_start();
include("../Notifications/fonction_notif.php");
include("../Fonctions.php");


// // Récupérer les données
$Id_Essai = $_POST['Id_Essai'];
$Id_Notif = $_POST['Id_Notif']; 
$CodeNotif = $_POST['CodeNotif'] ;

// Rediriger l'utilisateur vers la page appropriée
if ($Id_Notif == 1){
    header('Location: ../Admin/Home_Admin.php');
    exit();
}elseif ($Id_Notif == 4){
    header('Location: ../Essais.php');
    exit();
}

else {
$_SESSION['Id_essai'] = $Id_Essai;
header('Location:../Essai_individuel.php');
exit();
}
?>
