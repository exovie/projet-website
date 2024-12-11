<?php
//Connection à la base
include("../Fonctions.php");
include_once '../Notifications/fonction_notif.php';
$conn=Connexion_base();


if (!isset($_GET['id'])) {
    header('Location: Liste_medecins.php');
    exit;
}

session_start();
$_SESSION['origin'] =  $_SERVER['REQUEST_URI'];

$id_medecin = intval($_GET['id']);
$query = "SELECT Id_medecin, Nom, Prenom, Specialite, Matricule, Telephone FROM MEDECINS WHERE Id_medecin = :id_medecin";
$stmt = $conn->prepare($query);
$stmt->execute(['id_medecin' => $id_medecin]);
$medecin = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$medecin) {
    echo "Médecin introuvable.";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Modifier Médecin</title>
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

        /* Bouton "Retour à la page précédente" (rouge) */
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
        <a href="../Entreprises.php" class="nav-btn">Entreprise</a>

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
            <a href="<?= $_SESSION['origin'] ?>" class="close-btn">&times;</a>
            <h1>Centre de notifications</h1>
            <!-- Contenu de la messagerie -->
            <?php Affiche_notif($_SESSION['Id_user'])?>
        </div>
    </div>

    <!-- Contenu Principal-->
    <div class="container">
        <h1>Modifier les informations du Médecin</h1>
        <form-modif id="form-modification" method="POST" action="Enregistrer_modif.php">
            <input type="hidden" name="role" value="Medecin">
            <input type="hidden" name="id" value="<?= htmlspecialchars($medecin['Id_medecin']) ?>">
            <label>Nom <input type="text" name="Nom" value="<?= htmlspecialchars($medecin['Nom']) ?>"></label><br>
            <label>Prénom <input type="text" name="Prenom" value="<?= htmlspecialchars($medecin['Prenom']) ?>"></label><br>
            <label>Spécialité <input type="text" name="Spécialité" value="<?= htmlspecialchars($medecin['Specialite']) ?>"></label><br>
            <label>Matricule <input type="text" name="Matricule" value="<?= htmlspecialchars($medecin['Matricule']) ?>"></label><br>
            <label>Téléphone <input type="text" name="Telephone" value="<?= htmlspecialchars($medecin['Telephone']) ?>"></label><br>
            <!-- Conteneur des boutons -->
            <div class="buttons-container">
                <button type="submit" class="save-btn">Enregistrer les modifications</button>
            </div>
        </form>
        <form id="form-modification" method="POST" action="Liste_medecins.php">
            <!-- Conteneur des boutons -->
            <div class="buttons-container">
                <button type="submit" class="cancel-bt">Retour à la liste des médecins</button>
            </div>
        </form-modif>
        </div>
</body>
</html>
