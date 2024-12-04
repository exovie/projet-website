<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Essai Clinique</title>
</head>
<body>
    <h1>Créer un Essai Clinique</h1>
    <form method="POST" action="Creer_essai.php">

    <p>
        <label for="Titre">Titre :</label>
        <input type="text" id="Titre" name="Titre" required><br>

        <label for="Contexte">Contexte :</label>
        <textarea id="Contexte" name="Contexte" required></textarea><br>

        <label for="Objectif_essai">Objectif de l'Essai :</label>
        <textarea id="Objectif_essai" name="Objectif_essai" required></textarea><br>

        <label for="Design_etude">Design de l'Étude :</label>
        <textarea id="Design_etude" name="Design_etude" required></textarea><br>

        <label for="Critere_evaluation">Critères d'Évaluation :</label>
        <textarea id="Critere_evaluation" name="Critere_evaluation" required></textarea><br>

        <label for="Resultats_attendus">Résultats Attendus :</label>
        <textarea id="Resultats_attendus" name="Resultats_attendus" required></textarea><br>

        <label for="Nb_medecins">Nombre de Médecins :</label>
        <input type="number" id="Nb_medecins" name="Nb_medecins" required><br>

        <label for="Nb_patients">Nombre de Patients :</label>
        <input type="number" id="Nb_patients" name="Nb_patients" required><br>
    </p>
    <p>    
        <input type="submit" value="Créer l'Essai" />
    </p>
    </form>
</body>
</html>
