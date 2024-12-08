<?php

// Vérification du rôle de l'utilisateur
session_start();
$_SESSION['Role'] = 'Admin';
if ($_SESSION['Role'] !== 'Admin') {
    header('Location: login.php'); // Redirection si non Admin
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Accueil Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <link rel="stylesheet" href='website.css'>
    <link rel="stylesheet" href= 'navigationBar.css'>
    <style>
        body {
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: turquoise;
            font-family: Arial, sans-serif;
        }
        h1 {
            position: absolute;
            top: 60px; /* Déplace le message un peu plus bas */
            text-align: center;
            color: black;
        }
        .button-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr); /* Deux colonnes de largeur égale */
            gap: 20px;
            margin-top: 50px; /* Ajoute un espace en haut si nécessaire */
        }
        button {
            background-color: turquoise;
            color: black;
            font-size: 18px;
            font-weight: bold;
            border: 3px solid white;
            border-radius: 8px;
            padding: 15px;
            width: 200px; /* Largeur fixe pour tous les boutons */
            height: 70px; /* Hauteur fixe pour tous les boutons */
            cursor: pointer;
            text-align: center;
        }
        button:hover {
            background-color: white;
            color: turquoise;
        }
    </style>
</head>
<body>

    <!-- Bandeau de navigation -->
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

    <!-- Contenu principal -->
    <h1>Bienvenue, Admin</h1>
    <div class="button-container">
        <button onclick="window.location.href='Liste_patients.php'">Liste Patients</button>
        <button onclick="window.location.href='liste_medecins.php'">Liste Médecins</button>
        <button onclick="window.location.href='liste_entreprises.php'">Liste Entreprises</button>
        <button onclick="window.location.href='validation_en_attente.php'">Validation en attente</button>
    </div>

</body>
</html>