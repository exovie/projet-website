<?php
session_start();    

//import des fonctions
include 'fonctionConnexion.php';
include '../Fonctions.php';


// Connexion à la base de données
$pdo = Connexion_base();

// Vérification de l'envoi du formulaire
if (isset($_POST['Part1C'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Vérification de l'existence de l'email dans la base de données
    $verifEmail = Verif_mail_connexion($pdo, $email);
    if (!$verifEmail) {
        $_SESSION['ErrorCode'] = 3; 
        Fermer_base($pdo);
        header('Location: /projet-website/Homepage.php');
        exit;
        }
    
    //Récupération du récupération des informations du compte 
    $idCompte = $verifEmail[0];
    $mdpCompte = $verifEmail[1];
    $roleCompte = $verifEmail[3];

    $_SESSION['machin'] = $verifEmail;
    $_SESSION['ma']= $password;

    //Vérification de la validation de l'utilisateur
    if ($roleCompte == 'Medecin' || $roleCompte == 'Entreprise') {
        $verification = Verif_validation_connexion($pdo, $idCompte, $roleCompte);
        if (!$verification) {
            $_SESSION['ErrorCode'] = 4;
            Fermer_base($pdo);
            header('Location: /projet-website/Connexion/Form1_connexion.php');
            exit;
        }}
    
    //Comparaison des mots de passe
    $checkMdp = Compare_mdp_connexion($pdo, trim($password), ($mdpCompte));
    if (!$checkMdp) {
        $_SESSION['ErrorCode'] = 5;
        Fermer_base($pdo);
        header('Location: /projet-website/Connexion/Form1_connexion.php#modal');
        exit;
    }

    //sauvegarde des informations de l'utilisateur dans la session
    $_SESSION['Logged_user'] = true;
    $_SESSION['Id_user'] = $idCompte;
    $_SESSION['role'] = $roleCompte;

    $informationUSERS= RecupereInformations($pdo, $idCompte, $roleCompte);
    $_SESSION['Nom'] = $informationUSERS[1];

    $_SESSION['SuccessCode']= 2;
    Fermer_base($pdo);
    header('Location: /projet-website/Homepage.php');
    exit;
}
else {
    Fermer_base($pdo);
    header('Location: /projet-website/Homepage.php');
    exit;
}