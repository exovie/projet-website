<?php
session_start();
$_SESSION['origin'] =  $_SERVER['REQUEST_URI'];
include_once 'Fonctions_essai.php';
include_once 'Notifications/fonction_notif.php';
include_once 'Fonctions.php';
$role = $_SESSION['role'];
$Id_user = $_SESSION['Id_user'];

if (isset($_POST['essai_indi'])) {
    $_SESSION['Id_essai'] = $_POST['essai_indi'];
} elseif (isset($_SESSION['postdata']['medecins'])) {
    $_SESSION['Id_essai'] =$_SESSION['postdata']['medecins'];
}
$Id_essai = $_SESSION['Id_essai'];

$role = 'entreprise';
$Id_user = 5;
$Statut_essai = 'En attente';
$_SESSION['origin'] = 'Essai_individuel.php';

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
    <link rel="stylesheet" href='Notifications/Notifications_style.css'>
    afficha

</head>
<body>
<!-- Code de la barre de navigation -->
<div class="navbar">
        <div id="logo">
            <a href="Homepage.php">
                <img src="Pictures/logo.png" alt="minilogo" class="minilogo">
            </a>
        </div>
        <a href="Essais.php" class="nav-btn">Essais Cliniques</a>

        <!-- Accès à la page de Gestion -->
        <?php if ($_SESSION['role'] == 'Admin'): ?>
            <a href="Admin/Home_Admin.php" class="nav-btn">Gestion</a>
        <?php endif; ?>

        <!-- Accès à la messagerie -->
        <?php if (isset($_SESSION['Logged_user']) && $_SESSION['Logged_user'] === true): ?>
        <div class="dropdown">
            <a href= "<?= $_SESSION['origin'] ?>#messagerie">
                <img src="Pictures/letterPicture.png" alt="letterPicture" style="cursor: pointer;">
            </a>
            <!-- Affichage de la pastille -->
            <?php 
            $showBadge = Pastille_nombre($_SESSION['Id_user']);
            if ($showBadge > 0): ?>
                <span class="notification-badge"><?= htmlspecialchars($showBadge) ?></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Connexion / Inscription -->
        <div class="dropdown">
            <a>
                <img src="Pictures/pictureProfil.png" alt="pictureProfil" style="cursor: pointer;">
            </a>
            <div class="dropdown-content">
            <?php if (isset($_SESSION['Logged_user']) && $_SESSION['Logged_user'] === true): ?>
                <!-- Options pour les utilisateurs connectés -->
                <?php 
                if ($_SESSION['role'] == 'Medecin') {
                    echo "<h1 style='font-size: 18px; text-align: center;'>Dr " . htmlspecialchars($_SESSION['Nom'], ENT_QUOTES, 'UTF-8') . "</h1>";
                } elseif ($_SESSION['role'] == 'Entreprise') {
                    echo "<h1 style='font-size: 18px; text-align: center;'>" . htmlspecialchars($_SESSION['Nom'], ENT_QUOTES, 'UTF-8') . "®</h1>";
                } elseif(($_SESSION['role']=='Admin')){
                    echo "<h1 style='font-size: 18px; text-align: center;'>Admin</h1>";
                } else{
                    echo "<h1 style='font-size: 18px; text-align: center;'>" . htmlspecialchars($_SESSION['Nom'], ENT_QUOTES, 'UTF-8') . "</h1>";
                }
                if ($_SESSION["role"]!=='Admin'&& $_SESSION['Logged_user'] === true)
                {echo "<a href='Page_Mes_Infos/Menu_Mes_Infos.php'>Mon Profil</a>";} ?>
                <a href="Deconnexion.php">Déconnexion</a>
            <?php else: ?>
                <!-- Options pour les utilisateurs non connectés -->
                <a href="Connexion/Form1_connexion.php#modal">Connexion</a>
                <a href="Inscription/Form1_inscription.php#modal">S'inscrire</a>
            <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Message Success -->
    <?php 
    if (isset($_SESSION['SuccessCode'])): 
        SuccesEditor($_SESSION['SuccessCode']);
        unset($_SESSION['SuccessCode']); // Nettoyage après affichage
    endif; 
    ?>

    <!-- Message Erreur -->
    <?php 
    if (isset($_SESSION['ErrorCode'])): 
        ErrorEditor($_SESSION['ErrorCode']);
        unset($_SESSION['ErrorCode']); // Nettoyage après affichage
    endif; 
    ?>
    
    <!-- Messagerie -->
    <div id="messagerie" class="messagerie">
        <div class="messagerie-content">
            <!-- Lien de fermeture qui redirige vers Homepage.php -->
            <a href="<?= $_SESSION['origin'] ?>" class="close-btn">&times;</a>
            <h1>Centre de notifications</h1>
            <!-- Contenu de la messagerie -->
            <?php Affiche_notif($_SESSION['Id_user'])?>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="content">
    <div id="indiv_trial_boxes">
       <?php Afficher_Essai($Id_essai); ?>
        </div> 
        <div class="frame">
        <?php 

        echo '<form method="POST" action="hub.php">';
        if($role == 'Patient'){
                    if (Verif_Patient_Cet_Essai($Id_essai, $Id_user)){ //si ce patient est dans cet essai
                        echo '<button name = "action" value="se retirer patient" class="nav-btn_essai">Se retirer de cet essai</button>';
                   }

                
                    $Statut_patient = Get_Statut_Patient($Id_essai, $Id_user);
                        if(!Verif_Participation_Patient($Id_user)&& $Statut_essai == 'Recrutement' && $Statut_patient == null ){ 
                        //si ce patient n'est pas dans cet essai et que l'essai est en recrutement
                echo '<button name = "action" value="participer patient" type="submit" class="nav-btn_essai">Postuler à cet essai</button>';
                }
                    if($Statut_patient == 'En attente'){
                        echo '<p>Candidature en étude</p>';
                    
            
            }
            }

        if($role == 'Medecin'){
            if(Verif_Participation_Medecin($Id_user, $Id_essai)){ // Si ce médecin s'occupe ou s'est occupé de cet essai
                echo '<div class="side-buttons_candidature">';
                echo '<button name = "action" value="se retirer medecin" type="submit" class="nav-btn_essai">Se retirer de cet essai</button>';
                echo '</div>';
                echo'<div class="side-buttons__statistique">
                <a href="Page_Essai_Individuel/Page_aff_stat.php" type="submit" class="nav-btn">Afficher les Stastistiques</a>
                </div>';
                //fonctionne mais nécessite surement actualisation
                $Statut_medecin = Get_Statut_Medecin($Id_essai, $Id_user);
                if ($Statut_medecin === 'Actif') {
                Afficher_Patients($Id_essai, 'Actif');
                Afficher_Patients($Id_essai, 'En attente');
                }
                
            } else { 
                // Si ce médecin ne s'occupe pas de cet essai
                $Statut_medecin = Get_Statut_Medecin($Id_essai, $Id_user);
                if($Statut_medecin == 'Sollicite'){
                    echo '<div class="side-buttons_candidature">';
                    echo '<p><strong>L\'entreprise souhaite vous solliciter sur cet essai, voulez-vous accepter ou refuser ?</strong></p>';
                    echo '<button name = "action" value="accepter" type="submit" class="nav-btn_essai_candidature">Accepter</button>';                                         
                    echo '<button name = "action" value="refuser" type="submit" class="nav-btn_essai_candidature">Refuser</button>';   
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
                    echo '<button name = "action" value="participer medecin" type="submit" class="nav-btn_essai">Postuler à cet essai</button></div>';
                } 
            }

        }

            if ($role == 'Admin'){
                echo'<div class="side-buttons__statistique">
                <a href="Page_Essai_Individuel/Page_aff_stat.php" class="nav-btn">Afficher les Stastistiques</a>
                </div>';
                Afficher_Patients($Id_essai,'Actif');
                Afficher_Patients($Id_essai,'En attente'); 
                Afficher_Medecins($Id_essai,'Actif', $Id_user, $role);
                Afficher_Medecins($Id_essai,'En attente', $Id_user, $role);
                if($Statut_essai =='En cours'){
                    echo '<div class="side-buttons_candidature">';
                    echo '<button name = "action" value="suspendre essai" type="submit" class="nav-btn_essai">Suspendre cet essai</button></div>';}
                if($Statut_essai == 'Suspendu'){
                    echo '<div class="side-buttons_candidature">';
                    echo '<button name = "action" value="relancer essai" type="submit" class="nav-btn_essai">Relancer cet essai</button></div>';}
                }
               
                //modifier l'essai?
                //demander_medecin?
            
            

            if ($role == 'Entreprise'){
                $Id_entreprise = Get_Entreprise($Id_essai);
                if($Id_entreprise == $Id_user){ //si l'entrepise gère cet essai
                   echo'<div class="side-buttons__statistique">
                   <a href="Page_Essai_Individuel/Page_aff_stat.php" class="nav-btn">Afficher les Stastistiques</a>
                   </div>';
                    Afficher_Medecins($Id_essai,'Actif', $Id_user, $role);
                    Afficher_Medecins($Id_essai,'En attente', $Id_user, $role);
                     if($Statut_essai == 'En cours'){
                        echo '<div class="side-buttons_candidature">';
                        echo '<button name = "action" value="suspendre essai" type="submit" class="nav-btn_essai">Suspendre cet essai</button></div>';}
                     if($Statut_essai == 'Suspendu'){
                         echo '<div class="side-buttons_candidature">';
                        echo '<button name = "action" value="relancer essai" type="submit" class="nav-btn_essai">Relancer cet essai</button></div>';}
                    if (isset($postdata['liste_medecins'])) {
                        $id_medecins = Get_id('MEDECINS', 'Id_medecin');
                        $medecins = [];
                            if (!empty($id_medecins)) { // Vérifie que le tableau n'est pas vide
                                foreach ($id_medecins as $id_medecin) {
                                    $medecins[] = List_Medecin($id_medecin);   
                                }
                                affichage_request_medecin($Id_essai, $medecins);       
                            } else {
                                // Gérer le cas où il n'y a pas de médecins à afficher
                                echo "Aucun médecin trouvé.";
                            }
                        if (isset($postdata['demande_medecin'])) {
                            list($Id_essai, $Id_medecin) = explode('_', $postdata['demande_medecin']);
                            Demander_Medecin_essai($Id_essai, $Id_medecin);
                        }
                    }
                    } else {
                        echo '
                        <form method="POST" action="hub.php">
                            <button name="liste_medecins" value=' . $Id_essai . ' type="submit" class="search-button">Voir la liste des médecins</button>
                        </form>
                        ';
                    }
           
                    //si le recrutement a commencé: afficher les statistiques
                    //demander un médecin
                    //modifier l'essai si le recrutement n'a pas débuté
               
       }
       echo '</form>';
      
       

