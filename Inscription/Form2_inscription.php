<?php
session_start();

/* Verification de si le premier formulaire a deja ete rempli*/
if (!isset($_SESSION['email'], $_SESSION['role'])) {
    header("Location: /projet-website/Inscription/Form1_inscription.php");
    exit();
}

$role = $_SESSION['role'];
if (isset($_SESSION['FormsErr'])) {
    $FormsErr= $_SESSION['FormsErr'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Modale2</title>
    <link rel="stylesheet" href="/projet-website/website.css">
</head>
<body>
<div class="content">
        <h1>Inscrivez-vous chez Clinicou</h1>
        <img src="/projet-website/Pictures/logo.png" alt="logo" id="grologo">
    </div>
    <div id="modal" class="modal">
        <div class="modal-content">
            <a href="/projet-website/Homepage.php" class="close-btn">&times;</a>
            <h1>Inscription - <?php echo $role?></h1>
            <form method="POST" action="/projet-website/Inscription/verification2_inscription.php">

            <!-- Affichage des erreurs -->
            <?php if (isset($FormsErr)): ?>
                        <p class="error-message"><?php echo htmlspecialchars($FormsErr); ?></p>
                        <?php unset($FormsErr);
                        unset($_SESSION['FormsErr']); ?>
            <?php endif; ?>

            <!-- Affichage du formulaire -->
            <?php 
            // Questions spécifiques
            $questions = [
                'Patient' => ['Nom','Prenom','Date de naissance','Sexe','Telephone','Profil Picture','Taille', 'Poids', 'Traitements', 'Allergies', 'CNI'],
                'Medecin' => ['Nom','Prenom','Spécialité','Telephone','Matricule', 'Profil Picture'],
                'Entreprise' => ['Nom Entreprise', 'Telephone','Profil Picture', 'SIRET']
            ];

            foreach ($questions[$role] as $question): ?>
            <div class="form-group">
                <?php if ($question == 'Sexe'): ?>
                    <label><?php echo htmlspecialchars($question); ?></label>
                    <select name="reponses[]" required>
                        <option value="M">Homme</option>
                        <option value="F">Femme</option>
                    </select>
                <?php elseif ($question == 'Date de naissance'): ?>
                    <label><?php echo htmlspecialchars($question); ?></label>
                    <input type="text" name="reponses[]" placeholder="AAAA-MM-JJ" required>
                <?php elseif ($question == 'Profil Picture'): ?>
                    <label><?php echo htmlspecialchars($question); ?></label>
                    <input type="file" name="reponses[]" accept="image/*">
                <?php elseif ($question == 'CNI'): ?>
                    <label><?php echo htmlspecialchars($question); ?></label>
                    <input type="file" name="reponses[]" accept="image/*">
                <?php elseif ($question == 'Taille'): ?>
                    <label><?php echo htmlspecialchars($question); ?></label>
                    <input type="int" name="reponses[]" placeholder=" en cm " required>
                <?php elseif ($question == 'Poids'): ?>
                    <label><?php echo htmlspecialchars($question); ?></label>
                    <input type="int" name="reponses[]" placeholder=" en kg" required>
                <?php elseif ($question == 'Telephone'): ?>
                    <label><?php echo htmlspecialchars($question); ?></label>
                    <input type="int" name="reponses[]" placeholder=" XX XX XX XX XX " required>
                <?php elseif ($question == 'Traitements'): ?>
                    <label><?php echo htmlspecialchars($question); ?></label>
                    <input type="text" name="reponses[]">
                <?php elseif ($question == 'Allergies'): ?> 
                    <label><?php echo htmlspecialchars($question); ?></label>
                    <input type="text" name="reponses[]">
                <?php else: ?>
                    <label><?php echo htmlspecialchars($question); ?></label>
                    <input type="text" name="reponses[]" required>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>

            <?php
            if ($role == 'medecin' or $role == 'entreprise') {
                echo "Votre inscription sera soumise à validation par un administrateur.\n Vous pourrez vous connecter une fois votre compte validé.";
                } 
                ?>
                <br><br>
                <button type="submit" name="part2" >Finaliser l'inscription</button>
            </form>
        </div>
    </div>
</body>
</html>
