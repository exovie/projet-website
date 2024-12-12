<?php
/*Fonction qui permet de charger les informations d'un patient
qui participent à un essai*/

session_start();
include_once('../Fonctions.php');
$servername = "mysql:host=localhost;dbname=website-project"; // ou l'adresse de votre serveur
$_SESSION['servername'] = $servername;
$Id_patient=40;
//$Id_patient= isset($_POST['Id_patient']);
$conn= Connexion_base();

function Info_Patient_Essais($conn, int $Id_patient){
    
    try {
  
        $query= $conn -> prepare("
        SELECT patients.Id_patient, Nom, Prenom, Date_naissance, Sexe, Telephone, Poids, Taille, Traitements, Allergies
        FROM patients 
        WHERE Id_patient= :Id_patient
        ");
        $query->bindParam(':Id_patient', $Id_patient, PDO::PARAM_INT);
        $query->execute();

        // Récupération des résultats
        $dataPatient = $query->fetchAll(PDO::FETCH_ASSOC);
        return $dataPatient;
    } 
    catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        die();
    }
    $conn = null;
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informations des Patients - Essai <?= htmlspecialchars($id_essai) ?></title>
    <link rel="stylesheet" href='website.css'>
    <link rel="stylesheet" href= 'navigationBar.css'>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }
        h1 {
            margin-top: 100px;
            text-align: center;
        }
        .patient-info {
            background-color: #ffffff;
            padding: 70px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 20px auto;
        }
        .patient-info ul {
            list-style: none;
            padding: 0;
        }
        .patient-info li {
            margin-bottom: 15px;
            font-size: 16px;
            font-weight: normal;
        }
        .patient-info li strong {
            color: #007BFF;
        }
        .modifier-btn {
            display: inline-block;
            padding: 5px 10px;
            background-color: #007BFF;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
        }

        .modifier-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<?php
$datasPatient = Info_Patient_Essais($conn, $Id_patient);
?>

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

    <h1>Liste des patients</h1>

    <div class="patient-info">
        <?php if (!empty($datasPatient)): ?>
            <?php foreach ($datasPatient as $dataPatient): ?>
                <ul>
                    <li><strong>Nom :</strong> <?= htmlspecialchars($dataPatient['Nom']) ?></li>
                    <li><strong>Prénom :</strong> <?= htmlspecialchars($dataPatient['Prenom']) ?></li>
                    <li><strong>Date de Naissance :</strong> <?= htmlspecialchars($dataPatient['Date_naissance']) ?></li>
                    <li><strong>Sexe :</strong> <?= htmlspecialchars($dataPatient['Sexe']) ?></li>
                    <li><strong>Téléphone :</strong> <?= htmlspecialchars($dataPatient['Telephone']) ?></li>
                    <li><strong>Poids :</strong> <?= htmlspecialchars($dataPatient['Poids']) ?> kg</li>
                    <li><strong>Taille :</strong> <?= htmlspecialchars($dataPatient['Taille']) ?> cm</li>
                    <li><strong>Traitements :</strong> <?= htmlspecialchars($dataPatient['Traitements']) ?></li>
                    <li><strong>Allergies :</strong> <?= htmlspecialchars($dataPatient['Allergies']) ?></li>
                </ul>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucune information disponible pour ce patient.</p>
        <?php endif; ?>
    </div>
</body>
</html>







    
















