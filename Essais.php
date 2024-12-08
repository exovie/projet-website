<?php
session_start();
$_SESSION['origin'] = 'Essais_cliniques';
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

</head>
<body>
    <!-- Conteneur fixe en haut de la page -->
    
    <div class="navbar">
    <div id="logo">
        <a href="Homepage.php">
            <img src="Pictures/logo.png" alt="minilogo" class="minilogo">
        </a>
    </div>
    <a href="Essais.php" class="nav-btn">Essais Cliniques</a>
    <a href="Entreprises.php" class="nav-btn">Entreprise</a>
    <a href="Contact.php" class="nav-btn">Contact</a>
    <!-- Accès à la messagerie -->
    <?php if (isset($_SESSION['Logged_user']) && $_SESSION['Logged_user'] === true): ?>
    <div class="dropdown">
        <a href="Essais.php#messagerie">
            <img src="Pictures/letterPicture.png" alt="letterPicture" style="cursor: pointer;">
        </a>
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
            } else {
                echo "<h1 style='font-size: 18px; text-align: center;'>" . htmlspecialchars($_SESSION['Nom'], ENT_QUOTES, 'UTF-8') . "</h1>";
            }
            ?>
            <a href="#">Mon Profil</a>
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
            <a href="/projet-website/Essais.php" class="close-btn">&times;</a>
            <h1>Centre de notifications</h1>
            <!-- Contenu de la messagerie -->
            <?php Affiche_notif($_SESSION['Id_user'])?>
        </div>
    </div>
    
    <!-- Contenu principal -->
    <div class="content">
      <!-- Barre de recherche -->
      <div class="search-container">
          <input type="text" class="search-box" placeholder="Rechercher..." id="searchInput">
          <button class="search-button">Rechercher</button>
      </div>
      <!-- Sélection de filtres -->
    <div class="filter-container">
      <!-- Filtre de statut -->
      <select name="statusFilter" class="filter-box">
          <option value="Tous">Toutes les phases</option>
          <option value="Recrutement">PHASE I</option>
          <option value="En attente">PHASE II</option>
          <option value="Terminé">PHASE III</option>
      </select>

      <!-- Filtre de date -->
      <select name="dateFilter" class="filter-box">
          <option value="Tous">Toutes les dates</option>
          <option value="Aujourd'hui">Aujourd'hui</option>
          <option value="Cette semaine">Cette semaine</option>
          <option value="Ce mois">Ce mois</option>
      </select>

      <!-- Filtre de l'entreprise-->
      <select name="dateFilter" class="filter-box">
          <option value="Tous">Toutes les entreprises</option>
          <option value="Aujourd'hui">Aujourd'hui</option>
          <option value="Cette semaine">Cette semaine</option>
          <option value="Ce mois">Ce mois</option>
      </select>
    </div>
      <div>
          <div id="trial_boxes">
          <?php
            List_essai($role, $db_name);
          ?>
          </div>
      </div>
    </div>
</body>
</html>

