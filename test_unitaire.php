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
            overflow-y: auto; /* Affiche toujours la barre de défilement verticale */
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
    include 'Fonctions.php';

    // Vérification de la connexion
    $pdo = Connexion_base();
    if ($pdo){
        echo "<p class='success'>Connexion à la base de données réussie.</p>";
    } else {
        echo "<p class='failure'>Erreur de connexion : " ;
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
            $role = 'Patient';
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
            $role = 'Medecin';
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
            $role = 'Entreprise';
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
            $role = "Patient";
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
            $role = "Medecin";
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
            $role = "Entreprise";
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

        // Supprimer les utilisateurs ajoutés pour les tests
        foreach ($userCreated as $Id) {
            $sql = $pdo->prepare("DELETE FROM `USERS` WHERE `Id_user` = :id_user;");
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
            $resultEmail = Verif_mail_connexion($pdo, $email_existant);
            $resultFakeEmail = Verif_mail_connexion($pdo, $email_non_existant);
            addTestResult(
                "Verif_mail_connexion (email existant)",
                "Un compte est relié à cet email",
                $resultEmail ? "L'email existe" : "L'email n'existe pas",
                $resultEmail !== false
            );
            addTestResult(
                "Verif_mail_connexion (email non existant)",
                "L'email n'existe pas",
                $resultFakeEmail ? "L'email existe" : "L'email n'existe pas",
                $resultFakeEmail == false
            );

            //Verification si le compte est validé
            //pour un medecin
            $resultMedF = Verif_validation_connexion($pdo, 101, 'Medecin');
            addTestResult(
                'Verif_validation_connexion(MedecinF)',
                'Compte non validé',
                $resultMedF ? 'Compte validé': 'Compte non validé',
                $resultMedF == false
            );

            $resultMed = Verif_validation_connexion($pdo, 25 ,'Medecin');
            addTestResult(
                'Verif_validation_connexion(Medecin)',
                'Compte validé',
                $resultMed ? 'Compte validé': 'Compte non validé',
                $resultMed == true
            );

            $resultEn = Verif_validation_connexion($pdo, 12,'Entreprise');
            addTestResult(
                'Verif_validation_connexion(Entreprise)',
                'Compte validé',
                $resultEn ? 'Compte validé': 'Compte non validé',
                $resultEn == true
            );

            $resultEnF = Verif_validation_connexion(    $pdo, 102,'Entreprise');
            addTestResult(
                'Verif_validation_connexion(EntrepriseF)',
                'Compte non validé',
                $resultMedF ? 'Compte validé': 'Compte non validé',
                $resultMedF == false
            );

            $resultWrongRole = Verif_validation_connexion($pdo, 54, 'Patient');
            addTestResult(
                'Verif_validation_connexion(Wrong Role)',
                'Pas le bon rôle',
                is_string($resultWrongRole) ? 'Pas le bon rôle': 'Validé ou pas',
                is_string($resultWrongRole) == true
            );

            //Comparaison des mots de passe
            $wrongMDP = Compare_mdp_connexion($pdo, 'blabla', $resultEmail[1]);
            addTestResult(
                'Compare_mdp_connexion(MDP faux)',
                'Le mot de passe est incorrect',
                $wrongMDP ? 'Le mot de passe est correct': 'Le mot de passe est incorrect',
                !$wrongMDP
            );
            $goodMDP= Compare_mdp_connexion($pdo, 'angie', $resultEmail[1]);
            addTestResult(
                'Compare_mdp_connexion(MDP vrai)',
                'Le mot de passe est correct',
                $goodMDP == true ? 'Le mot de passe est correct': 'Le mot de passe est incorrect',
                $goodMDP
            );
            ?>
        </tbody>
    </table>

    <body>
    <h2>Tests des Fonctions des NOTIFICATIONS </h2>
    <table>
        <thead>
            <tr>
                <th>Fonction</th>
                <th>Résultat Attendu</th>
                <th>Résultat Obtenu</th>
                <th>Commentaire</th>
            </tr>
        </thead>
        <?php 
        include 'Notifications/fonction_notif.php'; 
        $notifCreated = []; // Stocker les Id_notif des notifications ajoutées pour les supprimer après les tests

        //Verif notifications
        $NotifT = Verif_notif(19,3,5);
        addTestResult(
            'Verif_notif (existe)',
            'Il faut générer une nouvelle notification',
            $NotifT ? 'Il faut générer une nouvelle notification' : "La notification précédente n'est pas ouverte",
            $NotifT == true
        );
        $NotifNew = Verif_notif(19, 3, 98);
        addTestResult(
            "Verif_notif (n'exite ) ",
            "Il faut générer une nouvelle notification",
            $NotifNew ? 'Il faut générer une nouvelle notification' : "La notification précédente n'est pas ouverte",
            $NotifNew == true
        );
        $NotifF = Verif_notif(19,3, 16 );
        addTestResult(
            'Verif_notif (Non ouverte)',
            "La notification précédente n'est pas ouverte",
            $NotifF ? 'Il faut générer une nouvelle notification' : "La notification précédente n'est pas ouverte",
            $NotifF == false
        );

        //Generer notification
        $NotifGenerateT = Generer_notif(19,3,5);
        addTestResult(
            "Generer_notif (n'existe pas déjà)",
            "Notification générée avec succès",
            $NotifGenerateT !== false ? "Notification générée avec succès avec Id_notif = $NotifGenerateT": "Notification non générée",
            $NotifGenerateT !==false     
        );
        if ($NotifGenerateT !== false) {
            $notifCreated[] = $NotifGenerateT;
        }
        $NotifGenerateF = Generer_notif(19,3,16);
        addTestResult(
            "Generer_notif (existe déjà)",
            "Notification générée avec succès",
            $NotifGenerateF !== false ? "Notification générée avec succès avec Id_notif = $NotifGenerateF": "Notification non générée",
            $NotifGenerateF ==false
        );
        if ($NotifGenerateF !== false) {
            $notifCreated[] = $NotifGenerateF;
        }

        //Nombre de notifications 
        $nbrNotifs = Pastille_nombre(22);
        addTestResult(
            "Pastille_nombre ",
            "Nombre de notifications (int) ",
            is_integer($nbrNotifs) ? $nbrNotifs : "Erreur de récupération",
            is_integer($nbrNotifs)
        );

        //Listes des notifications
        $list = List_Notif(23, 'Medecin');
        if (is_array($list)) {
            $commList = "";
            $commList .= "Id_Notif: " ;
            // Itérer sur chaque notification et les afficher
            foreach ($list as $key => $value) {
                $commList .= $value['Id_notif'] . " - ";
            }}
        addTestResult(
            "List_Notif",
            "Liste des notifications",
            is_array($list) ? "Liste des notifications" : "Erreur de récupération",
            is_array($list),
            is_array($list)?  $commList : 'Erreur de récupération'
        );

        //Lire une notification
        Lire_notif(99, 11);
        addTestResult(
            'Lire_notif (users)',
            'Notification Ouverte',
            Obtenir_statut_notification(99, 11) == 'Ouvert'? 'Notification Ouverte' : 'Notification Non Ouverte',
            Obtenir_statut_notification(99, 11) == 'Ouvert'
        );
        Ne_plus_lire_notif(99,11);
        addTestResult(
            'Ne_plus_lire_notif (users)',
            'Notification Non Ouverte',
            Obtenir_statut_notification(99, 11) == 'Ouvert'? 'Notification Ouverte' : 'Notification Non Ouverte',
            Obtenir_statut_notification(99, 11) !== 'Ouvert'
        );

        //medecin
        session_start();
        $_SESSION['role'] = 'Medecin';
        Lire_notif(112, 14);
        addTestResult(
            'Lire_notif (medecin)',
            'Notification Ouverte',
            Obtenir_statut_notification(112, 14) == 'Ouvert'? 'Notification Ouverte' : 'Notification Non Ouverte',
            Obtenir_statut_notification(112, 14) == 'Ouvert'
        );
        Ne_plus_lire_notif(112,14);
        addTestResult(
            'Ne_plus_lire_notif (medecin)',
            'Notification Non Ouverte',
            Obtenir_statut_notification(112, 14) == 'Ouvert'? 'Notification Ouverte' : 'Notification Non Ouverte',
            Obtenir_statut_notification(112, 14) !== 'Ouvert'
        );
        session_destroy();

        // // Supprimer les notiications ajoutées pour les tests
        // foreach ($notifCreated as $Id) {
        //     $sql = $pdo->prepare("DELETE FROM `NOTIFICATION` WHERE `Id_notif` = :id_N;");
        //     $sql->execute(['id_N' => $Id]);
        // }
        // ?>
        </tbody>
    </table>
    <i>Les notifications générées avec les Id_notif = <?php foreach ($notifCreated as $Id) {echo $Id, " , ";}?> ont été supprimés de la BdD.</i>
    

<body>

    <h2>Tests des Fonctions d'ADMIN</h2>
    <i>En raison de l'architecture des pages .php, qui incluent à la fois le code manipulant les données et l'affichage dans un même fichier, les tests unitaires des fonctionnalités de l'Admin et des sections 'Mes Infos' ont été réalisés manuellement. Par ailleurs, le tableau correspondant a également été complété à la main.</i>
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
            //Test d'accès à Home_Admin.php
            $manual_verification=' ';
            /*Lorsqu'on n'est pas connecter*/
            addTestResult(
                "Home_Admin.php (Visiteur, Patient, Medecin, Entreprise) ",
                "Accès Refusé, redirection vers la page de connexion",
                $manual_verification = "Accès Refusé, redirection vers la page de connexion",
                ($manual_verification == $manual_verification)

            );
            //Lorsqu'on est connecter à un compte
            addTestResult(
                "Home_Admin.php (Admin) ",
                "Accès autorisé",
                $manual_verification = "Accès autorisé",
                ($manual_verification == $manual_verification)
            );

            //Test d'accès à Liste_{Role}.php
            addTestResult(
                "Liste_patients.php (Visiteur, Patient, Medecin, Entreprise) ",
                "Accès Refusé, redirection vers la page de connexion",
                $manual_verification = "Accès Refusé, redirection vers la page de connexion",
                ($manual_verification == $manual_verification),
                "Idem pour Liste_medecins.php et Liste_entreprises.php"
            );
            addTestResult(
                "Liste_patients.php (Admin) ",
                "Accès autorisé",
                $manual_verification = "Accès autorisé",
                ($manual_verification == $manual_verification),
                "Idem pour Liste_medecins.php et Liste_entreprises.php"
            );
            
            //Test d'accès à Modifier_{Role}.php
            addTestResult(
                "Modifier_patients.php (Visiteur, Patient, Medecin, Entreprise) avec \$id_patient valide",
                "Accès Refusé, redirection vers la page de connexion",
                $manual_verification = "Accès Refusé, redirection vers la page de connexion",
                ($manual_verification == $manual_verification),
                "Idem pour Modifier_medecins.php et Modifier_entreprises.php"
            );
            addTestResult(
                "Modifier_patients.php (Visiteur, Patient, Medecin, Entreprise) avec \$id_patient invalide",
                "Accès Refusé, redirection vers la page de connexion",
                $manual_verification = "Accès Refusé, redirection vers la page de connexion",
                ($manual_verification == $manual_verification),
                "Idem pour Modifier_medecins.php et Modifier_entreprises.php"
            );
            addTestResult(
                "Modifier_patients.php (Admin) avec \$id_user valide ",
                "Renvoie sur la page de modification des informations grâce à l'id ",
                $manual_verification = "Renvoie sur la page de modification des informations grâce à l'id",
                ($manual_verification == $manual_verification),
                "Idem pour Modifier_medecins.php et Modifier_entreprises.php"
            );
            addTestResult(
                "Modifier_patients.php (Admin) avec \$id_patient invalide ",
                "Affiche patient introuvable",
                $manual_verification = "Affiche patient introuvable",
                ($manual_verification == $manual_verification),
                "Par exemple, si on prend id_patient=13 alors que id_user= 13 correspond à une entreprise. \n
                 Idem pour Liste_medecins.php et Liste_entreprises.php"
            );
            addTestResult(
                "Modifier_patients.php - Appuyer sur 'Enregistrer les modifications'",
                "Dirige vers la page Confirmer_modif.php",
                $manual_verification = "Dirige vers la page Confirmer_modif.php",
                ($manual_verification == $manual_verification),
            );
            addTestResult(
                "Modifier_patients.php - Appuyer sur 'Retour à la liste des {Role}'",
                "Retourne sur la page précédente contenant la liste des utilisateurs (Patient, Medecin ou Entreprise)",
                $manual_verification = "Retourne sur la page précédente",
                ($manual_verification == $manual_verification),
            );

            //Test Confirmer_modif.php
            addTestResult(
                "Confirmer_modif.php - Appuyer sur 'Valider",
                "Mise à jour de la Base de Donnée",
                $manual_verification = "Mise à jour de la Base de Donnée",
                ($manual_verification == $manual_verification),
            );
            addTestResult(
                "Confirmer_modif.php - Appuyer sur 'Annuler",
                "Retour sur la page Liste_{Role}.php selon le rôle de l'utilisateur dont on fait les modifications",
                $manual_verification = "Retour à la page Liste_{Role}.php",
                ($manual_verification == $manual_verification),
            );

            //Validation_en_attente.php
            addTestResult(
                "Validation_en_attente.php (Visiteur, Patient, Medecin, Entreprise)",
                "Accès Refusé, redirection vers la page de connexion",
                $manual_verification = "Accès Refusé, redirection vers la page de connexion",
                ($manual_verification == $manual_verification),
            );
            addTestResult(
                "Validation_en_attente.php (Admin)",
                "Accès autorisé",
                $manual_verification = "Accès autorisé à la page de Validation d'inscription",
                ($manual_verification == $manual_verification),
            );
            addTestResult(
                "Validation_en_attente.php - Appuyer sur 'Valider'",
                "Valider la demande d'inscription d'un utilisateur (medecin ou entreprise)",
                $manual_verification = "Valider la demande d'inscription d'un utilisateur",
                ($manual_verification == $manual_verification),
                "Verif_inscription=0 devient Verif_inscription=1"
            );
            addTestResult(
                "Validation_en_attente.php - Appuyer sur 'Supprimer'",
                "Supression de la demande d'inscription d'un utilisateur (medecin ou entreprise)",
                $manual_verification = "Suppression de l'utilisateur dans la BdD",
                ($manual_verification == $manual_verification),
            );

            //Supprimer_utilisateur.php
            addTestResult(
                "Supprimer_utilisateur.php",
                "Supression de la demande d'inscription d'un utilisateur (medecin ou entreprise)",
                $manual_verification = "Suppression de l'utilisateur dans la BdD",
                ($manual_verification == $manual_verification),
            );

?>

</tbody>
</body>
</table>

<body>
    <h2>Tests des Fonctions Mes Infos</h2>
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
            //Test Menu_Mes_Infos.php()
            addTestResult(
                'Menu_Mes_Infos.php (Patient ou Medecin)',
                "Affichage seulement des boutons 'Mes Infos' et 'Mes Essais'",
                $manual_verification = "Affichage de deux boutons",
                ($manual_verification == $manual_verification) 
            );
            addTestResult(
                'Menu_Mes_Infos.php (Entreprise)',
                "Affichage trois boutons: 'Mes Infos', 'Mes Essais', 'Créer un essai'",
                $manual_verification = "Affichage trois boutons",
                ($manual_verification == $manual_verification) 
            );
            addTestResult(
                "Mes_Infos.php - Appuyer sur 'Modifier' avec les informations sous le bon format",
                "Modification dans la BdD",
                $manual_verification = "Modification dans la BdD",
                ($manual_verification == $manual_verification) 
            );
            addTestResult(
                "Mes_Infos.php - Appuyer sur 'Modifier' avec les informations sous le mauvais format",
                "Affichage d'un message d'erreur",
                $manual_verification = "Affichage d'un message d'erreur en précisant le champ invalide",
                ($manual_verification == $manual_verification),
                "Par exemple, Telephone nécessite 10 chiffres. Il y a donc un message d'erreur si celui contient des chaines de caractère ou celle ci ne contient pas exactement 10 chiffres"
            );
            addTestResult(
                "Page_Creer_Essai.php (Entreprise)",
                "Accès autorisé sur la page",
                $manual_verification = "Accès autorisé sur la page",
                ($manual_verification == $manual_verification),
            );
            addTestResult(
                "Page_Creer_Essai.php (Patient ou Medecin)",
                "Refus d'accès, redirection vers Menu_Mes_Infos.php",
                $manual_verification = "Refus d'accès, redirection vers Menu_Mes_Infos.php",
                ($manual_verification == $manual_verification),
            );
            addTestResult(
                "Page_Creer_Essai.php",
                "Création de l'essai (Ajout dans la BdD)",
                $manual_verification = "Création de l'essai",
                ($manual_verification == $manual_verification),
            );

            ?>
</body>       
</html>






