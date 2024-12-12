<?php
/*Fonction qui permet de charger les informations des patients
qui participent à un essai*/

session_start();
include_once '../Notifications/fonction_notif.php';
include_once ("../Fonctions.php");

if (isset($_POST['id_essai'])) {
    $id_essai = $_POST['id_essai']; // Récupère la valeur si elle existe
}else {
    $id_essai = $_SESSION['id_essai']; // Sinon, récupère la valeur de la session
}
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
        Fermer_base($conn);
        return $patients;
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
    <link rel="stylesheet" href='../navigationBar.css'>
    <link rel="stylesheet" href='../Notifications/Notifications_style.css'>
    <link rel="stylesheet" href='../Admin/Admin.css'>
    <style>
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
    <div class="table-list">
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
    <button class="back-btn" onclick="window.location.href='<?php echo $_SESSION['origin']; ?>'">Retour</button>
</div>
</div>
</body>
</html>







    
















