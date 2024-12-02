<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats des Tests Unitaires</title>
    <link rel="stylesheet" href="website.css">
    <style>
        table {
            width: 95%;
            border-collapse: collapse;
            margin-bottom: 20px;
            padding: 10px;
            margin-left: 30px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .failure {
            background-color: #f8d7da;
            color: #721c24;
        }
        body {
            overflow-y: scroll; /* Affiche toujours la barre de défilement verticale */
            overflow-x: auto;   /* Affiche la barre de défilement horizontale si nécessaire */
            padding : 10px;
        }
        h1 {
        color: rgb(24, 98, 104);
        }

    </style>
</head>

<?php 
// Fonction pour générer une ligne de résultat
function addTestResult($functionName, $expected, $actual, $condition, $com='') {
    $class = $condition ? 'success' : 'failure';
    echo "<tr>";
    echo "<td>{$functionName}</td>";
    echo "<td>{$expected}</td>";
    echo "<td class='{$class}'>{$actual}</td>";
    echo "<td>{$com}</td>";
    echo "</tr>";
}
?>


<body>
    <h1>Résultats des Tests Unitaires</h1>
    <h2>Connexion à la Base de Données</h2>
    <?php
    // Vérification de la connexion
    $host = 'localhost';
    $dbname = 'website_db';
    $user = 'root';
    $password = '';
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "<p class='success'>Connexion à la base de données réussie.</p>";
    } catch (PDOException $e) {
        echo "<p class='failure'>Erreur de connexion : " . $e->getMessage() . "</p>";
        die();
    }
    ?>


    <h2>Tests des Fonctions d'INSCRIPTION</h2>
    <table>
        <thead>
            <tr>
                <th>Fonction</th>
                <th>Résultat Attendu</th>
                <th>Résultat Obtenu</th>
                <th>Commentaire</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include 'Inscription/fonctionInscription.php';
            $userCreated = []; // Stocker les Id_user des utilisateurs ajoutés pour les supprimer après les tests

            // Test de Verif_mail
            $email_existant = 'angie@admin.com';
            $email_non_existant = 'angie@people.com';
            addTestResult(
                "Verif_mail (email existant)",
                "L'email existe",
                Verif_mail($pdo, $email_existant) ? "L'email n'existe pas" : "L'email existe",
                Verif_mail($pdo, $email_existant) == false
            );
            addTestResult(
                "Verif_mail (email non existant)",
                "L'email n'existe pas",
                Verif_mail($pdo, $email_non_existant) ? "L'email n'existe pas" : "L'email existe",
                Verif_mail($pdo, $email_non_existant) == true
            );

            // Test de Verif_age
            $age_mineur = '2015-01-01';
            $age_majeur = '1990-01-01';
            addTestResult(
                "Verif_age (mineur)",
                "Mineur",
                Verif_age($age_mineur) ? "Majeur" : "Mineur",
                Verif_age($age_mineur) == false
            );
            addTestResult(
                "Verif_age (majeur)",
                "Majeur",
                Verif_age($age_majeur) ? "Majeur" : "Mineur",
                Verif_age($age_majeur) == true
            );

            // Test de validateResponsesByRole Patient
            $role = 'patient';
            $responsesPatient = [
                'John', 'Doe', '2015-01-01', 'M', '12345', 'profile.jpg', 180, 75, 'Aucun', 'Aucune', '123456789123'
            ];
            $errors = validateResponsesByRole($role, $responsesPatient);
            $errorMessages = '';
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $errorMessages .= $error . "<br>";
                }
            }
            addTestResult(
                "validateResponsesByRole (patient)",
                "Réponses invalides",
                empty($errors) ? "Réponses valides" : "Réponses invalides",
                !empty($errors),
                $errorMessages
            );

            $responsesPatient = [
                'John', 'Doe', '2015-01-01', 'M', '1234567891', 'profile.jpg', 180, 75, 'Aucun', 'Aucune', '1234567891'
            ];
            $errors = validateResponsesByRole($role, $responsesPatient);
            $errorMessages = '';
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $errorMessages .= $error . "<br>";
                }
            }
            addTestResult(
                "validateResponsesByRole (patient)",
                "Réponses valides",
                empty($errors) ? "Réponses valides" : "Réponses invalides",
                empty($errors),
                $errorMessages
            );

            // Test de validateResponsesByRole  Medecin
            $role = 'medecin';
            $responsesMedecin = [
                'John', 'Doe', 'Chirurgien', '9784858707', '10003378191', 'profile.jpg'
            ];
            $errors = validateResponsesByRole($role, $responsesMedecin);
            $errorMessages = '';
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $errorMessages .= $error . "<br>";
                }
            }
            addTestResult(
                "validateResponsesByRole (medecin)",
                "Réponses valides",
                empty($errors) ? "Réponses valides" : "Réponses invalides",
                empty($errors),
                $errorMessages 
            );

            // Test de validateResponsesByRole Entreprise
            $role = 'entreprise';
            $responsesEntreprise = [
                'BigPharma', '1234567890', 'adresse.jpg', '12345678901234'
            ];
            $errors = validateResponsesByRole($role, $responsesEntreprise);
            $errorMessages = '';
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $errorMessages .= $error . "<br>";
                }
            }
            addTestResult(
                "validateResponsesByRole (entreprise)",
                "Réponses valides",
                empty($errors) ? "Réponses valides" : "Réponses invalides",
                empty($errors),
                $errorMessages
            );

            // Test de addUser Admin
            $mdp = "securepassword";
            $email = "user@example.com";
            $role = "admin";
            $userId = addUser($pdo, $mdp, $email, $role);
            addTestResult(
                "addUser (admin)",
                "Utilisateur ajouté avec succès",
                $userId ? "Utilisateur ajouté avec Id_user = $userId" : "Erreur d'ajout",
                $userId !== false
            );
            if ($userId) {
                $userCreated[] = $userId;
            }

            //Test de addRole Patient
            $role = "patient";
            $emailPatient = "test@patent.com";
            $mdpPatient = "Patent123";
            $id_patent = addUser($pdo, $mdpPatient, $emailPatient, $role);
            $errorsRole = addRole($pdo,  $role, $id_patent, $responsesPatient);
            addTestResult(
                "addRole (patient)",
                "Role ajouté avec succès",
                $errorsRole ? "Role ajouté avec Id_user = $id_patent" : "Erreur d'ajout",
                $errorsRole !== false
            );
            if ($id_patent) {
                $userCreated[] = $id_patent;
            }

            //Test de addRole Patient
            $role = "medecin";
            $emailMedecin = "test@medecin.com";
            $mdpMedecin = "Medecin123";
            $id_Medecin = addUser($pdo, $mdpPatient, $emailPatient, $role);
            $errorsRole = addRole($pdo,  $role, $id_Medecin, $responsesMedecin);
            addTestResult(
                "addRole (medecin)",
                "Role ajouté avec succès",
                $errorsRole ? "Role ajouté avec Id_user = $id_Medecin" : "Erreur d'ajout",
                $errorsRole !== false
            );
            if ($id_Medecin) {
                $userCreated[] = $id_Medecin;
            }

            //Test de addRole Entreprise
            $role = "entreprise";
            $emailEntreprise = "test@firm.com";
            $mdpEntreprise = "Firm123";
            $id_firm = addUser($pdo, $mdpEntreprise, $emailEntreprise, $role);
            $errorsRole = addRole($pdo,  $role, $id_firm, $responsesEntreprise);
            addTestResult(
                "addRole (entreprise)",
                "Role ajouté avec succès",
                $errorsRole ? "Role ajouté avec Id_user = $id_firm" : "Erreur d'ajout",
                $errorsRole !== false
            );
            if ($id_firm) {
                $userCreated[] = $id_firm;
            }

        // Supprimer l'utilisateur ajouté
        foreach ($userCreated as $Id) {
            $sql = $pdo->prepare("DELETE FROM `USERS` WHERE `USERS`.`Id_user` = :id_user;");
            $sql->execute(['id_user' => $Id]);
        }
            ?>
        </tbody>
    </table>
    <i>Les utilisateurs générés avec les Id_user = <?php foreach ($userCreated as $Id) {echo $Id, " , ";}?> ont été supprimés de la BdD.</i>

    <body>
    <h2>Tests des Fonctions de CONNEXION</h2>
    <table>
        <thead>
            <tr>
                <th>Fonction</th>
                <th>Résultat Attendu</th>
                <th>Résultat Obtenu</th>
                <th>Commentaire</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include 'Connexion/fonctionConnexion.php';

            //Verification si l'email est attribué à un compte
            $email_existant = 'angie@admin.com';
            $email_non_existant = 'angie@test.com';
            addTestResult(
                "Verif_mail (email existant)",
                "Un compte est relié à cet email",
                Verif_mail_connexion($pdo, $email_existant) ? "L'email existe" : "L'email n'existe pas",
                Verif_mail_connexion($pdo, $email_existant) !== false
            );
            addTestResult(
                "Verif_mail (email non existant)",
                "L'email n'existe pas",
                Verif_mail_connexion($pdo, $email_non_existant) ? "L'email existe" : "L'email n'existe pas",
                Verif_mail_connexion($pdo, $email_non_existant) == false
            );
            ?>

</body>
</html>
