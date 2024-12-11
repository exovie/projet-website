<?php
session_start();
$_SESSION['origin'] = 'Homepage.php';
$db_name = "mysql:host=localhost;dbname=website_db"; 
$_SESSION['db_name'] = $db_name;
if (isset($_SESSION['Logged_user']) && $_SESSION['Logged_user'] === true) {
    $_SESSION['role'] = $role;

} else {
    $_SESSION['role'] = 'visiteur';
}
$role = $_SESSION['role'];
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
            <p><a href="https://www.linkedin.com/in/oussamaammar/">Pour plus d'informations</a></p>
            <img src="https://media.wired.com/photos/5f87340d114b38fa1f8339f9/master/w_1600%2Cc_limit/Ideas_Surprised_Pikachu_HD.jpg" alt="Surprised Pikachu">
            <p>L'eau, dans 20, 30 ans <br> il n'y en aura plus</p>
            <p><a href="#">retour au début</a></p>
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
