<?php
session_start();
$_SESSION['origin'] = 'Homepage';
$servername = "mysql:host=localhost;dbname=website-project"; // ou l'adresse de votre serveur
$_SESSION['servername'] = $servername;
?>

<!DOCTYPE html>
<html lang="fr">
  <head>
    <title>That's what she said</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="'utf-8">
    <link rel="stylesheet" href="website.css">
  </head>
  <body>
    <div id="upper" class="border">
      <nav id="navbar">
        <ul>
          <li id="logo">
            <a href="Homepage.php">
              <img src="Pictures/minilogo.png" alt="minilogo">
            </a>
          </li>
          <li class="capsule-border capsule-button"><a href="Essais.php"><input type="submit" value="Essais cliniques"></a></li>
          <li class="capsule-border" id="Entreprises"><a href="Entreprises.php">Entreprises</a></li>
          <li>
            <div id="main_search" class="search">
              <input type="search" name="pattern" class="search-query tt-input" placeholder="Search" accesskey="s" spellcheck="false" dir="auto" aria-owns="tt-91ab948f-4f49-cd37-fddf-5046fc840e9c_listbox" aria-controls="tt-91ab948f-4f49-cd37-fddf-5046fc840e9c_listbox" role="combobox" aria-autocomplete="list" aria-expanded="false">
            </div>
          </li>
          <li>
            <p id = envelope></p>
          </li>
          <li>
            <ul id="Subscription">
              <li><img src="Pictures/subscription.png" alt="petit bonhomme"></li>
              <li class="capsule-border"><p><a href="connexion.php">Se connecter</a> / <a href="inscription.php">Je m'inscris</a></p></li>
            </ul>
          </li>
        </ul>
      </nav>
    </div>
    <div class="banner">
    </div>
    <div class="content">
      <img src="Pictures/logo.png" alt="logo" id="grologo">
      <h1 id="main_page">Clinicou, le site des <strong>essais cliniques !</strong></h1>
      <h2>Le site qui vous permet de vous inscrire <em> facilement </em> pour crever pour big pharma</h2>
      <p class = "sarcasm"> Un max de fun</p>
      <p><a href="https://www.linkedin.com/in/oussamaammar/">Pour plus d'informations</a></p>
      <img src="https://media.wired.com/photos/5f87340d114b38fa1f8339f9/master/w_1600%2Cc_limit/Ideas_Surprised_Pikachu_HD.jpg" alt="Surprised Pikachu">
      <p>L'eau, dans 20, 3O ans <br> il n'y en aura plus</p>
      <p> <a href="#main_page">retour au début</a></p>
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
-->
