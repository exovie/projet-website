<?php 
session_start();
include("fonction_notif.php");
include("../Fonctions.php");


if (isset($_POST['Ne_plus'])) {
    // Accéder aux valeurs du tableau Ne_plus
    $Id_Notif = (int)$_POST['Ne_plus']['Id_Notif'];
    $Id_D = (int)$_POST['Ne_plus']['Id_D'];

    if ($Id_Notif && $Id_D) {
        // Traitez la notification (par exemple, marquer comme lu)
        Ne_plus_lire_notif($Id_Notif , $Id_D );
        header("Location: " . $_SESSION['origin'] . "#messagerie");
    }
} elseif (isset($_POST['Lire'])) {
    // Accéder aux valeurs du tableau Lire
    $Id_Notif = (int)$_POST['Lire']['Id_Notif'];
    $Id_D = (int)$_POST['Lire']['Id_D'];

    if ($Id_Notif && $Id_D) {
        // Traitez la notification (par exemple, marquer comme non lu)
        Lire_notif($Id_Notif , $Id_D );
        header("Location: " . $_SESSION['origin'] . "#messagerie");
    }
}

else {
// // Récupérer les données
$Id_Essai = $_POST['Id_Essai'];
$Id_Notif = $_POST['Id_Notif']; 
$CodeNotif = $_POST['CodeNotif'] ;

// Rediriger l'utilisateur vers la page appropriée
if ($CodeNotif == 1){
    header('Location: ../Admin/Validation_en_attente.php');
    exit();
}elseif ($CodeNotif == 4){
    header('Location: ../Essais.php');
    exit();
}
else {
$_SESSION['Id_essai'] = $Id_Essai;
header('Location:../Essai_individuel.php');
exit();
}}
?>