?>

<?php
$Id_entreprise_verif = Get_Entreprise($Id_essai);
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

}
if (isset($_SESSION['postdata'])) {  // Utilisez isset() pour vérifier que 'medecins' est réellement présent dans $_POST
    $postdata = $_SESSION['postdata']; 
    unset($_SESSION['postdata']);
    if (isset($postdata['action'])) {

    if ($postdata['action'] === 'se retirer patient'){
        Retirer_Patient_Essai($Id_essai, $Id_user);
    }
    if ($postdata['action'] === 'participer patient'){
        Postuler_Patient_Essai($Id_essai, $Id_user);
    }
    if ($postdata['action'] === 'se retirer medecin'){
        Retirer_Medecin_Essai($Id_essai, $Id_user);
    }
    if ($postdata['action'] === 'participer medecin'){
        Postuler_Medecin_Essai($Id_essai, $Id_user);
    }
    if ($postdata['action'] === 'suspendre essai'){
        Suspendre_Essai($Id_essai);
    }
    if ($postdata['action'] === 'relancer essai'){
        Relancer_Essai($Id_essai);
    }
}

if (isset($postdata['aller_vers_patient'])) {
    $_SESSION['Id_patient'] = $postdata['aller_vers_patient'];
    header("Location: Page_Essai_individuel/Liste_Patients_Essai.php");
}

if (isset($postdata['aller_vers_patient_sans_modif'])) {
    $_SESSION['Id_patient'] = $postdata['aller_vers_patient'];
    header("Location: Page_Essai_individuel/Infos_Patient.php");
    exit;
}

if (isset($postdata['retirer_medecin'])){
    $id_medecin = $Id_user;
    Retirer_Medecin_Essai($Id_essai, $id_medecin);
}

if (isset($postdata['accepter_medecin'])){
    $id_medecin = $postdata['accepter_medecin'];
    Traiter_Candidature_Medecin($Id_essai, $id_medecin, 1);
}

if (isset($postdata['refuser_medecin'])){
    $id_medecin = $postdata['refuser_medecin'];
    Traiter_Candidature_Medecin($Id_essai, $id_medecin, 0);
}

if (isset($postdata['accepter_patient'])) {
    $id_patient = $postdata['accepter_patient'];
    Traiter_Candidature_Patient($Id_essai, $id_patient,1);
}

if (isset($postdata['refuser_patient'])) {
    $id_patient = $postdata['refuser_patient'];
    Traiter_Candidature_Patient($Id_essai, $id_patient,0);
}

if (isset($postdata['retirer_patient'])) {
    $id_patient = $postdata['retirer_patient'];
    Retirer_Patient_Essai($Id_essai, $id_patient);
}

if (isset($postdata['liste_medecins'])) {
    $id_medecins = Get_id('MEDECINS', 'Id_medecin');
    $medecins = [];
        if (!empty($id_medecins)) { // Vérifie que le tableau n'est pas vide
            foreach ($id_medecins as $id_medecin) {
                $medecins[] = List_Medecin($id_medecin);   
            }
            affichage_request_medecin($Id_essai, $medecins);       
        } else {
            // Gérer le cas où il n'y a pas de médecins à afficher
            echo "Aucun médecin trouvé.";
        }
    if (isset($postdata['demande_medecin'])) {
        list($Id_essai, $Id_medecin) = explode('_', $postdata['demande_medecin']);
        Demander_Medecin_essai($Id_essai, $Id_medecin);
    }
}

} elseif (($Id_entreprise_verif == $Id_user || $role === 'Admin')) {
    echo '
    <form method="POST" action="hub.php">
        <button name="liste_medecins" value=' . $Id_essai . ' type="submit" class="search-button">Voir la liste des médecins</button>
    </form>
    ';
}
?>

   </div>
</main>
<?php 
?>
</body>
</html>
