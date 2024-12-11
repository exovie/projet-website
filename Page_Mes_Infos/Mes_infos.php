<?php
session_start();
include '../Fonctions.php';
include_once '../Notifications/fonction_notif.php';
include 'Fonction_Mes_infos.php';

$id_user =$_SESSION['Id_user'];
$role= $_SESSION['role'];

// Récupération des informations de l'utilisateur
$userInfo = getUserInfo($conn, $id_user);


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

    <style>
        form {
            max-width: 600px;
            margin: 0 auto;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            color: green;
            margin: 10px 0;
            text-align: center;
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

    <?php
    // Récupérer le rôle de l'utilisateur
$sql= $conn -> prepare("SELECT Role FROM USERS WHERE Id_user= :id_user");
$sql->execute(['id_user' => $id_user]);
$result=$sql->fetch(PDO::FETCH_ASSOC);
$role=$result['Role'];

    ?>
    <h1>Profil utilisateur</h1>
    

    <?php if ($userInfo) : ?>
        <form method="POST" enctype="multipart/form-data">
        <?php foreach ($userInfo as $key => $value): ?>
            <?php 
                // Ignorer les clés spécifiques
                if ($key === 'Id_Patients' || $key === 'Id_medecin' || $key === 'Id_entreprise' || $key === 'Profile_picture') {
                    continue;
                }
            ?>
            
            <label for="<?= $key ?>"><?= ucfirst(str_replace('_', ' ', $key)) ?> :</label>
            <input type="text" id="<?= $key ?>" name="<?= $key ?>" value="<?= htmlspecialchars($value) ?>">
        <?php endforeach; ?>
            <!-- Afficher la photo de profil si elle existe -->
        <label for="Profile_picture">Photo de profil:</label>
        <?php if (!empty($userInfo['Profile_picture'])): ?>
            <img src="<?= $userInfo['Profile_picture'] ?>" alt="Photo de profil" style="max-width: 100px; max-height: 100px;">
        <?php endif; ?>

        <!-- Option pour télécharger une nouvelle photo de profil -->
        <input type="file" name="Profile_picture" id="Profile_picture">
        <button type="submit">Modifier</button>
        </form>
    <?php else: ?>
        <p>Aucune information trouvée pour cet utilisateur.</p>
    <?php endif; ?>

    
    <?php 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mise à jour des informations de l'utilisateur
    $success = updateUserInfo($conn, $id_user);

    // Affichage des messages d'erreur stockés dans $_SESSION
    if (isset($_SESSION['FormsErr'])): ?>
        <p class="message"><?= htmlspecialchars($_SESSION['FormsErr']) ?></p>
        <?php 
        unset($_SESSION['FormsErr']); // Supprimer le message après affichage
    endif;

}
?>
 <button onclick="window.location.href='Menu_Mes_Infos.php';">Retour à la page précédente</button>
</body>
</html>