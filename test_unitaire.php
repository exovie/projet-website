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

        // Supprimer les notiications ajoutées pour les tests
        foreach ($notifCreated as $Id) {
            $sql = $pdo->prepare("DELETE FROM `NOTIFICATION` WHERE `Id_notif` = :id_N;");
            $sql->execute(['id_N' => $Id]);
        }
        ?>
        </tbody>
    </table>
    <i>Les notifications générées avec les Id_notif = <?php foreach ($notifCreated as $Id) {echo $Id, " , ";}?> ont été supprimés de la BdD.</i>
    
    <body>
    <h2>Tests des Fonctions ???</h2>
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
            include_once 'Fonctions.php';

            //test de la fonction Get_id()
            $table = 'PATIENTS';
            $column = 'Id_patient';
            $list_id = Get_id($table, $column);
            addTestResult(
                'Get_id()',
                'liste des ID des patients',
                !empty($list_id)? 'Liste des ID des patients': 'Erreur de Récupération',
                !empty($list_id) == true 
            );

            $table = 'PATIENTS';
            $column = 'Id_patients';
            $list_id = Get_id($table, $column);
            addTestResult(
                'Get_id(avec une colonne incorrecte)',
                'Liste vide',
                empty($list_id)? 'Liste vide': 'Erreur de Récupération',
                empty($list_id) == true,
                $com = "Erreur: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'Id_patients' in 'field list'" 
            );

            $table = 'PATIENT';
            $column = 'Id_patient';
            $list_id = Get_id($table, $column);
            addTestResult(
                'Get_id(avec une table incorrecte)',
                'Liste vide',
                empty($list_id)? 'Liste vide': 'Erreur de Récupération',
                empty($list_id) == true,
                $com = "Erreur: SQLSTATE[42S02]: Base table or view not found: 1146 Table 'website_db.patient' doesn't exist" 
            );

            //test de la fonction Get_entreprise_data
            $id_entreprise = 5;
            $data = Get_entreprise_data($id_entreprise);
            $entreprise = $data['entreprise'];
            $clinical_trials = $data['clinical_trials'];
            $medecins = $data['medecins'];
            addTestResult(
                'Get_entreprise_data()',
                'Array contenant l`ensemble des données de l`entreprise',
                !empty($entreprise) && is_array($entreprise)? 'Array contenant l`ensemble des données de l`entreprise': 'erreur',
                !empty($entreprise) && is_array($entreprise) == true
            );

            $id_entreprise = 4;
            $data = Get_entreprise_data($id_entreprise);
            $entreprise = $data['entreprise'];
            $clinical_trials = $data['clinical_trials'];
            $medecins = $data['medecins'];
            addTestResult(
                'Get_entreprise_data(quand l`identifiant est incorrect)',
                'Arrays vides',
                empty($entreprise) && is_array($entreprise)? 'Arrays vides': 'erreur',
                empty($entreprise) && is_array($entreprise) == true
            );

            //test de la fonction Get_essais
            $role = 'patient';
            $essais = Get_essais($role);
            addTestResult(
                'Get_essais($role = "patient")',
                'array contenants les informations des essais à afficher',
                !empty($data) && is_array($data)? 'array contenants les informations des essais à afficher': 'erreur',
                !empty($data) && is_array($data) == true
            );

            $role = 'medecin';
            $essais = Get_essais($role);
            addTestResult(
                'Get_essais($role = "medecin")',
                'array contenants les informations des essais à afficher',
                !empty($data) && is_array($data)? 'array contenants les informations des essais à afficher': 'erreur',
                !empty($data) && is_array($data) == true
            );

            $role = 'entreprise';
            $essais = Get_essais($role);
            addTestResult(
                'Get_essais($role = "entreprise")',
                'array contenants les informations des essais à afficher',
                !empty($data) && is_array($data)? 'array contenants les informations des essais à afficher': 'erreur',
                !empty($data) && is_array($data) == true
            );

            $role = 'visiteur';
            $essais = Get_essais($role);
            addTestResult(
                'Get_essais($role = "visiteur")',
                'array contenants les informations des essais à afficher',
                !empty($data) && is_array($data)? 'array contenants les informations des essais à afficher': 'erreur',
                !empty($data) && is_array($data) == true
            );

            $role = 'admin';
            $essais = Get_essais($role);
            addTestResult(
                'Get_essais($role = "admin")',
                'array contenants les informations des essais à afficher',
                !empty($data) && is_array($data)? 'array contenants les informations des essais à afficher': 'erreur',
                !empty($data) && is_array($data) == true
            );

            $role = 'fake';
            $essais = Get_essais($role);
            addTestResult(
                'Get_essais($role = "fake")',
                'array vide',
                empty($essais) && is_array($essais)? 'array vide': 'erreur',
                empty($essais) && is_array($essais) == true
            );

            //test de la fonction List_medecin
            $id_medecin = 16;
            $data = List_Medecin($id_medecin);
            addTestResult(
                'List_Medecin(quand l`identifiant est correct)',
                'Array contenant toutes les informations d`un medecin',
                !empty($data) && is_array($data)? 'Array contenant toutes les informations d`un medecin': 'erreur',
                !empty($data) && is_array($data) == true
            );

            $id_medecin = 4;
            $data = List_Medecin($id_medecin);
            addTestResult(
                'List_Medecin(quand l`identifiant est incorrect)',
                'Array vide',
                empty($data) && is_array($data)? 'Array vide': 'erreur',
                empty($data) && is_array($data) == true
            );

            $id_medecin = 'string';
            $data = List_Medecin($id_medecin);
            addTestResult(
                'List_Medecin(quand l`identifiant est une chaine de caractère)',
                'Array vide',
                empty($data) && is_array($data)? 'Array vide': 'erreur',
                empty($data) && is_array($data) == true
            );

            //test de la fonction recherche_EC
            $list_ec = Get_essais('patient');
            $recherche = '';
            $filtres = ['Tous', 'Tous'];
            $data = recherche_EC($list_ec, $recherche, $filtres);
            addTestResult(
                'recherche_EC(quand la recherche est vide et sans filtres)',
                'array identique à la liste complète',
                $data === $list_ec? 'array identique à la liste complète': 'erreur',
                ($data === $list_ec) == true
            );

            $list_ec = Get_essais('patient');
            $recherche = '';
            $filtres = ['Tous', 'Tous'];
            $data = recherche_EC($list_ec, $recherche, $filtres);
            addTestResult(
                'recherche_EC(quand la recherche a une correspondance)',
                'array comprenant la liste des essais à afficher',
                !empty($data) && is_array($data)? 'array comprenant la liste des essais à afficher': 'erreur',
                !empty($data) && is_array($data) == true
            );

            $list_ec = Get_essais('patient');
            $recherche = 'sdvsdgszdg';
            $filtres = ['Tous', 'Tous'];
            $data = recherche_EC($list_ec, $recherche, $filtres);
            addTestResult(
                'recherche_EC(quand la recherche n`a pas de correspondance)',
                'array vide',
                empty($data) && is_array($data)? 'array vide': 'erreur',
                empty($data) && is_array($data) == true,
                $com = "Aucun essai ne correspond à votre recherche."
            );

            $list_ec = Get_essais('patient');
            $recherche = 'DELETE TABLE PATIENTS';
            $filtres = ['Tous', 'Tous'];
            $data = recherche_EC($list_ec, $recherche, $filtres);
            addTestResult(
                'recherche_EC(quand la recherche tente une injection sql)',
                'array vide',
                empty($data) && is_array($data)? 'array vide': 'erreur',
                empty($data) && is_array($data) == true,
                $com = "Aucun essai ne correspond à votre recherche."
            );

            $list_ec = Get_essais('patient');
            $recherche = '//commentaire';
            $filtres = ['Tous', 'Tous'];
            $data = recherche_EC($list_ec, $recherche, $filtres);
            addTestResult(
                'recherche_EC(quand la recherche est un commentaire php)',
                'le contenu n`est pas pris en compte et les essais ne sont pas filtrés',
                $data === $list_ec? 'array identique à la liste complète': 'erreur',
                ($data === $list_ec) == true
            );

            ?>
</body>

</html>
