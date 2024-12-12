<?php
session_start();
$_SESSION['origin'] =  $_SERVER['REQUEST_URI'];
// Connexion à la base de données
$db_name = "mysql:host=localhost;dbname=website_db"; 
$_SESSION['db_name'] = $db_name;
if (isset($_SESSION['Logged_user']) && $_SESSION['Logged_user'] === true) {
    $role= $_SESSION['role'];

} else {
    $_SESSION['role'] = 'visiteur';
}
$role = $_SESSION['role'];
include_once 'Fonctions.php';
include_once 'Fonctions_essai.php';
include_once 'Notifications/fonction_notif.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Clinicou - HomePage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="'utf-8">
    <link rel="stylesheet" href='website.css'>
    <link rel="stylesheet" href='navigationBar.css'>
    <link rel="stylesheet" href='Notifications/Notifications_style.css'>
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
    <h1>Clinicou, le site des essais cliniques !</h1>
    <p>Description à faire </p>
        <div>
            <img src="Pictures/logo.png" alt="logo" id="grologo">
            <h1 id="main_page">Bienvenue sur notre site</h1>
            <p class = "presentation"> Les entreprises membres :</p>
            <div id="boxes">
            <?php
              $id_entreprises = Get_id( 'ENTREPRISES', 'Id_entreprise');
              $index = 0;
              foreach ($id_entreprises as $temporary) {
                tablename($temporary);
                if (!Verif_inscription($temporary)) {
                    unset($id_entreprises[$index]);
                }
                $index++;
              }          
              foreach ($id_entreprises as $id_entreprise) {
                $entreprise = Get_entreprise_data($id_entreprise);
                Display_entreprise_data($entreprise);
              }

            ?>
            </div>
            <p class="presentation" id="medecin">Les médecins membres :</p>
            <div id="medecin_boxes">
            <?php
              $id_medecins = Get_id( 'MEDECINS', 'Id_medecin');
              $counter = 0;
              foreach ($id_medecins as $id_medecin) {
                if ($counter == 18) break;
                $medecins = List_Medecin($id_medecin);
                display_medecin($medecins);
                $counter++;
              }
            ?>
            </div>
        </div>
    </div>
    


    <?php
        if ($role === 'entreprise' && verif_entreprise($Id_essai, $Id_entreprise)) {
         if (isset($_SESSION['postdata'])) {  // Utilisez isset() pour vérifier que 'medecins' est réellement présent dans $_POST
            $postdata = $_SESSION['postdata']; 
            unset($_SESSION['postdata']);
            if (isset($postdata['medecins'])) {
            $id_medecins = Get_id('MEDECINS', 'Id_medecin');
            $medecins = [];
                if (!empty($id_medecins)) { // Vérifie que le tableau n'est pas vide
                    foreach ($id_medecins as $id_medecin) {
                        $medecins[] = List_Medecin($id_medecin);   
                    }
                    affichage_request_medecin(11, $medecins);       
                } else {
                    // Gérer le cas où il n'y a pas de médecins à afficher
                    echo "Aucun médecin trouvé.";
                }
        }
        } else {
            echo '
            <form method="POST" action="hub.php">
                <button name="medecins" type="submit" class="search-button">Rechercher</button>
            </form>
            ';
        }
    }
        
    ?>

</body>
</html>

<!--
<form action="TD2_exo3.php" method="post">
  <label for="nom">Entrez le produit voulu et la quantité au format suivant :</label>
  <input type="text" id="nom" placeholder="produit, quantité" name="nom" required>
  
  <br><br>
  <input type="submit" value="Envoyer">
</form> 

    foreach ($clinical_trials as $essai_clinique) {
        echo '<ul>';
        echo '<li>Titre : ' . $essai_clinique['Titre'] . '</li>';
        echo '<li>Contexte : ' . $essai_clinique['Contexte'] . '</li>';
        echo '<li>Objectif de l\'essai : ' . $essai_clinique['Objectif_essai'] . '</li>';
        echo '<li>Design de l\'étude : ' . $essai_clinique['Design_etude'] . '</li>';
        echo '<li>Critère d\'évaluation : ' . $essai_clinique['Critere_evaluation'] . '</li>';
        echo '<li>Résultats attendus : ' . $essai_clinique['Resultats_attendus'] . '</li>';
        echo '<li>Date de lancement : ' . $essai_clinique['Date_lancement'] . '</li>';
        echo '<li>Date de fin : ' . $essai_clinique['Date_fin'] . '</li>';
        echo '<li>Date de création : ' . $essai_clinique['Date_creation'] . '</li>';
        echo '<li>Statut : ' . $essai_clinique['Statut'] . '</li>';
        echo '</ul>';
    }
-->