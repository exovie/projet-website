<?php
//Connection à la base
include("../Fonctions.php");
include_once '../Notifications/fonction_notif.php';
$conn=Connexion_base();
session_start();
$_SESSION['origin'] =  $_SERVER['REQUEST_URI'];

//Récupération des infos
$query = "
    SELECT Id_medecin, Nom, Prenom, Specialite, Matricule, Telephone
    FROM MEDECINS
";
$stmt = $conn->query($query);
$medecins = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html>
<head>
    <title>Liste des Médecins</title>
    <link rel="stylesheet" href='../website.css'>
    <link rel="stylesheet" href= '../navigationBar.css'>
    <link rel="stylesheet" href='../Notifications/Notifications_style.css'>
    <link rel="stylesheet" href='Admin.css'>
    <style>
        /* Styles pour la table */
        .table-list {
            width: 80%;
            margin-top: 50px;
            background-color: white;
            justify-content: center;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
    
        h1 {
            margin-top: 120px;
            padding: 10px; /* Espace entre le texte et la bordure */
            border-radius: 6px; /* Coins arrondis de la bordure */
            color: rgb(24, 98, 104);
        }
        table {
            border-collapse: collapse;
            width: 100%; /* Table occupe toute la largeur de son conteneur */
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        /* Styles pour le bouton "Retour" */
        .back-button {
            display: block;
            width: 200px;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            text-align: center;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }

        .back-button:hover {
            background-color: #45a049;
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
            <a href="Liste_medecins.php#messagerie">
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
            <a href="Liste_medecins.php" class="close-btn">&times;</a>
            <h1>Centre de notifications</h1>
            <!-- Contenu de la messagerie -->
            <?php Affiche_notif($_SESSION['Id_user'])?>
        </div>
    </div>

    <!-- Contenu de la page -->
    <h1>Liste des Médecins</h1>
    <div class="table-list">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Spécialité</th>
                <th>Matricule</th>
                <th>Téléphone</th>
                <th>Modifier</th>
                <th>Supprimer</th>

            </tr>
        </thead>
        <tbody>
            <?php foreach ($medecins as $medecin): ?>
                <tr>
                    <td><?= htmlspecialchars($medecin['Id_medecin']) ?></td>
                    <td><?= htmlspecialchars($medecin['Nom']) ?></td>
                    <td><?= htmlspecialchars($medecin['Prenom']) ?></td>
                    <td><?= htmlspecialchars($medecin['Specialite']) ?></td>
                    <td><?= htmlspecialchars($medecin['Matricule']) ?></td>
                    <td><?= htmlspecialchars($medecin['Telephone']) ?></td>
                    
                    <td>
                        <button class='validate-btn' onclick="window.location.href='Modifier_Medecins.php?id=<?= $medecin['Id_medecin'] ?>'">Modifier</button>
                    </td>
                    <td>
                    <form action="Supprimer_utilisateur.php" method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($medecin['Id_medecin']) ?>">
                    <input type="hidden" name="role" value="Medecin">
                    <button class='reject-btn'type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</button>
                    </form>
                </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <button class="back-btn" onclick="window.location.href='Home_Admin.php'">Revenir à la page d'accueil</button>
    </div>
</body>
</html>
