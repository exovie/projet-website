<?php
session_start();

include('Connexion_base.php');

$conn=Connexion_base();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $id = intval($_POST['id']);
    $modifications = $_POST;

    // Retirez les champs inutiles
    unset($modifications['role'], $modifications['id']);

    // Enregistrez les modifications dans la session pour révision
    $_SESSION['modifications'] = [
        'role' => $role,
        'id' => $id,
        'data' => $modifications
    ];

    header('Location: Confirmer_modif.php');
    exit;
}
?>
