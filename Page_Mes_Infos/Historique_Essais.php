<?php
session_start();

include("Fonction_Mes_infos.php");
//$id_user =$_SESSION['id_user'];
$id_user =6;

// Récupération de l'historique des essais
$historique = getHistoriqueEssais($conn, $id_user);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Essais Cliniques</title>
    <link rel="stylesheet" href='website.css'>
    <link rel="stylesheet" href= 'navigationBar.css'>
    <style>
    body{
        font-family: Arial, sans-serif;
        margin: 20px;
        background-color: #b0f5e7;
    }
    /* Style pour encadrer le tableau */
    .table-container {
        border: 2px solid #007BFF; /* Bordure bleue autour du cadre */
        padding: 20px; /* Espacement intérieur pour que le tableau ne touche pas les bords du cadre */
        margin: 20px auto; /* Marge autour du cadre */
        width: 90%; /* Limite la largeur du cadre à 90% de l'écran */
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Ombre légère pour donner un effet de profondeur */
        background-color: #ffffff; /* Fond blanc pour l'intérieur du cadre */
        border-radius: 10px; /* Coins arrondis pour un effet plus doux */
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }
    h1{
        margin-top:70px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    th {
        background-color: #f4f4f4;
    }

    a {
        text-decoration: none;
        color: #007BFF;
    }

    a:hover {
        text-decoration: underline;
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

    <h1>Historique des Essais Cliniques</h1>

    <?php if (count($historique) > 0): ?>
        <!-- Encadrer le tableau avec un conteneur -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Date de Création</th>
                        <th>Date de Fin</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($historique as $essai): ?>
                        <tr>
                            <td><?= htmlspecialchars($essai['Titre']) ?></td>
                            <td><?= htmlspecialchars($essai['Date_creation']) ?></td>
                            <td><?= htmlspecialchars(!empty($essai['Date_fin']) ? $essai['Date_fin'] : 'NULL') ?></td>
                            <td><?= htmlspecialchars($essai['Statut']) ?></td>
                            <td>
                                <!-- Mettre le lien vers la page de l'essai clinique en question -->
                                <a href="Page_Essai.php?id_essai=<?= urlencode($essai['Id_essai']) ?>">Voir l'essai</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button onclick="window.location.href='Menu_Mes_Infos.php';">Retour à la page précédente</button>
        </div>
    <?php else: ?>
        <p>Aucun essai clinique trouvé pour cet utilisateur.</p>
    <?php endif; ?>
</body>
