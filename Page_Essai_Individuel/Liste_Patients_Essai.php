<?php
/*Fonction qui permet de charger les informations des patients
qui participent à un essai*/

session_start();
include("Connexion_base.php");
//$id_essai = isset($_GET['id_essai']);
$id_essai = isset($_GET['id_essai']) ? (int)$_GET['id_essai'] : 8;
$conn= Connexion_base();

function Liste_Patients_Essais($conn, int $id_essai){
    
    try {
  
        $query= $conn -> prepare("
        SELECT Nom, Prenom, Date_naissance, Sexe, Telephone, Taille, Traitements, Allergies
        FROM patients 
        JOIN patients_essais ON patients_essais.Id_patient= patients.Id_patient
        WHERE patients_essais.Id_essai = :id_essai
        ");
        $query->bindParam(':id_essai', $id_essai, PDO::PARAM_INT);
        $query->execute();

        // Récupération des résultats
        $patients = $query->fetchAll(PDO::FETCH_ASSOC);
        return $patients;
    } 
    catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        die();
    }
    $conn = null;
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informations des Patients - Essai <?= htmlspecialchars($id_essai) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: #ffffff;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
    </style>
</head>

<?php
$patients = Liste_Patients_Essais($conn, $id_essai);
?>

<body>
    <h1>Liste des patients pour l'essai ID : <?= htmlspecialchars($id_essai) ?></h1>

    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Date de Naissance</th>
                <th>Sexe</th>
                <th>Téléphone</th>
                <th>Taille (cm)</th>
                <th>Traitements</th>
                <th>Allergies</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($patients)): ?>
                <?php foreach ($patients as $patient): ?>
                    <tr>
                        <td><?= htmlspecialchars($patient['Nom']) ?></td>
                        <td><?= htmlspecialchars($patient['Prenom']) ?></td>
                        <td><?= htmlspecialchars($patient['Date_naissance']) ?></td>
                        <td><?= htmlspecialchars($patient['Sexe']) ?></td>
                        <td><?= htmlspecialchars($patient['Telephone']) ?></td>
                        <td><?= htmlspecialchars($patient['Taille']) ?></td>
                        <td><?= htmlspecialchars($patient['Traitements']) ?></td>
                        <td><?= htmlspecialchars($patient['Allergies']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" style="text-align: center;">Aucun patient trouvé pour cet essai.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>







    
















