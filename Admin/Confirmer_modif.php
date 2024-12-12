<?php

include_once("Fonctions_admin.php");
include_once ('../Notifications/fonction_notif.php');

session_start();
if (!isset($_SESSION['modifications'])) {
    header('Location: Home_Admin.php');
    exit;
}

$modifications = $_SESSION['modifications'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href='../website.css'>
    <link rel="stylesheet" href= '../navigationBar.css'>
    <link rel="stylesheet" href='../Notifications/Notifications_style.css'>
    <title>Confirmer les modifications</title>

    <style>
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        p {
            font-size: 1.2em;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin-bottom: 10px;
        }
        form {
            margin-top: 20px;
        }
        .cancel-button {
            background-color: #f44336;
        }
        .cancel-button:hover {
            background-color: #d32f2f;
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

        <!-- Accès à la page de Gestion -->
        <?php if ($_SESSION['role'] == 'Admin'): ?>
            <a href="Home_Admin.php" class="nav-btn">Gestion</a>
        <?php endif; ?>

        <!-- Accès à la messagerie -->
        <?php if (isset($_SESSION['Logged_user']) && $_SESSION['Logged_user'] === true): ?>
        <div class="dropdown">
            <a href= "<?= $_SESSION['origin'] ?>#messagerie">
                <img src="../Pictures/letterPicture.png" alt="letterPicture" style="cursor: pointer;">
            </a>
            <!-- Affichage de la pastille -->
            <?php 
            $showBadge = Pastille_nombre($_SESSION['Id_user']);
            if ($showBadge > 0): ?>
                <span class="notification-badge"><?= htmlspecialchars($showBadge) ?></span>
            <?php endif; ?>
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
                if ($_SESSION["role"]!=='Admin'&& $_SESSION['Logged_user'] === true)
                {echo "<a href='../Page_Mes_Infos/Menu_Mes_Infos.php'>Mon Profil</a>";} ?>
                <a href="../Deconnexion.php">Déconnexion</a>
            <?php else: ?>
                <!-- Options pour les utilisateurs non connectés -->
                <a href="../Connexion/Form1_connexion.php#modal">Connexion</a>
                <a href="../Inscription/Form1_inscription.php#modal">S'inscrire</a>
            <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Messagerie -->
    <div id="messagerie" class="messagerie">
        <div class="messagerie-content">
            <!-- Lien de fermeture qui redirige vers Home_Admin.php -->
            <a href="<?= $_SESSION['origin'] ?>" class="close-btn">&times;</a>
            <h1>Centre de notifications</h1>
            <!-- Contenu de la messagerie -->
            <?php Affiche_notif($_SESSION['Id_user'])?>
        </div>
    </div>

    
    <!-- Contenu de la page -->
    <div class="content">
        <div class="container">
        <h1>Confirmer les modifications</h1>
        <p>Vous êtes sur le point de modifier les informations suivantes :</p>
        <ul>
            <?php foreach ($modifications['data'] as $key => $value): ?>
                <li><?= htmlspecialchars($key) ?> : <?= htmlspecialchars($value) ?></li>
            <?php endforeach; ?>
        </ul>

        <!-- Formulaire de confirmation -->
        <form method="POST">
        <input type="hidden" name="confirm" value="true">
        <button type="submit">Valider</button>

        
        <!-- Bouton d'annulation -->
        <?php 
            // Détermine la page de redirection en fonction du rôle
            $redirectUrl = ''; // Valeur par défaut
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
    </div>

        <!-- Exécution si le bouton valider est appuyé  -->
        <?php 
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['confirm']) && $_POST['confirm'] === 'true') {
                // Exécute la requête de modification
                Save_BdD_modif();
                //Message de confirmation
                $_SESSION['SuccessCode'] = 5;
                // Effectue la redirection
                header("Location: " . htmlspecialchars($redirectUrl, ENT_QUOTES, 'UTF-8'));
                exit;
            }
        }
        ?>

</body>
</html>

