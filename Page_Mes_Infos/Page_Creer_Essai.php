<?php
session_start();

// Vérifiez si l'utilisateur est autorisé
// if ($_SESSION['role'] !== "Entreprise") {
//     // Redirigez l'utilisateur s'il n'est pas "Entreprise"
//     header("Location: unauthorized.php");
//     exit;
// }

// Logique de la page "Créer un essai" ici
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Essai Clinique</title>
    <style>
        body {
            background-color: cyan; /* Arrière-plan cyan */
            display: flex; /* Utiliser flexbox pour centrer */
            justify-content: center; /* Centre horizontalement */
            align-items: center; /* Centre verticalement */
            min-height: 100vh; /* Hauteur minimale de l'écran */
            margin: 0; /* Supprimer les marges par défaut */
            font-family: Arial, sans-serif; /* Police lisible */
        }
        form {
            background-color: white; /* Fond blanc pour le formulaire */
            padding: 20px; /* Espacement interne */
            border-radius: 10px; /* Coins arrondis */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Légère ombre */
            max-width: 500px; /* Largeur maximale */
            width: 100%; /* Largeur relative */
        }
        h1 {
            text-align: center; /* Centrer le titre */
            margin-bottom: 20px; /* Espacement en bas */
        }
        label {
            display: block; /* Forcer les labels sur leur propre ligne */
            margin-bottom: 8px; /* Espacement sous le label */
            font-weight: bold; /* Mettre en gras */
        }
        input, textarea {
            width: 100%; /* S'étendre à la largeur du parent */
            padding: 8px; /* Espacement interne */
            margin-bottom: 15px; /* Espacement entre les champs */
            border: 1px solid #ccc; /* Bordure grise */
            border-radius: 5px; /* Coins arrondis */
            box-sizing: border-box; /* Inclure les marges/paddings */
        }
        textarea {
            resize: vertical; /* Autoriser le redimensionnement vertical uniquement */
            height: 100px; /* Hauteur par défaut */
        }
        input[type="submit"] {
            background-color: #007BFF; /* Couleur bleue pour le bouton */
            color: white; /* Texte blanc */
            border: none; /* Supprimer la bordure */
            padding: 10px 15px; /* Espacement interne */
            cursor: pointer; /* Curseur de pointeur */
            border-radius: 5px; /* Coins arrondis */
            font-size: 16px; /* Taille du texte */
        }
        input[type="submit"]:hover {
            background-color: #0056b3; /* Couleur plus foncée au survol */
        }
    </style>
</head>
<body>
    <form method="POST" action="Creer_essai.php">
        <h1>Créer un Essai Clinique</h1>
        <label for="Titre">Titre :</label>
        <input type="text" id="Titre" name="Titre" required>

        <label for="Contexte">Contexte :</label>
        <textarea id="Contexte" name="Contexte" required></textarea>

        <label for="Objectif_essai">Objectif de l'Essai :</label>
        <textarea id="Objectif_essai" name="Objectif_essai" required></textarea>

        <label for="Design_etude">Design de l'Étude :</label>
        <textarea id="Design_etude" name="Design_etude" required></textarea>

        <label for="Critere_evaluation">Critères d'Évaluation :</label>
        <textarea id="Critere_evaluation" name="Critere_evaluation" required></textarea>

        <label for="Resultats_attendus">Résultats Attendus :</label>
        <textarea id="Resultats_attendus" name="Resultats_attendus" required></textarea>

        <label for="Nb_medecins">Nombre de Médecins :</label>
        <input type="number" id="Nb_medecins" name="Nb_medecins" required>

        <label for="Nb_patients">Nombre de Patients :</label>
        <input type="number" id="Nb_patients" name="Nb_patients" required>

        <input type="submit" value="Créer l'Essai">
    </form>
</body>
</html>
