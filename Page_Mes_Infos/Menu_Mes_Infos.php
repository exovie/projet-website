<?php
// Exemple : Récupérer le rôle depuis une session ou une base de données
$role='Entreprise';
//$role = $_SESSION['role']; // Par exemple : "Patient", "Medecin", ou "Entreprise"

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu basé sur le rôle</title>
    <style>
        /* CSS pour centrer les boutons */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .button-container {
            display: flex;
            gap: 20px; /* Espacement entre les boutons */
        }

        .button-container a {
            text-decoration: none;
            color: white;
            background-color: #007BFF;
            padding: 15px 25px;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .button-container a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="button-container">
        <!-- Boutons communs à tous les rôles -->
        <a href="Mes_infos.php">Mes Infos</a>
        <a href="Historique_Essais.php">Mes Essais</a>

        <!-- Bouton spécifique pour "Entreprise" -->
        <?php if ($role === "Entreprise"): ?>
            <a href="Page_Creer_Essai.php">Créer un Essai</a>
        <?php endif; ?>
    </div>
</body>
</html>
