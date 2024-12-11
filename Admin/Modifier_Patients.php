<?php

//Connexion à la base de donnée
include("../Fonctions.php");
include_once '../Notifications/fonction_notif.php';
$conn=Connexion_base();

if (!isset($_GET['id'])) {
    header('Location: Liste_Patients.php');
    exit;
}

$id_patient = intval($_GET['id']);
$query = "SELECT Id_Patient, Nom, Prenom, Sexe, Telephone FROM PATIENTS WHERE Id_Patient = :id_patient";
$stmt = $conn->prepare($query);
$stmt->execute(['id_patient' => $id_patient]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    echo "Patient introuvable.";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Modifier Patient</title>
    <link rel="stylesheet" href='../website.css'>
    <link rel="stylesheet" href= '../navigationBar.css'>
    <link rel="stylesheet" href= '../Notifications/notification.css'>
    <style>

        /* Conteneur principal pour le contenu centré */
        .container {
            background-color: white; /* Fond blanc pour les éléments */
            padding: 30px;
            border-radius: 10px; /* Coins arrondis */
            width: 60%; /* Largeur du conteneur */
            max-width: 500px; /* Limite la largeur du conteneur */
            margin-top: 80px; /* Décale la liste un peu plus bas */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Ombre autour du conteneur */
        }

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
            <a href="Modifier_Patients.php#messagerie">
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
            <a href="Modifier_Patients.php" class="close-btn">&times;</a>
            <h1>Centre de notifications</h1>
            <!-- Contenu de la messagerie -->
            <?php Affiche_notif($_SESSION['Id_user'])?>
        </div>
    </div>

    <!-- Contenu de la page -->
    <div class="container">
        <h3>Modifier les informations du patient</h3>
        <form-modif id="form-modification" method="POST" action="Enregistrer_modif.php">
            <input type="hidden" name="role" value="Patient">
            <input type="hidden" name="id" value="<?= htmlspecialchars($patient['Id_Patient']) ?>">
            
            <label>Nom
                <input type="text" name="Nom" value="<?= htmlspecialchars($patient['Nom']) ?>">
            </label>
            <label>Prénom
                <input type="text" name="Prenom" value="<?= htmlspecialchars($patient['Prenom']) ?>">
            </label>
            <label>Sexe
                <select name="Sexe">
                    <option value="M" <?= $patient['Sexe'] == 'M' ? 'selected' : '' ?>>Masculin</option>
                    <option value="F" <?= $patient['Sexe'] == 'F' ? 'selected' : '' ?>>Féminin</option>
                </select>
            </label>
            <label>Téléphone
                <input type="text" name="Telephone" value="<?= htmlspecialchars($patient['Telephone']) ?>">
            </label>
            <!-- Conteneur des boutons -->
            <div class="buttons-container">
                <button type="submit" class="save-btn">Enregistrer les modifications</button>
            </div>
        </form>
        <form id="form-modification" method="POST" action="Liste_patients.php">
            <!-- Conteneur des boutons -->
            <div class="buttons-container">
                <button type="submit" class="cancel-bt">Retour à la liste des patients</button>
            </div>
        </form-modif>
    </div>
</body>
</html>
