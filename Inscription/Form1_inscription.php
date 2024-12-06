<?php
session_start();
include("../Fonctions.php");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Modale1</title>
    <link rel="stylesheet" href="/projet-website/website.css">
</head>
<body>
<div class="content">
        <h1>Inscrivez-vous chez Clinicou</h1>
        <img src="/projet-website/Pictures/logo.png" alt="logo" id="grologo">
    </div>

    <!-- Modal -->
    <div id="modal" class="modal">
        <div class="modal-content">
            <!-- Lien de fermeture qui redirige vers Homepage.php -->
            <a href="/projet-website/Homepage.php" class="close-btn">&times;</a>
            <h1>Inscription</h1>
            <form method="POST" action="/projet-website/Inscription/verification1_inscription.php" id="form-part-1">

            <!-- Affichage des erreurs  -->
            <?php 
            if (isset($_SESSION['ErrorCode'])): 
                ErrorEditor($_SESSION['ErrorCode'], 'true');
                unset($_SESSION['ErrorCode']); // Nettoyage après affichage
            endif; 
            ?>

            <!-- Affichage du formulaire -->
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="role">Rôle :</label>
                <select id="role" name="role" required>
                    <option value="patient">Patient</option>
                    <option value="medecin">Médecin</option>
                    <option value="entreprise">Entreprise</option>
                </select>
            </div>
                <button type="submit" name="part1">Continuer</button>
            </form>
        </div>
    </div>
</body>
</html>
