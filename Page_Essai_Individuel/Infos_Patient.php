<?php
/*Fonction qui permet de charger les informations d'un patient
qui participent à un essai*/

session_start();
include_once '../Notifications/fonction_notif.php';
include_once ("../Fonctions.php");

if (isset($_POST['Id_patient'])) {
    $Id_patient = $_POST['Id_patient']; }
    else {
        if(isset($_SESSION['Id_patient_redirect']))
        $Id_patient = $_SESSION['Id_patient_redirect'];}

function Info_Patient_Essais(int $Id_patient){
    $conn= Connexion_base();
    try {
        $query= $conn -> prepare("
        SELECT Id_patient, Nom, Prenom, Date_naissance, Sexe, Telephone, Poids, Taille, Traitements, Allergies
        FROM PATIENTS 
        WHERE Id_patient= :Id_patient
        ");
        $query->bindParam(':Id_patient', $Id_patient, PDO::PARAM_INT);
        $query->execute();

        // Récupération des résultats
        $dataPatient = $query->fetchAll(PDO::FETCH_ASSOC);
        Fermer_base($conn);
        return $dataPatient;
    } 
    catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        Fermer_base($conn);
        die();
    }
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informations des Patients - Essai <?= htmlspecialchars($id_essai) ?></title>
    <link rel="stylesheet" href='../website.css'>
    <link rel="stylesheet" href= '../navigationBar.css'>
    <link rel="stylesheet" href='../Notifications/Notifications_style.css'>
    <link rel="stylesheet" href='../Admin/Admin.css'>
    <style>
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
$datasPatient = Info_Patient_Essais($Id_patient);
?>

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
            <a href="../Admin/Home_Admin.php" class="nav-btn">Gestion</a>
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
    <div class="content">

    <h1>Fiche Patient</h1>
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
        <button class="back-btn" onclick="window.location.href='../<?php echo $_SESSION['origin']; ?>'">Retour</button>
        <form method="POST" action="../Essai_individuel.php">
        <?php
            $historique = historique_patient($Id_patient);
            Display_essais($historique);

            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['essai_indi'])) {
                header("Location: ../Essai_individuel.php");
            }
        ?>
        </form>
</body>
</html>