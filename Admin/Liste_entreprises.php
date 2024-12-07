<?php

include('Connexion_base.php');

$conn=Connexion_base();

$query = "
    SELECT Id_entreprise, Nom_entreprise, Telephone, Siret
    FROM entreprises
";
$stmt = $conn->query($query);
$entreprises = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Liste des Entreprises</title>
</head>
<body>
    <h1>Liste des Entreprises</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Téléphone</th>
                <th>Siret</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($entreprises as $entreprise): ?>
                <tr>
                    <td><?= htmlspecialchars($entreprise['Id_entreprise']) ?></td>
                    <td><?= htmlspecialchars($entreprise['Nom_entreprise']) ?></td>
                    <td><?= htmlspecialchars($entreprise['Siret']) ?></td>
                    <td><?= htmlspecialchars($entreprise['Telephone']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button onclick="window.location.href='Home_Admin.php'">Retour</button>
</body>
</html>
