<?php
// Inclure la connexion à la base de données
include_once '../Notifications/fonction_notif.php';
include_once ("../Fonctions.php");
session_start();
$conn = Connexion_base();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $role = $_POST['role'];

    try {
        // Déterminer la table selon le rôle
        if ($role === 'Patient') {
            $query = "DELETE FROM PATIENTS WHERE Id_Patient = :id";
        } elseif ($role === 'Medecin') {
            $query = "DELETE FROM MEDECINS WHERE Id_medecin = :id";
        } elseif ($role === 'Entreprise') {
            $query = "DELETE FROM ENTREPRISES WHERE Id_entreprise = :id";
        } else {
            throw new Exception("Rôle invalide.");
        }

        // Préparer et exécuter la requête
        $stmt = $conn->prepare($query);
        $stmt->execute(['id' => $id]);

        // Afficher un message de succès
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Utilisateur supprimé</title>
            <link rel=\"stylesheet\" href='../website.css'>
            <link rel=\"stylesheet\" href='../navigationBar.css'>
            <link rel=\"stylesheet\" href='../Notifications/Notifications_style.css'>
            <link rel=\"stylesheet\" href='Admin.css'>
            <style>
                body {
                    background-color: turquoise;
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                }
                .message-container {
                    background-color: white;
                    border-radius: 10px;
                    padding: 30px;
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                    text-align: center;
                    width: 50%;
                    max-width: 400px;
                }
                .message-container h1 {
                    color: red;
                    font-size: 20px;
                    margin-bottom: 20px;
                }
                .message-container button {
                    background-color: #4CAF50;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 16px;
                }
                .message-container button:hover {
                    background-color: #45a049;
                }
            </style>
        </head>
        <body>
        <!-- Code de la barre de navigation -->
        <div class=\"navbar\">
            <div id=\"logo\">
                <a href='../Homepage.php'>
                    <img src=../Pictures/logo.png alt=\"minilogo\" class=\"minilogo\">
                </a>
            </div>
            <a href='../Essais.php' class=\"nav-btn\">Essais Cliniques</a>

            <!-- Accès à la page de Gestion -->
            <a href='Home_Admin.php' class=\"nav-btn\">Gestion</a>

            <!-- Connexion / Inscription -->
            <div class=\"dropdown\">
                <a>
                    <img src=../Pictures/pictureProfil.png alt=\"pictureProfil\" style=\"cursor: pointer;\">
                </a>
                </div>
            </div>
        </div>
        
        <div class='message-container'>
            <h1>L'utilisateur a été supprimé avec succès</h1>
            <button onclick=\"window.location.href='Home_Admin.php'\">Retour à la page d'accueil</button>
        </div>
        </body>
        </html>";
    } catch (Exception $e) {
        // Afficher un message d'erreur
        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Erreur de suppression</title>
            <link rel=\"stylesheet\" href='website.css'>
            <link rel=\"stylesheet\" href='navigationBar.css'>
            <style>
                body {
                    background-color: turquoise;
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                }
                .message-container {
                    background-color: white;
                    border-radius: 10px;
                    padding: 30px;
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                    text-align: center;
                    width: 50%;
                    max-width: 400px;
                }
                .message-container h1 {
                    color: red;
                    font-size: 20px;
                    margin-bottom: 20px;
                }
                .message-container p {
                    color: #333;
                    margin-bottom: 20px;
                }
                .message-container button {
                    background-color: #4CAF50;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 16px;
                }
                .message-container button:hover {
                    background-color: #45a049;
                }
            </style>
        </head>
        <body>
        <!-- Code de la barre de navigation -->
        <div class=\"navbar\">
            <div id=\"logo\">
                <a href=\"Homepage.php\">
                    <img src=\"Pictures/logo.png\" alt=\"minilogo\" class=\"minilogo\">
                </a>
            </div>
            <a href=\"Essais.php\" class=\"nav-btn\">Essais Cliniques</a>
            <a href=\"Entreprises.php\" class=\"nav-btn\">Entreprise</a>
            <a href=\"Contact.php\" class=\"nav-btn\">Contact</a>
            <div class=\"dropdown\">
                <a href=\"Homepage.php\">
                    <img src=\"Pictures/letterPicture.png\" alt=\"letterPicture\" style=\"cursor: pointer;\">
                </a>
            </div>
            <div class=\"dropdown\">
                <a>
                    <img src=\"Pictures/pictureProfil.png\" alt=\"pictureProfil\" style=\"cursor: pointer;\">
                </a>
                <div class=\"dropdown-content\">
                <?php if (isset(\$_SESSION['Logged_user']) && \$_SESSION['Logged_user'] === true) { ?>
                    <?php 
                    if (\$_SESSION['role'] == 'Medecin') {
                        echo \"<h1 style='font-size: 18px; text-align: center;'>Dr \" . htmlspecialchars(\$_SESSION['Nom'], ENT_QUOTES, 'UTF-8') . \"</h1>\";
                    } elseif (\$_SESSION['role'] == 'Entreprise') {
                        echo \"<h1 style='font-size: 18px; text-align: center;'>\" . htmlspecialchars(\$_SESSION['Nom'], ENT_QUOTES, 'UTF-8') . \"®</h1>\";
                    } else {
                        echo \"<h1 style='font-size: 18px; text-align: center;'>\" . htmlspecialchars(\$_SESSION['Nom'], ENT_QUOTES, 'UTF-8') . \"</h1>\";
                    }
                    ?>
                    <a href=\"#\">Mon Profil</a>
                    <a href=\"Deconnexion.php\">Déconnexion</a>
                <?php } else { ?>
                    <a href=\"Connexion/Form1_connexion.php#modal\">Connexion</a>
                    <a href=\"Inscription/Form1_inscription.php#modal\">S'inscrire</a>
                <?php } ?>
                </div>
            </div>
        </div>
        <div class='message-container'>
            <h1>Erreur de suppression</h1>
            <p>Une erreur est survenue : " . htmlspecialchars($e->getMessage()) . "</p>
            <button onclick=\"window.location.href='Home_Admin.php'\">Retour à la page d'accueil</button>
        </div>
        </body>
        </html>";
    }
} else {
    header('Location: Home_Admin.php');
    exit;
}
?>
