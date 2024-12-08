<?php
session_start();
include("../Fonctions.php");

// Connexion à la base
$conn = Connexion_base();

// Récupérer l'ID du patient
if (!isset($_GET['id_patient'])) {
    header('Location: Liste_Patients_Essai.php');
    exit;
}

$id_patient = intval($_GET['id_patient']);

// Récupérer les informations actuelles du patient
try {
    $query = $conn->prepare("
        SELECT Nom, Prenom, Date_naissance, Sexe, Telephone, Poids, Taille, Traitements, Allergies 
        FROM patients 
        WHERE Id_patient = :id_patient
    ");
    $query->bindParam(':id_patient', $id_patient, PDO::PARAM_INT);
    $query->execute();
    $patient = $query->fetch(PDO::FETCH_ASSOC);
    
    if (!$patient) {
        echo "Patient introuvable.";
        exit;
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit;
}

// Mettre à jour les informations après soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $telephone = $_POST['telephone'];
        $poids = $_POST['poids'];
        $taille = $_POST['taille'];
        $traitements = $_POST['traitements'];
        $allergies = $_POST['allergies'];

        $update = $conn->prepare("
            UPDATE patients
            SET Telephone = :telephone, Poids = :poids, Taille = :taille, Traitements = :traitements, Allergies = :allergies
            WHERE Id_patient = :id_patient
        ");
        $update->bindParam(':telephone', $telephone, PDO::PARAM_STR);
        $update->bindParam(':poids', $poids, PDO::PARAM_STR);
        $update->bindParam(':taille', $taille, PDO::PARAM_STR);
        $update->bindParam(':traitements', $traitements, PDO::PARAM_STR);
        $update->bindParam(':allergies', $allergies, PDO::PARAM_STR);
        $update->bindParam(':id_patient', $id_patient, PDO::PARAM_INT);

        $update->execute();

        // Afficher la fenêtre modale
        echo "<script>
            window.onload = function() {
                document.getElementById('modal').style.display = 'block';
            };
        </script>";
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Patient</title>
    <link rel="stylesheet" href="website.css">
    <link rel="stylesheet" href= 'navigationBar.css'>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }
        h1 {
            text-align: center;
            margin-top: 50px;
        }
        form {
            max-width: 500px;
            margin: auto;
            background: #ffffff;
            padding: 50px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #4CAF9A;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a086;
        }
        /* Styles de la fenêtre modale */
        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #ffffff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
            text-align: center;
            border-radius: 5px;
            margin-top: 90px;
        }
        .modal button {
            margin-top: 120px;
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

    <h1>Modifier les informations du patient</h1>
    
    <form method="POST">
    <label for="nom">Nom :</label>
    <input type="text" id="nom" value="<?= htmlspecialchars($patient['Nom']) ?>" disabled>

    <label for="prenom">Prénom :</label>
    <input type="text" id="prenom" value="<?= htmlspecialchars($patient['Prenom']) ?>" disabled>

    <label for="date_naissance">Date de Naissance :</label>
    <input type="date" id="date_naissance" value="<?= htmlspecialchars($patient['Date_naissance']) ?>" disabled>

    <label for="sexe">Sexe :</label>
    <input type="text" id="sexe" value="<?= htmlspecialchars($patient['Sexe']) ?>" disabled>

    <label for="telephone">Téléphone :</label>
    <input type="text" id="telephone" name="telephone" value="<?= htmlspecialchars($patient['Telephone']) ?>" required>

    <label for="poids">Poids (kg) :</label>
    <input type="number" id="poids" name="poids" value="<?= htmlspecialchars($patient['Poids']) ?>" required>

    <label for="taille">Taille (cm) :</label>
    <input type="number" id="taille" name="taille" value="<?= htmlspecialchars($patient['Taille']) ?>" required>

    <label for="traitements">Traitements :</label>
    <textarea id="traitements" name="traitements" rows="3"><?= htmlspecialchars($patient['Traitements']) ?></textarea>

    <label for="allergies">Allergies :</label>
    <textarea id="allergies" name="allergies" rows="3"><?= htmlspecialchars($patient['Allergies']) ?></textarea>

    <button type="submit">Valider les modifications</button>
</form>


    <!-- Fenêtre modale -->
    <div id="modal" class="modal">
            <p>Les modifications ont été apportées avec succès.</p>
            <button onclick="window.location.href='Liste_Patients_Essai.php';">Retour à la liste des patients</button>
        </div>
</body>
</html>
