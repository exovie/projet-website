<?php
session_start();
include('Connexion_base.php');

$conn = Connexion_base();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['modifications'])) {
    $modifications = $_SESSION['modifications'];
    $role = $modifications['role'];
    $id = $modifications['id'];
    $data = $modifications['data'];
    try {

        if ($role === 'Patient') {
            $query = "UPDATE PATIENTS SET 
                        Nom = :Nom, 
                        Prenom = :Prenom, 
                        Sexe = :Sexe, 
                        Telephone = :Telephone 
                      WHERE Id_Patient = :id";
        } elseif ($role === 'Medecin') {
            $query = "UPDATE MEDECINS SET 
                        Nom = :Nom, 
                        Prenom = :Prenom, 
                        Specialite = :Specialite, 
                        Matricule = :Matricule, 
                        Telephone = :Telephone,
                        Profil_Picture= NULL,
                        Statut_inscription= 1
                      WHERE Id_medecin = :id";
        } elseif ($role === 'Entreprise') {
            $query = "UPDATE ENTREPRISES SET 
                        Nom_entreprise = :Nom_entreprise, 
                        Telephone = :Telephone, 
                        Siret = :Siret 
                      WHERE Id_entreprise = :id";
        } else {
            throw new Exception("Rôle invalide.");
        }
        
        $stmt = $conn->prepare($query);
        $data['id'] = $id;
        $stmt->execute($data);
        unset($_SESSION['modifications']);

    } catch (Exception $e) {
        echo "<div style='color: red; text-align: center; margin-top: 20%;'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
        exit;
    }
} else {
    echo "<div style='color: red; text-align: center; margin-top: 20%;'>Aucune modification à appliquer.</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modification Appliquée</title>
    <link rel="stylesheet" href='website.css'>
    <link rel="stylesheet" href= 'navigationBar.css'>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .message-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }
        button:hover {
            background-color: #45a049;
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

    <div class="message-container">
        <h1>Modification(s) appliquée(s) avec succès</h1>
        <form action="Home_Admin.php" method="get">
            <button type="submit">Retour à la page d'accueil</button>
        </form>
    </div>
</body>
</html>
