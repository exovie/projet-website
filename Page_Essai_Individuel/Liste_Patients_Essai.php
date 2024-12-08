<?php
/*Fonction qui permet de charger les informations des patients
qui participent à un essai*/

session_start();
include("../Fonctions.php");


$id_essai = isset($_POST['id_essai']);
$conn= Connexion_base();

function Liste_Patients_Essais($conn, int $id_essai){
    
    try {
  
        $query= $conn -> prepare("
        SELECT PATIENTS.Id_patient, Nom, Prenom, Date_naissance, Sexe, Telephone, Poids, Taille, Traitements, Allergies
        FROM PATIENTS 
        JOIN PATIENTS_ESSAIS ON PATIENTS_ESSAIS.Id_patient = PATIENTS.Id_patient
        WHERE PATIENTS_ESSAIS.Id_essai = :id_essai
        ");
        $query->bindParam(':id_essai', $id_essai, PDO::PARAM_INT);
        $query->execute();

        // Récupération des résultats
        $patients = $query->fetchAll(PDO::FETCH_ASSOC);
        return $patients;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: #ffffff;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #4CAF9A;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
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
$patients = Liste_Patients_Essais($conn, $id_essai);
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

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Date de Naissance</th>
                <th>Sexe</th>
                <th>Téléphone</th>
                <th>Poids</th>
                <th>Taille (cm)</th>
                <th>Traitements</th>
                <th>Allergies</th>
                <th>Modifier</th> 
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($patients)): ?>
                <?php foreach ($patients as $patient): ?>
                    <tr>
                        <td><?= htmlspecialchars($patient['Nom']) ?></td>
                        <td><?= htmlspecialchars($patient['Prenom']) ?></td>
                        <td><?= htmlspecialchars($patient['Date_naissance']) ?></td>
                        <td><?= htmlspecialchars($patient['Sexe']) ?></td>
                        <td><?= htmlspecialchars($patient['Telephone']) ?></td>
                        <td><?= htmlspecialchars($patient['Poids']) ?></td>
                        <td><?= htmlspecialchars($patient['Taille']) ?></td>
                        <td><?= htmlspecialchars($patient['Traitements']) ?></td>
                        <td><?= htmlspecialchars($patient['Allergies']) ?></td>
                        <td>
                            <a href="Modifier_Patient_Essai.php?id_patient=<?= urlencode($patient['Id_patient']) ?>" 
                            class="modifier-btn">Modifier</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center;">Aucun patient trouvé pour cet essai.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>







    
















