<?php
session_start();

include("Fonction_Mes_infos.php");
//$id_user =$_SESSION['id_user'];
$id_user= 14 ;

// Récupération de l'historique des essais
$historique = getHistoriqueEssais($conn, $id_user);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique des Essais Cliniques</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        a {
            text-decoration: none;
            color: #007BFF;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Historique des Essais Cliniques</h1>

    <?php if (count($historique) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>ID Essai</th>
                    <th>Date de Création</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historique as $essai): ?>
                    <tr>
                        <td><?= htmlspecialchars($essai['Id_essai']) ?></td>
                        <td><?= htmlspecialchars($essai['Date_creation']) ?></td>
                        <td><?= htmlspecialchars($essai['Statut']) ?></td>
                        <td>
                            <a href="Page_Essai.php?id_essai=<?= urlencode($essai['Id_essai']) ?>">Voir l'essai</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun essai clinique trouvé pour cet utilisateur.</p>
    <?php endif; ?>
</body>
</html>
