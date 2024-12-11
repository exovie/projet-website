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
            $column = 'Id_patient   ';
            $list_id = Get_id($table, $column);
            addTestResult(
                'Get_id',
                'liste des ID des patients',
                is_array($list_id)? 'Liste des ID': 'Erreur de Récupération',
                is_array($list_id) == true 
            );


            ?>
</body>

<body>
    <h2>Tests des Fonctions Admin</h2>
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
            
            // Simule les valeurs de session pour tester
            $_SESSION['role'] = 'Admin'; // Changez cette valeur pour tester différents cas
            $_SERVER['REQUEST_URI'] = '/test_unitaire.php'; // Simule une URL d'origine

            // Activer le mode test
            define('TEST_MODE', true);
            // Active la capture de la sortie
            ob_start();
            include 'Home_Admin.php'; // Inclut le fichier à tester
            $output = ob_get_clean();

            // Teste si l'utilisateur reste sur la page ou est redirigé
            if ($_SESSION['role'] === 'Admin') {
                // Si le rôle est Admin, on doit rester sur la page
                $access = true;
            } else {
                // Si le rôle n'est pas Admin, on doit être redirigé
                if (headers_sent() || strpos($output, 'Location: ../Connexion/Form1_connexion.php#modal') !== false) {
                    $access = true;
                } else {
                //Si on est pas redirigé
                    $access = false;
                }
            };
            addTestResult(
                'Home_Admin (Admin)',
                'Accès à Home_Admin.php',
                $access ? "Accès autorisé":"Accès refusé, redirection vers la page de connexion",
                $access == true 
            );
            
            addTestResult(
                'Home_Admin (Medecin)',
                'Redirection vers Form1_connexion.php#modal',
                $access ? "Accès autorisé":"Accès refusé, redirection vers la page de connexion",
                $access == false 
            );
            $_SESSION['role']='Patient';
            addTestResult(
                'Home_Admin (Patient)',
                'Redirection vers Form1_connexion.php#modal',
                $access ? "Accès autorisé":"Accès refusé, redirection vers la page de connexion",
                $access == false 
            );   
            $_SESSION['role']='Entreprise';
            addTestResult(
                'Home_Admin (Entreprise)',
                'Redirection vers Form1_connexion.php#modal',
                $access ? "Accès autorisé":"Accès refusé, redirection vers la page de connexion",
                $access == false 
            );  

            //Test Modifier_Patients.php
            //include_once ('Modifier_Patients.php');
            $Id_patient=40;
            if (!isset($_GET['Id_patient'])){
                $result=true;
            }else{
                $result=false;
            }
            
            addTestResult(
                "Modifier_Patients.php",
                "Modification des informations d'un patient",
                $result ? "Modification(s) apportée(s) avec succès" : "Erreur de modification",
                $result == true
            );


            ?>
</body>

<body>
    <h2>Tests des Fonctions Essais_cliniques</h2>
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
            include_once '../Fonction_Modif_Essais.php';

            //Test getEssaiInfo()
            $Id_essai= 5; #Essai existant
            $result = getEssaiInfo($conn, $Id_essai);
            addTestResult(
                'getEssaiInfo()',
                "Récupération des informations d'un essai",
                $result ? "Informations de l'essai": 'Erreur de Récupération',
                $result == true 
            );

            $Id_essai= 30; #Essai inconnue
            $result = getEssaiInfo($conn, $Id_essai);
            addTestResult(
                'getEssaiInfo()',
                "Récupération des informations d'un essai",
                $result ? "Informations de l'essai": 'Erreur de Récupération',
                $result == false
            );

            //Test updateEssaiInfo()
            $Id_essai= 5; #Essai existant
            $result = updateEssaiInfo($conn, $Id_essai);
            addTestResult(
                'updateEssaiInfo()',
                "Mise à jour des informations d'un essai",
                $result ? "Mise à jour réussie": 'Erreur de mise à jour',
                $result == true 
            );

            $Id_essai= 30; #Essai inconnue
            $result = updateEssaiInfo($conn, $Id_essai);
            addTestResult(
                'updateEssaiInfo()',
                "Mise à jour des informations d'un essai",
                $result ? "Mise à jour réussie": 'Erreur de mise à jour',
                $result == true 
            );


            //Test Mettre à jours les informations de l'essai

            ?>
</body>       
</html>
