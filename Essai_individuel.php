<?php
session_start();
$_SESSION['origin'] = 'Essai_individuel';
include 'Fonctions_essai.php';
include 'Notifications/fonction_notif.php';
include 'Fonctions.php';
$role = $_SESSION['role'];
$Id_user = $_SESSION['Id_user'];
$Id_essai = $_SESSION['Id_essai'];

//Requete pour récupérer les informations nécessaires 
$pdo= Connexion_base();
$stmt = $pdo ->prepare('SELECT `Statut`, `Id_entreprise` FROM `ESSAIS_CLINIQUES` WHERE `Id_essai` = :Id_essai');
$stmt -> execute(['Id_essai' => $Id_essai]);
$Infos= $stmt->fetch(PDO::FETCH_ASSOC);
$Id_entreprise = $Infos['Id_entreprise'];
$Statut_essai = $Infos['Statut'];

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
                    if (Verif_Patient_Cet_Essai($Id_essai, $Id_user)){ //si ce patient est dans cet essai
                        echo '<button name = "action" value="se retirer patient" class="nav-btn_essai">Se retirer de cet essai</button>';
                   }

                    
                        $Statut_patient = Get_Statut_Patient($Id_essai, $Id_user);
                         if(!Verif_Participation_Patient($Id_user)&& $Statut_essai == 'Recrutement' && $Statut_patient == null ){ 
                            //si ce patient n'est pas dans cet essai et que l'essai est en recrutement
                    echo '<button name = "action" value="participer patient" class="nav-btn_essai">Postuler à cet essai</button>';
                    }
                        if($Statut_patient == 'En attente'){
                            echo '<p>Candidature en étude</p>';
                        
                
                }
                }

                if($role == 'medecin'){
                    if(Verif_Participation_Medecin($Id_user, $Id_essai)){ // Si ce médecin s'occupe ou s'est occupé de cet essai
                        echo '<div class="side-buttons_candidature">';
                        echo '<button name = "action" value="se retirer medecin" class="nav-btn_essai">Se retirer de cet essai</button>';
                        echo '</div>';
                        echo'<div class="side-buttons__statistique">
                        <a href="Homepage.php" class="nav-btn">Afficher les Stastistiques</a>
                        </div>';
                        //fonctionne mais nécessite surement actualisation
                        Afficher_Patients($Id_essai, 'Actif');
                        Afficher_Patients($Id_essai, 'En attente');
                        
                    } else { 
                        // Si ce médecin ne s'occupe pas de cet essai
                        $Statut_medecin = Get_Statut_Medecin($Id_essai, $Id_user);
                        if($Statut_medecin == 'Sollicite'){
                            echo '<div class="side-buttons_candidature">';
                            echo '<p><strong>L\'entreprise souhaite vous solliciter sur cet essai, voulez-vous accepter ou refuser ?</strong></p>';
                            echo '<button name = "action" value="accepter" class="nav-btn_essai_candidature">Accepter</button>';                                         
                            echo '<button name = "action" value="refuser" class="nav-btn_essai_candidature">Refuser</button>';   
                            //fonctionne mais nécessite actualisation                 
                            echo '</div>';
                        } 
                        if($Statut_medecin == 'En attente'){
                            //le medecin a demandé a participé à l'essai et attend la réponse
                            //affichage erreur double clé
                            echo '<p>Candidature en étude</p>';
                        }
                        elseif ($Statut_essai != 'Termine' && $Statut_medecin == null ) {
                            echo '<div class="side-buttons_candidature">';
                            echo '<button name = "action" value="participer medecin" class="nav-btn_essai">Postuler à cet essai</button></div>';
                        } 
                    }

                }

            if ($role == 'admin'){
                //semble appeler postuler_medecin??
                echo'<div class="side-buttons__statistique">
                <a href="Homepage.php" class="nav-btn">Afficher les Stastistiques</a>
                </div>';
                Afficher_Patients($Id_essai,'Actif');
                Afficher_Patients($Id_essai,'En attente'); 
                Afficher_Medecins($Id_essai,'Actif');
                Afficher_Medecins($Id_essai,'En attente');
                if($Statut_essai =='En cours'){
                    echo '<div class="side-buttons_candidature">';
                    echo '<button name = "action" value="suspendre essai" class="nav-btn_essai">Suspendre cet essai</button></div>';}
                if($Statut_essai == 'Suspendu'){
                    echo '<div class="side-buttons_candidature">';
                    echo '<button name = "action" value="relancer essai" class="nav-btn_essai">Relancer cet essai</button></div>';}
                }
               
                //modifier l'essai?
                //demander_medecin?
            
            

            if ($role == 'entreprise'){
                $Id_entreprise = Get_Entreprise($Id_essai);
                if($Id_entreprise == $Id_user){ //si l'entrepise gère cet essai
                   echo'<div class="side-buttons__statistique">
                   <a href="Homepage.php" class="nav-btn">Afficher les Stastistiques</a>
                   </div>';
                   echo'<div class="side-buttons__statistique">
                   <a href="Homepage.php" class="nav-btn">Demander un médecin</a>
                   </div>';
                    Afficher_Medecins($Id_essai,'Actif');
                    Afficher_Medecins($Id_essai,'En attente');
                     if($Statut_essai == 'En cours'){
                        echo '<div class="side-buttons_candidature">';
                        echo '<button name = "action" value="suspendre essai" class="nav-btn_essai">Suspendre cet essai</button></div>';}
                     if($Statut_essai == 'Suspendu'){
                         echo '<div class="side-buttons_candidature">';
                        echo '<button name = "action" value="relancer essai" class="nav-btn_essai">Relancer cet essai</button></div>';}
           
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
            if ($_POST['action'] === 'relancer essai'){
                Relancer_Essai($Id_essai);
            }
        }
    }
    

       ?>
   </div>
</main>
</body>
</html>