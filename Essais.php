<?php
session_start();
$_SESSION['origin'] = 'Essais_cliniques';
$role = $_SESSION['role'];
$db_name = $_SESSION['db_name'];
include 'Fonctions.php';
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
      <div class="search-container">
          <input type="text" class="search-box" placeholder="Rechercher..." id="searchInput">
          <button class="search-button">Rechercher</button>
      </div>
      <!-- Sélection de filtres -->
    <div class="filter-container">
      <!-- Filtre de statut -->
      <select name="statusFilter" class="filter-box">
          <option value="Tous">Tous les statuts</option>
          <option value="Recrutement">Recrutement</option>
          <option value="En attente">En attente</option>
          <option value="Terminé">Terminé</option>
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
