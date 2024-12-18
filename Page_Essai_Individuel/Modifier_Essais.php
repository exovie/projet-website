<?php
session_start();
include_once("Fonction_Modif_Essais.php");
include_once '../Notifications/fonction_notif.php';
include_once ("../Fonctions.php");


$Id_essai = $_SESSION['Id_essai']; // Sinon, récupère la valeur de la session

// Récupération des informations de l'utilisateur
$EssaiInfo = getEssaiInfo($conn, $Id_essai);

//Variable pour stocker le message
$message = null;
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mise à jour des informations
    $success = updateEssaiInfo($conn, $Id_essai);

    // Définir le message en fonction du résultat
    if ($success) {
        $_SESSION['message'] = "Modifications faites avec succès.";
    } else {
        $_SESSION['message'] = "Erreur de modification."; // Stocke le message dans la session
    }
    // Rediriger vers la même page pour afficher les mises à jour
    header("Location: Modifier_Essais.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil utilisateur</title>
    <link rel="stylesheet" href='../website.css'>
    <link rel="stylesheet" href= '../navigationBar.css'>
    <link rel="stylesheet" href='../Notifications/Notifications_style.css'>
    <link rel="stylesheet" href='../Admin/Admin.css'>
    <style>

        form {
            background-color: white; 
            padding: 50px; 
            border-radius: 10px; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
            max-width: 600px; 
            width: 100%; 
            margin-top: 20px;
        }

        label {
            display: block; 
            margin-bottom: 8px; 
            font-weight: bold; 
        }
        input, textarea {
            width: 100%; 
            padding: 20px; 
            margin-bottom: 15px; 
            border: 1px solid #ccc; 
            border-radius: 5px; 
            box-sizing: border-box; 
            font-size: 13px; 
        }
        textarea {
            resize: vertical; 
            max-height: 500px; /* Hauteur maximale pour limiter l'expansion */
            overflow-y: scroll; /* Ajout de la barre de défilement verticale */
        }
        input[readonly], textarea[readonly] {
            background-color: #f0f0f0; 
            color: #777; 
            cursor: not-allowed; 
        }
        input[type="submit"], .back-button {
            background-color: #007BFF; 
            color: white; 
            border: none; 
            padding: 12px 20px; 
            cursor: pointer; 
            border-radius: 5px; 
            font-size: 16px; 
            text-align: center;
            display: block;
            margin: auto;
            margin-top: 10px;
            text-decoration: none;
        }
        input[type="submit"]:hover, .back-button:hover {
            background-color: #0056b3; 
        }
        .message {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            padding: 20px;
            border-radius: 5px;
        }
        .success {
        background-color: #d4edda; /* Vert clair */
        color: #155724; /* Vert foncé pour le texte */
        border: 1px solid #c3e6cb; /* Bordure verte claire */
        }

        .error {
        background-color: #f8d7da; /* Rouge clair */
        color: #721c24; /* Rouge foncé pour le texte */
        border: 1px solid #f5c6cb; /* Bordure rouge claire */
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
            <a href="../Admin/Home_Admin.php" class="nav-btn">Gestion</a>
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

    <!-- Contenu Principal-->
    <div class="content">
    <h1>Modification des informations de l'essai</h1>
    <!-- Affichage du message -->
    <?php if (isset($_SESSION['message'])): ?>
            <div class="message <?= strpos($_SESSION['message'], 'succès') !== false ? 'success' : 'error' ?>">
                <?= htmlspecialchars($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message']); // Supprime le message après affichage ?>
        <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
    <?php if ($EssaiInfo): ?>
        <?php foreach ($EssaiInfo as $key => $value): ?>
            <?php if ($key === 'Id_essai'): continue; endif; ?>

            <?php if ($key === 'Date_lancement'): ?>
                <label for="Date_lancement">Date de lancement :</label>
                <textarea id="Date_lancement" name="Date_lancement" readonly>
                    <?= htmlspecialchars($value ?: ' ') ?>
                </textarea>
            
            <?php elseif ($key === 'Date_creation' || $key === 'Statut'): ?>
                <label for="<?= $key ?>"><?= ucfirst(str_replace('_', ' ', $key)) ?> :</label>
                <textarea id="<?= $key ?>" name="<?= $key ?>" readonly><?= htmlspecialchars($value ?: 'NULL') ?></textarea>
            
            <?php elseif ($key === 'Nb_medecins' || $key === 'Nb_patients'): ?>
                <label for="<?= $key ?>"><?= ucfirst(str_replace('_', ' ', $key)) ?> :</label>
                <input type="number" id="<?= $key ?>" name="<?= $key ?>" value="<?= htmlspecialchars($value) ?>" required>
            
            <?php else: ?>
                <label for="<?= $key ?>"><?= ucfirst(str_replace('_', ' ', $key)) ?> :</label>
                <textarea id="<?= $key ?>" name="<?= $key ?>"><?= htmlspecialchars($value) ?></textarea>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
    <input type="submit" value="Enregistrer">
    <button type="button" class="back-btn" onclick="window.location.href='../Essai_individuel.php'">Retour</button>
    </form>
    <form method="POST" action="../Essai_individuel.php">
        <?php
            $historique = historique_patient($Id_patient);
            Display_essais($historique);

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['essai_indi'])) {
                header("Location: ../Essai_individuel.php");
            }
        ?>
        </form>
</div>
</body>
</html>
