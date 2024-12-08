<?php 
session_start();
include("fonction_notif.php");

// Récupérer les données envoyées via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_essai']) && isset($_POST['code_notif']) && isset($_POST['id_notif'])) {

        // Récupérer les données
        $id_essai= $_POST['id_essai'];
        $code_notif = $_POST['code_notif'];
        $id_notif= $_POST['id_notif'];

        //Lit la notification
        Lire_notif($id_notif, $_SESSION['Id_users']);

        // Rediriger l'utilisateur vers la page appropriée
        if ($id_notif == 1){
            header('../admin_page.php');
            exit();
        }elseif ($id_notif == 4){
            header('../Essais.php');
            exit();
        }
        else {
            $_SESSION['Essai'] = $id_essai;
            header('../Essai_individuel.php');
            exit();
        }
}}
?>
