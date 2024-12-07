<?php
session_start();
error_reporting(E_ALL); // Active le rapport de toutes les erreurs
ini_set('display_errors', 1); // Affiche les erreurs à l'écran
ini_set('display_startup_errors', 1); // Affiche les erreurs au démarrage de PHP
//include 'Fonctions.php';
include 'Fonctions_essai.php';


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Clinicou - HomePage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="'utf-8">
    <link rel="stylesheet" href='website.css'>
    <link rel="stylesheet" href='essai_indiv.css'>
    <link rel="stylesheet" href= 'navigationBar.css'>


</head>
<body>
<header>
    <!-- Conteneur fixe en haut de la page -->
    <div class="navbar">
        <div id="logo">
            <a href="Homepage.php">
                <img src="Pictures/minilogo.png" alt="minilogo">
            </a>
        </div>
        <a href="Essais.php">
    <button class="nav-btn">Essais Cliniques</button>
</a>
        <button class="nav-btn">Entreprise</button>
        <button class="nav-btn">Contact</button>
        <div class="dropdown">
            <a href="Homepage.php">
                <img src="Pictures/letterPicture.png" alt="pictureProfil" style="cursor: pointer;">
            </a>
            </div>
        <div class="dropdown">
            <a>
                <img src="Pictures/pictureProfil.png" alt="pictureProfil" style="cursor: pointer;">
            </a>
            <div class="dropdown-content">
                <?php if (isset($_SESSION['Logged_user']) && $_SESSION['Logged_user'] === true): ?>
                    <!-- Options pour les utilisateurs connectés -->
                    <a href="#">Mon Profil</a>
                    <a href="#">Déconnexion</a>
                <?php else: ?>
                    <!-- Options pour les utilisateurs non connectés -->
                    <a href="Connexion/Form1_connexion.php#modal">Connexion</a>
                    <a href="Inscription/Form1_inscription.php#modal">S'inscrire</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div>
                </header>
                <main>

    <div id="indiv_trial_boxes">
       <?php Afficher_essai(1);
       //if statut == 'patient'{
             //if (Verif_Patient_Cet_Essai($Id_essai, $Id_patient)){
                     //echo '<button class="nav-btn_essai" onclick="Retirer_Patient_Essai(' . $Id_essai . ', ' . $Id_patient . ')">Se retirer de cet essai</button>';}
            //else{
               // if(!Verif_Participation_Patient($Id_patient)){
 //echo '<button class="nav-btn_essai" onclick="Postuler_Patient_Essai(' . $Id_essai . ', ' . $Id_patient . ')">Participer à cet essai</button>';}
                //}}
 

       //if statut =='medecin'{
            //if(Verif_Participation_Medecin($Id_medecin, $Id_essai))//si ce médecin s'occupe de cet essai{
                    //echo '<button class="nav-btn_essai" onclick="Retirer_Medecin_Essai(' . $Id_essai . ', ' . $Id_medecin . ')">Se retirer de cet essai</button>';}
                    //afficher la liste des patients + les stats?
                    //modifier les infos des patients
                    //retirer un patient
                    //traiter la candidature
            //else{
                    // if ('statut_essai' != 'Termine'){
                        // //echo '<button class="nav-btn_essai" onclick="Postuler_Medecin_Essai(' . $Id_essai . ', ' . $Id_medecin . ')"> Participer à cet essai</button>';}
                    //}
       //}
       //if (statut == 'admin'){
            // Afficher la liste des patients, possibilité de les retirer?
            //Afficher la liste des médecins
            //suspendre l'essai

       //}
       // if (statut == 'entreprise'){
                    //si le recrutement a commencé: afficher les statistiques
                    //demander un médecin
                    //retirer un médecin
                    //modifier l'essai si le recrutement n'a pas débuté
                    //suspendre l'essai
       //}
       
       
       ?>
               <!-- mettre le contenu de la page ici -->
                     
    </div>
                </main>              
</body>