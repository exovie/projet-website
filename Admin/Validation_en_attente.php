<?php

include('Connexion_base.php');

$conn=Connexion_base();

$query = "
    SELECT u.Id_user, u.Role, 
           CASE 
               WHEN u.Role = 'Medecin' THEN m.Statut_inscription 
               WHEN u.Role = 'Entreprise' THEN e.Verif_inscription 
           END AS Verification
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
<html>
<head>
    <title>Validation en attente</title>
    <script>
        function updateStatus(userId, role, action) {
            fetch('update_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ userId: userId, role: role, action: action })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const btn = document.getElementById(`btn-${userId}`);
                    btn.innerText = action === 'validate' ? 'Validé' : 'Refusé';
                    btn.style.backgroundColor = action === 'validate' ? 'green' : 'red';
                } else {
                    alert('Erreur : ' + data.message);
                }
            });
        }
    </script>
</head>
<body>
    <h1>Validation en attente</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Rôle</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($validations as $validation): ?>
                <tr>
                    <td><?= htmlspecialchars($validation['Id_user']) ?></td>
                    <td><?= htmlspecialchars($validation['Role']) ?></td>
                    <td><?= $validation['Verification'] == 0 ? 'En attente' : 'Validé' ?></td>
                    <td>
                        <button id="btn-<?= $validation['Id_user'] ?>" 
                                onclick="updateStatus(<?= $validation['Id_user'] ?>, '<?= $validation['Role'] ?>', 'validate')">
                            Valider
                        </button>
                        <button onclick="updateStatus(<?= $validation['Id_user'] ?>, '<?= $validation['Role'] ?>', 'refuse')">
                            Refuser
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button onclick="window.location.href='Home_Admin.php'">Retour</button>
</body>
</html>
