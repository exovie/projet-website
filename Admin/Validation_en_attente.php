<?php
// Connexion à la base de données
include("../Fonctions.php");
include_once '../Notifications/fonction_notif.php';
$conn = Connexion_base();

// Vérification du rôle de l'utilisateur
session_start();
if ($_SESSION['role'] !== 'Admin') {
    header('Location: ../Connexion/Form1_connexion.php#modal'); // Redirection si non Admin
    exit;
}

// Vérifier l'action de validation ou de refus
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['validate'])) {
        $userId = $_POST['userId'];
        $role = $_POST['role'];

        // Mettre à jour la base de données en fonction du rôle
        if ($role == 'Medecin') {
            $query = "UPDATE MEDECINS SET Statut_inscription = 1 WHERE Id_medecin = :userId";
        } elseif ($role == 'Entreprise') {
            $query = "UPDATE ENTREPRISES SET Verif_inscription = 1 WHERE Id_entreprise = :userId";
        }

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }

    if (isset($_POST['reject'])) {
        $userId = $_POST['userId'];
        $role = $_POST['role'];

        // Supprimer l'utilisateur de la base de données en fonction du rôle
        if ($role == 'Medecin') {
            $query = "DELETE FROM MEDECINS WHERE Id_medecin = :userId";
        } elseif ($role == 'Entreprise') {
            $query = "DELETE FROM ENTREPRISES WHERE Id_entreprise = :userId";
        }

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }
}

// Récupérer les médecins et les entreprises en attente de validation
$query = "
    SELECT USERS.Id_user, USERS.Role, 
           CASE 
               WHEN USERS.Role = 'Medecin' THEN MEDECINS.Statut_inscription
               WHEN USERS.Role = 'Entreprise' THEN ENTREPRISES.Verif_inscription
           END AS Verification,
           MEDECINS.Id_medecin, MEDECINS.Nom AS Nom_medecin, MEDECINS.Prenom, MEDECINS.Specialite, MEDECINS.Telephone, MEDECINS.Matricule,
           ENTREPRISES.Id_entreprise, ENTREPRISES.Nom_entreprise, ENTREPRISES.Telephone AS Tel_entreprise, ENTREPRISES.Siret
    FROM USERS
    LEFT JOIN MEDECINS ON USERS.Id_user = MEDECINS.Id_medecin AND USERS.Role = 'Medecin'
    LEFT JOIN ENTREPRISES ON USERS.Id_user = ENTREPRISES.Id_entreprise AND USERS.Role = 'Entreprise'
    WHERE (MEDECINS.Statut_inscription = 0 AND USERS.Role = 'Medecin') 
       OR (ENTREPRISES.Verif_inscription = 0 AND USERS.Role = 'Entreprise')
";
$stmt = $conn->query($query);
$validations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation en attente</title>
    <link rel="stylesheet" href='../website.css'>
    <link rel="stylesheet" href= '../navigationBar.css'>
    <link rel="stylesheet" href='../Notifications/Notifications_style.css'>
    <link rel="stylesheet" href='Admin.css'>
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
<div class="content">
<div class="table-list">
    <h1>Utilisateurs en attente de validation</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Détails</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($validations as $validation): ?>
                <tr>
                    <td>
                        <?php
                            // Affichage de l'ID utilisateur selon le rôle
                            if ($validation['Role'] == 'Medecin') {
                                echo htmlspecialchars($validation['Id_medecin']);
                            } elseif ($validation['Role'] == 'Entreprise') {
                                echo htmlspecialchars($validation['Id_entreprise']);
                            }
                        ?>
                    </td>
                    <td>
                        <?php
                            // Affichage du nom et prénom ou de l'entreprise selon le rôle
                            if ($validation['Role'] == 'Medecin') {
                                echo htmlspecialchars($validation['Nom_medecin']) . ' ' . htmlspecialchars($validation['Prenom']);
                            } elseif ($validation['Role'] == 'Entreprise') {
                                echo htmlspecialchars($validation['Nom_entreprise']);
                            }
                        ?>
                    </td>
                    <td>
                        <?php
                            // Affichage des détails en fonction du rôle
                            if ($validation['Role'] == 'Medecin') {
                                echo "Spécialité: " . htmlspecialchars($validation['Specialite']) . "<br>Matricule: " . htmlspecialchars($validation['Matricule']);
                            } elseif ($validation['Role'] == 'Entreprise') {
                                echo "Siret: " . htmlspecialchars($validation['Siret']);
                            }
                            echo "<br>Téléphone: " . htmlspecialchars($validation['Telephone']);
                        ?>
                    </td>
                    <td>
                        <?php if ($validation['Verification'] == 0): ?>
                            <form action="Validation_en_attente.php" method="POST" style="display:inline;">
                                <input type="hidden" name="userId" value="<?php echo $validation['Id_user']; ?>">
                                <input type="hidden" name="role" value="<?php echo $validation['Role']; ?>">
                                <button type="submit" name="validate" class="validate-btn">Valider</button>
                            </form>
                            <form action="Validation_en_attente.php" method="POST" style="display:inline;">
                                <input type="hidden" name="userId" value="<?php echo $validation['Id_user']; ?>">
                                <input type="hidden" name="role" value="<?php echo $validation['Role']; ?>">
                                <button type="submit" name="reject" class="reject-btn">Refuser</button>
                            </form>
                        <?php else: ?>
                            <span>Déjà validé</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Bouton revenir à la page d'accueil -->
    <a href="Home_Admin.php" class="back-btn">Revenir à la page d'accueil</a>
</div>
</div>
</body>
</html>
