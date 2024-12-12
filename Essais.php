<?php
session_start();
$_SESSION['origin'] =  $_SERVER['REQUEST_URI'];
$role = $_SESSION['role'];
$db_name = $_SESSION['db_name'];
include 'Fonctions.php';
include 'Notifications/fonction_notif.php';
$list_essai = Get_essais($role);
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
    <!-- Barre de recherche -->
    <form method="POST">
        <div class="search-container">
            <input type="text" name="navbar" class="search-box" placeholder="Rechercher..." id="searchInput">
            <button type="submit" class="search-button">Rechercher</button>
        </div>

        <!-- Sélection de filtres -->
        <div class="filter-container">
            <!-- Filtre de statut -->
            <select name="phaseFilter" class="filter-box">
                <option value="Tous">Toutes les phases</option>
                <option value="Phase I">PHASE I</option>
                <option value="Phase II">PHASE II</option>
                <option value="Phase III">PHASE III</option>
            </select>

            <!-- Filtre de l'entreprise-->
            <select name="companyFilter" class="filter-box">
                <?php
                enterprise_filter($list_essai);  // Liste des entreprises
                ?>
            </select>
        </div>
    </form>
</div>

      <div>
          <div id="trial_boxes">
            <form method="POST" action="Essai_individuel.php">
          <?php
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                Display_essais($list_essai);
            }
            // Vérifier si le bouton a été cliqué
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['navbar'])) {
                // Appeler une fonction PHP
                $recherche = $_POST['navbar'];
                $filtres = [$_POST['phaseFilter'], $_POST['companyFilter']]; 
                recherche_EC($list_essai, $recherche, $filtres);
            }
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['essai_indi'])) {
                header("Location: Essai_individuel.php");
            }
            ?>
            </form>
          </div>
      </div>
    </div>
</body>
</html>