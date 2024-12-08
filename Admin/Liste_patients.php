<?php

//Connection à la base
include("../Fonctions.php");
$conn=Connexion_base();

//Récupération des infos
$query = "
    SELECT Id_Patient, Nom, Prenom, Sexe, Telephone
    FROM PATIENTS
    JOIN USERS  ON PATIENTS.Id_Patient = USERS.Id_user
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
    <link rel="stylesheet" href='admin.css'>
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
    <h2>Liste des Patients</h2>
    
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
                            <button-liste onclick="window.location.href='Modifier_Patients.php?id=<?= $patient['Id_Patient'] ?>'">Modifier</button-liste>
                        </td>
                        <td>
                            <form action="Supprimer_utilisateur.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($patient['Id_Patient']) ?>">
                                <input type="hidden" name="role" value="Patient">
                                <button-liste type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">Supprimer</button-liste>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <button-liste class="back-button" onclick="window.location.href='Home_Admin.php'">Retour</button-liste>
</div>

</body>
</html>
