<?php
session_start();
$_SESSION['origin'] = 'Homepage';
$db_name = "mysql:host=localhost;dbname=website_db"; 
$_SESSION['db_name'] = $db_name;
$_SESSION['role'] = 'visiteur';
include 'Fonctions.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Clinicou - HomePage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="'utf-8">
    <link rel="stylesheet" href='website.css'>
    <link rel="stylesheet" href= 'navigationBar.css'>

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
        <div>
            <img src="Pictures/logo.png" alt="logo" id="grologo">
            <h1 id="main_page">Clinicou, le site des essais cliniques !</h1>
            <p class = "presentation"> Les entreprises membres :</p>
            <div id="boxes">
            <?php
              $id_entreprises = Get_id($db_name, 'ENTREPRISES', 'Id_entreprise');
              foreach ($id_entreprises as $id_entreprise) {
                  $entreprise = List_entreprise($db_name, $id_entreprise);
              }

            ?>
            </div>
            <p class="presentation">Les médecins membres :</p>
            <p><a href="https://www.linkedin.com/in/oussamaammar/">Pour plus d'informations</a></p>
            <img src="https://media.wired.com/photos/5f87340d114b38fa1f8339f9/master/w_1600%2Cc_limit/Ideas_Surprised_Pikachu_HD.jpg" alt="Surprised Pikachu">
            <p>L'eau, dans 20, 30 ans <br> il n'y en aura plus</p>
            <p><a href="#">retour au début</a></p>
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