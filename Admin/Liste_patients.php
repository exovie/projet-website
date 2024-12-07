<?php

//Connection à la base
include('Connexion_base.php');
$conn=Connexion_base();

//Récupération des infos
$query = "
    SELECT p.Id_Patient, p.Nom, p.Prenom, p.Sexe, p.Telephone
    FROM patients p
    JOIN users u ON p.Id_Patient = u.Id_user
";
$stmt = $conn->query($query);
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html>
<head>
    <title>Liste des Patients</title>
    <link rel="stylesheet" href='website.css'>
    <link rel="stylesheet" href= 'navigationBar.css'>
    <style>
        /* Styles pour la page */
        body {
            background-color: turquoise; /* Fond de la page en turquoise */
            font-family: Arial, sans-serif; /* Police pour le texte */
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: flex-start; /* Aligne tout au début de la page */
            align-items: center;
            height: 100vh;
            text-align: center; /* Centre tous les textes */
        }

        /* Centrage du titre et positionnement plus bas */
        h1 {
            margin-top: 120px;
            padding: 20px; /* Espace entre le texte et la bordure */
            border: 5px solid white; /* Bordure blanche autour du titre */
            border-radius: 10px; /* Coins arrondis de la bordure */
            background-color: rgba(0, 0, 0, 0.5); /* Fond légèrement transparent derrière le titre pour un meilleur contraste */
            color: white; /* Couleur du texte en blanc */
        }

        /* Centrage de la liste (table) */
        .content {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            margin-top: 100px; /* Décale la liste un peu plus bas */
            width: 80%; /* La table prendra 80% de la largeur de la page */
            margin-bottom: 40px; /* Espace entre la table et le bouton retour */
        }

        /* Styles pour la table */
        table {
            border-collapse: collapse;
            width: 100%; /* Table occupe toute la largeur de son conteneur */
        }

        th, td {
            border: 1px solid black;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #4CAF9A;
            color: white;
        }

        td {
            background-color: #f2f2f2;
        }

        button {
            padding: 10px 15px;
            background-color: #4CAF9A;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        button:hover {
            background-color: #45a049;
        }

        /* Styles pour le bouton "Retour" */
        .back-button {
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #f44336;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .back-button:hover {
            background-color: #e53935;
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
    <h1>Liste des Patients</h1>
    
    <div class="content">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Sexe</th>
                    <th>Téléphone</th>
                    <th>Modifier</th>
                    <th>Supprimer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($patients as $patient): ?>
                    <tr>
                        <td><?= htmlspecialchars($patient['Id_Patient']) ?></td>
                        <td><?= htmlspecialchars($patient['Nom']) ?></td>
                        <td><?= htmlspecialchars($patient['Prenom']) ?></td>
                        <td><?= htmlspecialchars($patient['Sexe']) ?></td>
                        <td><?= htmlspecialchars($patient['Telephone']) ?></td>
                        <td>
                            <button onclick="window.location.href='Modifier_Patients.php?id=<?= $patient['Id_Patient'] ?>'">Modifier</button>
                        </td>
                        <td>
                            <form action="Supprimer_utilisateur.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($patient['Id_Patient']) ?>">
                                <input type="hidden" name="role" value="Patient">
                                <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <button class="back-button" onclick="window.location.href='Home_Admin.php'">Retour</button>
</div>

</body>
</html>
