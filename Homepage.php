<?php
session_start();
$_SESSION['origin'] = 'Homepage';
$db_name = "mysql:host=localhost;dbname=website_db"; 
$_SESSION['db_name'] = $db_name;
if (isset($_SESSION['Logged_user']) && $_SESSION['Logged_user'] === true) {
    $_SESSION['role'] = $role;

} else {
    $_SESSION['role'] = 'visiteur';
}
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
        <?php
        //if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['role'] == 'admin')) {
                echo '<a href="admin_page.php" class="nav-btn">Gestion</a>';
        //    }
        ?>
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
              $id_entreprises = Get_id( 'ENTREPRISES', 'Id_entreprise');
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
                if ($counter == 10) break;
                $medecins = List_Medecin($id_medecin);
                display_medecin($medecins);
                $counter++;
              }
            ?>
            </div>
            <p><a href="https://www.linkedin.com/in/oussamaammar/">Pour plus d'informations</a></p>
            <img src="https://media.wired.com/photos/5f87340d114b38fa1f8339f9/master/w_1600%2Cc_limit/Ideas_Surprised_Pikachu_HD.jpg" alt="Surprised Pikachu">
            <p>L'eau, dans 20, 30 ans <br> il n'y en aura plus</p>
            <p><a href="#">retour au début</a></p>
        </div>
    </div>
</body>
</html>
