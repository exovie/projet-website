<?php
session_start();
$_SESSION['origin'] = 'Essais_cliniques';
$role = $_SESSION['role'];
$db_name = $_SESSION['db_name'];
include 'Fonctions.php';
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
        <div class="dropdown">
            <a href="Homepage.php">
                <img src="Pictures/letterPicture.png" alt="letterPicture" style="cursor: pointer;">
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

            // Fonction PHP appelée
            
            ?>
          </div>
      </div>
    </div>
</body>
</html>

