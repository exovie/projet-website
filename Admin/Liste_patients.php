<?php

include('Connexion_base.php');

$conn=Connexion_base();
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
</head>
<body>
    <h1>Liste des Patients</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Sexe</th>
                <th>Téléphone</th>
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
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button onclick="window.location.href='Home_Admin.php'">Retour</button>
</body>
</html>
