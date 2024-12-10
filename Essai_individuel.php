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
 

                if($role == 'medecin'){
                    if(Verif_Participation_Medecin($Id_user, $Id_essai)){ // Si ce médecin s'occupe de cet essai
                        echo '<button class="nav-btn_essai" onclick="Retirer_Medecin_Essai(' . $Id_essai . ', ' . $Id_user . ')">Se retirer de cet essai</button>';
                        Afficher_Patients($Id_essai, 'Actif');
                        Afficher_Patients($Id_essai, 'En attente');
                    } else { // Si ce médecin ne s'occupe pas de cet essai
                        if(Verif_Medecin_Sollicite($Id_essai, $Id_user)){
                            echo '<div class="side-buttons_candidature">';
                            echo '<p><strong>L\'entreprise souhaite vous solliciter sur cet essai, voulez-vous accepter ou refuser ?</strong></p>';
                            echo '<button class="nav-btn_essai_candidature" onclick="Traiter_Candidature_Medecin('.$Id_essai.', '.$Id_user.', 1)">Accepter</button>';
                            echo '<button class="nav-btn_essai_candidature" onclick="Traiter_Candidature_Medecin('.$Id_essai.', '.$Id_user.', 0)">Refuser</button>';
                            echo '</div>';
                        } elseif ($Statut_essai != 'Termine') {
                            echo '<button class="nav-btn_essai" onclick="Postuler_Medecin_Essai(' . $Id_essai . ', ' . $Id_user . ')">Participer à cet essai</button>';
                        }
                    }
                }
            if ($role == 'admin'){
                Afficher_Patients($Id_essai,'Actif');
                Afficher_Patients($Id_essai,'En attente'); 
                Afficher_Medecins($Id_essai,'Actif');
                Afficher_Medecins($Id_essai,'En attente');
                echo '<button class="nav-btn_essai" onclick="Suspendre_Essai(' . $Id_essai . ')"> Suspendre cet essai</button>';
                //modifier l'essai?
                //demander_medecin?
            
            }

            if ($role == 'entreprise'){
                if(Verif_Organisation_Entreprise($Id_essai, $Id_user)){ //si l'entrepise gère cet essai
                 Afficher_Medecins($Id_essai,'Actif');
                 Afficher_Medecins($Id_essai,'En attente');
                 echo '<button class="nav-btn_essai" onclick="Suspendre_Essai(' . $Id_essai . ')"> Suspendre cet essai</button>';}
                    //si le recrutement a commencé: afficher les statistiques
                    //demander un médecin
                    //modifier l'essai si le recrutement n'a pas débuté
               
       }

       ?>
   </div>
</main>
</body>
</html>