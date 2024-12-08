<?php
// Connexion à la base de données
include('Connexion_base.php');
$conn = Connexion_base();

// Vérifier l'action de validation ou de refus
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['validate'])) {
        $userId = $_POST['userId'];
        $role = $_POST['role'];

        // Mettre à jour la base de données en fonction du rôle
        if ($role == 'Medecin') {
            $query = "UPDATE medecins SET Statut_inscription = 1 WHERE Id_medecin = :userId";
        } elseif ($role == 'Entreprise') {
            $query = "UPDATE entreprises SET Verif_inscription = 1 WHERE Id_entreprise = :userId";
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
            $query = "DELETE FROM medecins WHERE Id_medecin = :userId";
        } elseif ($role == 'Entreprise') {
            $query = "DELETE FROM entreprises WHERE Id_entreprise = :userId";
        }

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();
    }
}

// Récupérer les médecins et les entreprises en attente de validation
$query = "
    SELECT u.Id_user, u.Role, 
           CASE 
               WHEN u.Role = 'Medecin' THEN m.Statut_inscription
               WHEN u.Role = 'Entreprise' THEN e.Verif_inscription
           END AS Verification,
           m.Id_medecin, m.Nom AS Nom_medecin, m.Prenom, m.Specialite, m.Telephone, m.Matricule,
           e.Id_entreprise, e.Nom_entreprise, e.Telephone AS Tel_entreprise, e.Siret
    FROM users u
    LEFT JOIN medecins m ON u.Id_user = m.Id_medecin AND u.Role = 'Medecin'
    LEFT JOIN entreprises e ON u.Id_user = e.Id_entreprise AND u.Role = 'Entreprise'
    WHERE (m.Statut_inscription = 0 AND u.Role = 'Medecin') 
       OR (e.Verif_inscription = 0 AND u.Role = 'Entreprise')
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
    <link rel="stylesheet" href='website.css'>
    <link rel="stylesheet" href= 'navigationBar.css'>
    <style>
        body {
            background-color: turquoise;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .validation-list {
            width: 80%;
            margin-top: 50px;
            background-color: white;
            justify-content: center;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        button {
            padding: 5px 10px;
            cursor: pointer;
            border: none;
            color: white;
            font-weight: bold;
        }

        .validate-btn {
            background-color: gray;
        }

        .validate-btn.active {
            background-color: green;
        }

        .reject-btn {
            background-color: gray;
        }

        .reject-btn.active {
            background-color: red;
        }

        .back-btn {
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

        .back-btn:hover {
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

<div class="validation-list">
    <h2>Utilisateurs en attente de validation</h2>
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

</body>
</html>
