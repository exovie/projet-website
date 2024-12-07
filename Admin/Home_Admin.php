<?php
// Vérification du rôle de l'utilisateur
session_start();
$_SESSION['Role'] = 'Admin';
if ($_SESSION['Role'] !== 'Admin') {
    header('Location: login.php'); // Redirection si non Admin
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Accueil Admin</title>
</head>
<body>
    <h1>Bienvenue, Admin</h1>
    <button onclick="window.location.href='Liste_patients.php'">Liste Patients</button>
    <button onclick="window.location.href='liste_medecins.php'">Liste Médecins</button>
    <button onclick="window.location.href='liste_entreprises.php'">Liste Entreprises</button>
    <button onclick="window.location.href='validation_en_attente.php'">Validation en attente</button>
</body>
</html>
