<?php
session_start();
$_SESSION['origin'] = 'admin_page';
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
    <link rel="stylesheet" href= 'gestion.css'>

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
        <a href="admin_page.php" class="nav-btn">Gestion</a>
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
        <div id="boxes">
            <?php
              if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_patient'])) {
                // Appeler une fonction PHP
                $id_patient = $_POST['id_patient'];
                $patient = get_patient($id_patient);
                display_patient_unique($patient);
                switch ($role){
                    case 'admin':
                        echo '<form method="POST">';
                        echo '<input type="hidden" name="id_patient" value="'.$id_patient.'">';
                        echo '<button type="submit" name="accepter" class="btn">Accepter</button>';
                        echo '<button type="submit" name="refuser" class="btn">Refuser</button>';
                        echo '</form>';
                        break;
                    case 'entreprise':
                        echo '<form method="POST">';
                        echo '<input type="hidden" name="id_patient" value="'.$id_patient.'">';
                        echo '<button type="submit" name="accepter" class="btn">Accepter</button>';
                        echo '</form>';
                        break;
                    case 'medecin':
                        echo '<form method="POST">';
                        echo '<input type="hidden" name="id_patient" value="'.$id_patient.'">';
                        echo '<button type="submit" name="accepter" class="btn">Accepter</button>';
                        echo '</form>';
                        break;
                }
            }

            ?>
        </div>
    </div>
</body>
</html>