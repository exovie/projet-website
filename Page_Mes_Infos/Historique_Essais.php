<?php
session_start();
include '../Fonctions.php';
include_once '../Notifications/fonction_notif.php';
include 'Fonction_Mes_infos.php';
$id_user =$_SESSION['Id_user'];
$conn = Connexion_base() ;

// Récupération de l'historique des essais
$historique = getHistoriqueEssais($conn, $id_user);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Essais Cliniques</title>
    <title>Profil utilisateur</title>
    <link rel="stylesheet" href='../website.css'>
    <link rel="stylesheet" href= '../navigationBar.css'>
    <link rel="stylesheet" href='../Notifications/Notifications_style.css'>
    <style>
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
            <a href="../Homepage.php">
                <img src="../Pictures/logo.png" alt="minilogo" class="minilogo">
            </a>
        </div>
        <a href="../Essais.php" class="nav-btn">Essais Cliniques</a>
        <a href="../Entreprises.php" class="nav-btn">Entreprise</a>

        <!-- Accès à la page de Gestion -->
        <?php if ($_SESSION['role'] == 'Admin'): ?>
            <a href="Home_Admin.php" class="nav-btn">Gestion</a>
        <?php endif; ?>

        <!-- Accès à la messagerie -->
        <?php if (isset($_SESSION['Logged_user']) && $_SESSION['Logged_user'] === true): ?>
        <div class="dropdown">
            <a href="Home_Admin.php#messagerie">
                <img src="../Pictures/letterPicture.png" alt="letterPicture" style="cursor: pointer;">
            </a>
        </div>
        <?php endif; ?>

        <!-- Connexion / Inscription -->
        <div class="dropdown">
            <a>
                <img src="../Pictures/pictureProfil.png" alt="pictureProfil" style="cursor: pointer;">
            </a>
            <div class="dropdown-content">
            <?php if (isset($_SESSION['Logged_user']) && $_SESSION['Logged_user'] === true): ?>
                <!-- Options pour les utilisateurs connectés -->
                <?php 
                if ($_SESSION['role'] == 'Medecin') {
                    echo "<h1 style='font-size: 18px; text-align: center;'>Dr " . htmlspecialchars($_SESSION['Nom'], ENT_QUOTES, 'UTF-8') . "</h1>";
                } elseif ($_SESSION['role'] == 'Entreprise') {
                    echo "<h1 style='font-size: 18px; text-align: center;'>" . htmlspecialchars($_SESSION['Nom'], ENT_QUOTES, 'UTF-8') . "®</h1>";
                } elseif(($_SESSION['role']=='Admin')){
                    echo "<h1 style='font-size: 18px; text-align: center;'>Admin</h1>";
                } else{
                    echo "<h1 style='font-size: 18px; text-align: center;'>" . htmlspecialchars($_SESSION['Nom'], ENT_QUOTES, 'UTF-8') . "</h1>";
                }
                ?>
                <a href="#">Mon Profil</a>
                <a href="../Deconnexion.php">Déconnexion</a>
            <?php else: ?>
                <!-- Options pour les utilisateurs non connectés -->
                <a href="../Connexion/Form1_connexion.php#modal">Connexion</a>
                <a href="../Inscription/Form1_inscription.php#modal">S'inscrire</a>
            <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Message Success -->
    <?php 
    if (isset($_SESSION['SuccessCode'])): 
        SuccesEditor($_SESSION['SuccessCode']);
        unset($_SESSION['SuccessCode']); // Nettoyage après affichage
    endif; 
    ?>

    <!-- Message Erreur -->
    <?php 
    if (isset($_SESSION['ErrorCode'])): 
        ErrorEditor($_SESSION['ErrorCode']);
        unset($_SESSION['ErrorCode']); // Nettoyage après affichage
    endif; 
    ?>
    
    <!-- Messagerie -->
    <div id="messagerie" class="messagerie">
        <div class="messagerie-content">
            <!-- Lien de fermeture qui redirige vers Home_Admin.php -->
            <a href="Home_Admin.php" class="close-btn">&times;</a>
            <h1>Centre de notifications</h1>
            <!-- Contenu de la messagerie -->
            <?php Affiche_notif($_SESSION['Id_user'])?>
        </div>
    </div>


    <!--Contenu de la page-->
    <div class="content">
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
    </div>
</body>
