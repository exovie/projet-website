<?php

//Connexion à la base
include('Connexion_base.php');
$conn=Connexion_base();

session_start();
if (!isset($_SESSION['modifications'])) {
    header('Location: admin_home.php');
    exit;
}

$modifications = $_SESSION['modifications'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href='website.css'>
    <link rel="stylesheet" href= 'navigationBar.css'>
    <link rel="stylesheet" href='admin.css'>
    <title>Confirmer les modifications</title>

    <style>
        body {
        background-color: turquoise;
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        text-align: center;
        }

    </style>

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


    <div class="container">
        <h1>Confirmer les modifications</h1>
        <p>Vous êtes sur le point de modifier les informations suivantes :</p>
        <ul>
            <?php foreach ($modifications['data'] as $key => $value): ?>
                <li><?= htmlspecialchars($key) ?> : <?= htmlspecialchars($value) ?></li>
            <?php endforeach; ?>
        </ul>
        <form method="POST" action="Appliquer_modif.php">
    <input type="hidden" name="confirm" value="true">
    <button type="submit">Valider</button>
    <?php 
        // Détermine la page de redirection en fonction du rôle
        $redirectUrl = '#'; // Valeur par défaut
        if (isset($modifications['role'])) {
            if ($modifications['role'] === 'Patient') {
                $redirectUrl = 'Liste_Patients.php';
            } elseif ($modifications['role'] === 'Medecin') {
                $redirectUrl = 'Liste_Medecins.php';
            } elseif ($modifications['role'] === 'Entreprise') {
                $redirectUrl = 'Liste_Entreprises.php';
            }
        }
    ?>
    <button 
        type="button" 
        class="cancel-button" 
        onclick="window.location.href='<?= htmlspecialchars($redirectUrl, ENT_QUOTES, 'UTF-8') ?>'">
        Annuler
    </button>
</form>

    </div>
</body>
</html>

