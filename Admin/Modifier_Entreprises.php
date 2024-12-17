<?php
//Connection à la base
include("../Fonctions.php");
include_once '../Notifications/fonction_notif.php';
session_start();
$conn=Connexion_base();

//Vérification du role de l'utilisateur
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../Connexion/Form1_connexion.php#modal'); // Redirection si non Admin
    exit;
}
//Vérification si on a obtenue l'id qu'on souhaite modifier
if (!isset($_GET['id'])) {
    header('Location: Liste_entreprises.php');
    exit;
}

session_start();
$_SESSION['origin'] =  $_SERVER['REQUEST_URI'];

$id_entreprise = intval($_GET['id']);
$query = "SELECT Id_entreprise, Nom_entreprise, Telephone, Siret FROM ENTREPRISES WHERE Id_entreprise = :id_entreprise";
$stmt = $conn->prepare($query);
$stmt->execute(['id_entreprise' => $id_entreprise]);
$entreprise = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$entreprise) {
    echo "Entreprise introuvable.";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Modifier Entreprise</title>
    <link rel="stylesheet" href='../website.css'>
    <link rel="stylesheet" href= '../navigationBar.css'>
    <link rel="stylesheet" href= '../Notifications/notification.css'>
    <link rel="stylesheet" href='/Admin/Admin.css'>
    <style>
        /* Styles pour le formulaire */
        form {
            display: flex;
            flex-direction: column; /* Alignement des éléments en colonne */
            gap: 15px; /* Espacement entre les éléments du formulaire */
        }

        label {
            font-size: 15px; /* Taille de la police pour les labels */
            display: inline-block;
            margin-bottom: 5px;
        }

        input[type="text"], select {
            padding: 10px;
            font-size: 16px;
            border: 2px solid #ccc; /* Bordure grise pour les champs */
            border-radius: 5px; /* Coins arrondis pour les champs */
            width: 100%; /* Les champs prennent toute la largeur disponible */
            box-sizing: border-box; /* Assure que padding et border ne dépassent pas la largeur */
        }

        /* Bouton "Enregistrer les modifications" (vert) */
        .save-btn {
            background-color: #4CAF50;
        }
        .save-btn:hover {
         background-color: #45a049; /* Couleur au survol */
        }

        /* Bouton "Retour à la page précédente" */
        .cancel-btn {
        background-color: #f44336;
        }
        .cancel-btn:hover {
        background-color: #d32f2f; /* Couleur au survol */
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
            <a href= "Liste_entreprises.php#messagerie">
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
    


    <!-- Contenu Principal-->
    <div class="content">
        <h1>Modifier les informations de l'Entreprise</h1>
        <form id="form-modification" method="POST" action="Enregistrer_modif.php">
            <input type="hidden" name="role" value="Entreprise">
            <input type="hidden" name="id" value="<?= htmlspecialchars($entreprise['Id_entreprise']) ?>">
            <label>Nom de l'entreprise <input type="text" name="Nom_entreprise" value="<?= htmlspecialchars($entreprise['Nom_entreprise']) ?>"></label><br>
            <label>Téléphone <input type="text" name="Telephone" value="<?= htmlspecialchars($entreprise['Telephone']) ?>"></label><br>
            <label>Siret <input type="text" name="Siret" value="<?= htmlspecialchars($entreprise['Siret']) ?>"></label><br>
            <!-- Conteneur des boutons -->
            <div class="buttons-container">
                <button type="submit" class="save-btn">Enregistrer les modifications</button>
            </div>
        </form>
        <form id="form-modification" method="POST" action="Liste_entreprises.php">
            <!-- Conteneur des boutons -->
            <div class="buttons-container">
                <button type="submit" class="cancel-bt">Retour à la liste des entreprises</button>
            </div>
        </form>
        </div>
</body>
</html>
