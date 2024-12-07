<?php
// Exemple : Récupérer le rôle depuis une session ou une base de données
$role='Entreprise';
//$role = $_SESSION['role']; // Par exemple : "Patient", "Medecin", ou "Entreprise"

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu basé sur le rôle</title>
    <link rel="stylesheet" href='website.css'>
    <link rel="stylesheet" href= 'navigationBar.css'>
    <style>
        /* CSS pour centrer les boutons */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;  /* Utilise toute la hauteur de la fenêtre */
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .button-container {
            display: flex;
            flex-direction: row;  /* Garde les boutons en ligne */
            gap: 20px; /* Espacement entre les boutons */
            justify-content: center; /* Centre les boutons horizontalement */
            align-items: center; /* Centre les boutons verticalement */
        }

        .button-container a {
            text-decoration: none;
            color: white;
            background-color: #007BFF;
            padding: 15px 25px;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .button-container a:hover {
            background-color: #0056b3;
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

    <div class="button-container">
        <!-- Boutons communs à tous les rôles -->
        <a href="Mes_infos.php">Mes Infos</a>
        <a href="Historique_Essais.php">Mes Essais</a>

        <!-- Bouton spécifique pour "Entreprise" -->
        <?php if ($role === "Entreprise"): ?>
            <a href="Page_Creer_Essai.php">Créer un Essai</a>
        <?php endif; ?>
    </div>
</body>
</html>
