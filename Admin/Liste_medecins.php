<?php

include('Connexion_base.php');

$conn=Connexion_base();

$query = "
    SELECT Id_medecin, Nom, Prenom, Specialite, Matricule, Telephone
    FROM medecins
";
$stmt = $conn->query($query);
$medecins = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html>
<head>
    <title>Liste des Médecins</title>
</head>
<body>
    <h1>Liste des Médecins</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Spécialité</th>
                <th>Matricule</th>
                <th>Téléphone</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($medecins as $medecin): ?>
                <tr>
                    <td><?= htmlspecialchars($medecin['Id_medecin']) ?></td>
                    <td><?= htmlspecialchars($medecin['Nom']) ?></td>
                    <td><?= htmlspecialchars($medecin['Prenom']) ?></td>
                    <td><?= htmlspecialchars($medecin['Specialite']) ?></td>
                    <td><?= htmlspecialchars($medecin['Matricule']) ?></td>
                    <td><?= htmlspecialchars($medecin['Telephone']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button onclick="window.location.href='Home_Admin.php'">Retour</button>
</body>
</html>
