<?php

include("Connexion_base.php");
$conn = Connexion_base();


if (!isset($_GET['id_essai'])) {
    echo "Aucun essai sélectionné.";
    exit;
}
$id_essai = $_GET['id_essai'];

try {
    // Requête pour récupérer les détails de l'essai clinique
    $query = "SELECT * FROM essais_cliniques WHERE Id_essai = :id_essai";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id_essai', $id_essai, PDO::PARAM_INT);
    $stmt->execute();

    $essai = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$essai) {
        echo "Essai non trouvé.";
        exit;
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails de l'Essai Clinique</title>
</head>
<body>
    <h1>Détails de l'Essai Clinique</h1>
    <p><strong>ID Essai :</strong> <?= htmlspecialchars($essai['Id_essai']) ?></p>
    <p><strong>Date de Création :</strong> <?= htmlspecialchars($essai['Date_creation']) ?></p>
    <p><strong>Statut :</strong> <?= htmlspecialchars($essai['Statut']) ?></p>
</body>
</html>
