<?php
session_start();
error_reporting(E_ALL); // Active le rapport de toutes les erreurs
ini_set('display_errors', 1); // Affiche les erreurs à l'écran
ini_set('display_startup_errors', 1); // Affiche les erreurs au démarrage de PHP
//include 'Fonctions.php';
include 'Fonctions_essai.php';
$Id_essai = 1;
$Id_entreprise = 7;
$role = 'admin';
$Id_user = 15;
$Statut_essai = 'Recrutement';

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
       <?php Afficher_Essai($Id_essai); ?>
        </div> 
        <div class="frame">
        <?php   if($role == 'patient'){
                    if (Verif_Patient_Cet_Essai($Id_essai, $Id_user)){ //si ce patient est dans cet essai
                     echo '<button class="nav-btn_essai" onclick="Retirer_Patient_Essai(' . $Id_essai . ', ' . $Id_user . ')">Se retirer de cet essai</button>';}
                     
                    else{
                if(!Verif_Participation_Patient($Id_user)&& $Statut_essai == 'Recrutement'){ //si ce patient n'est pas dans cet essai
                    echo '<button class="nav-btn_essai" onclick="Postuler_Patient_Essai(' . $Id_essai . ', ' . $Id_user . ')">Participer à cet essai</button>';}
                }
                }
 

            if($role =='medecin'){
                if(Verif_Participation_Medecin($Id_user, $Id_essai)){//si ce médecin s'occupe de cet essai
                     echo '<button class="nav-btn_essai" onclick="Retirer_Medecin_Essai(' . $Id_essai . ', ' . $Id_user . ')">Se retirer de cet essai</button>';
                     Afficher_Patients($Id_essai,'Actif', $Id_entreprise);
                     Afficher_Patients($Id_essai,'En attente', $Id_entreprise); }
                    //modifier les infos des patients dans la page menant au patient

                else{ // si ce médecin ne s'occupe pas de cet essai
                    if ($Statut_essai != 'Termine'){
                        echo '<button class="nav-btn_essai" onclick="Postuler_Medecin_Essai(' . $Id_essai . ', ' . $Id_user . ')"> Participer à cet essai</button>';}
                    }
                }
            if ($role == 'admin'){
                Afficher_Patients($Id_essai,'Actif', $Id_entreprise);
                Afficher_Patients($Id_essai,'En attente', $Id_entreprise); 
                Afficher_Medecins($Id_essai,'Actif', $Id_entreprise);
                Afficher_Medecins($Id_essai,'En attente', $Id_entreprise);
                echo '<button class="nav-btn_essai" onclick="Suspendre_Essai(' . $Id_essai . ')"> Suspendre cet essai</button>';
                //modifier l'essai?
                //demander_medecin?
            
            }

            if ($role == 'entreprise'){
                Afficher_Medecins($Id_essai,'Actif', $Id_entreprise);
                Afficher_Medecins($Id_essai,'En attente', $Id_entreprise);
                echo '<button class="nav-btn_essai" onclick="Suspendre_Essai(' . $Id_essai . ')"> Suspendre cet essai</button>';
                    //si le recrutement a commencé: afficher les statistiques
                    //demander un médecin
                    //modifier l'essai si le recrutement n'a pas débuté
               
       }

       ?>
   </div>
</main>
</body>
</html>