<?php
session_start();
error_reporting(E_ALL); // Active le rapport de toutes les erreurs
ini_set('display_errors', 1); // Affiche les erreurs à l'écran
ini_set('display_startup_errors', 1); // Affiche les erreurs au démarrage de PHP
//include 'Fonctions.php';
include 'Fonctions_essai.php';
$Id_essai = 5;
//$Id_entreprise = 7;
$role = 'patient';
$Id_user = 27;
$Statut_essai = 'Actif';
$_SERVER['origin'] = 'Essai_individuel.php';
//$_POST['action'] = "";

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Clinicou - HomePage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
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
        <?php   
        echo '<form method="POST">';
        if($role == 'patient'){
                    if (Verif_Patient_Cet_Essai($Id_essai, $Id_user)){ //si ce patient est dans cet essai, rajouter la condition du statut pour éviter le problème de double entrée
                      
                    echo '<button name = "action" value="se retirer patient" class="nav-btn_essai">Se retirer de cet essai</button>';
                   }

                    else{
                if(!Verif_Participation_Patient($Id_user)&& $Statut_essai == 'Recrutement'){ //si ce patient n'est pas dans cet essai
                    echo '<button name = "action" value="participer patient" class="nav-btn_essai">Postuler à cet essai</button>';
                    }
                
                }
                }

                if($role == 'medecin'){
                    if(Verif_Participation_Medecin($Id_user, $Id_essai)){ // Si ce médecin s'occupe de cet essai
                        echo '<button name = "action" value="se retirer medecin" class="nav-btn_essai">Se retirer de cet essai</button>';
                        Afficher_Patients($Id_essai, 'Actif');
                        Afficher_Patients($Id_essai, 'En attente');
                    } else { // Si ce médecin ne s'occupe pas de cet essai
                        if(Verif_Medecin_Sollicite($Id_essai, $Id_user)){
                            echo '<div class="side-buttons_candidature">';
                            echo '<p><strong>L\'entreprise souhaite vous solliciter sur cet essai, voulez-vous accepter ou refuser ?</strong></p>';
                            echo '<button name = "action" value="accepter" class="nav-btn_essai_candidature">Accepter</button>';                                         
                            echo '<button name = "action" value="refuser" class="nav-btn_essai_candidature">Refuser</button>';                    
                            echo '</div>';
                        } elseif ($Statut_essai != 'Termine') {
                            //mettre des if pour en attente, mettre un autre bouton
                            echo '<button name = "action" value="participer medecin" class="nav-btn_essai">Postuler à cet essai</button>';
                     
                        }
                      
                    }
                } #"Traiter_Candidature_Medecin('.$Id_essai.', '.$Id_user.', 1)
            if ($role == 'admin'){
                Afficher_Patients($Id_essai,'Actif');
                Afficher_Patients($Id_essai,'En attente'); 
                Afficher_Medecins($Id_essai,'Actif');
                Afficher_Medecins($Id_essai,'En attente');
                if($Statut_essai =='Actif'){
                  
                    echo '<button name = "action" value="suspendre essai" class="nav-btn_essai">Suspendre cet essai</button>';}
               
                //modifier l'essai?
                //demander_medecin?
            
            }

            if ($role == 'entreprise'){
                if(Verif_Organisation_Entreprise($Id_essai, $Id_user)){ //si l'entrepise gère cet essai
                 Afficher_Medecins($Id_essai,'Actif');
                 Afficher_Medecins($Id_essai,'En attente');
                 if($Statut_essai == 'Actif'){
                    echo '<button name = "action" value="suspendre essai" class="nav-btn_essai">Suspendre cet essai</button>';}
           
                    //si le recrutement a commencé: afficher les statistiques
                    //demander un médecin
                    //modifier l'essai si le recrutement n'a pas débuté
               
       }}
       echo '</POST>';
      
       if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

       }
       if ($_SERVER['REQUEST_METHOD']=== 'POST') {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'accepter') {
                Traiter_Candidature_Medecin($Id_essai, $Id_user, 1);
            } elseif ($_POST['action'] === 'refuser') {
                Traiter_Candidature_Medecin($Id_essai, $Id_user, 0);
            }
            if ($_POST['action'] === 'se retirer patient'){
                Retirer_Patient_Essai($Id_essai, $Id_user);
            }
            if ($_POST['action'] === 'participer patient'){
                Postuler_Patient_Essai($Id_essai, $Id_user);
            }
            if ($_POST['action'] === 'se retirer medecin'){
                Retirer_Medecin_Essai($Id_essai, $Id_user);
            }
            if ($_POST['action'] === 'participer medecin'){
                Postuler_Medecin_Essai($Id_essai, $Id_user);
            }
            if ($_POST['action'] === 'suspendre essai'){
                Suspendre_Essai($Id_essai);
            }
        }
    }
    

       ?>
   </div>
</main>
</body>
</html>