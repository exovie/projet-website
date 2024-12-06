<?php 

function Verif_mail_connexion($pdo, $email) {
    "Function that checks if the email exists in the database
    and returns the user's information if it does.";

    $stmt = $pdo->prepare("SELECT * FROM USERS WHERE Email = :mail");
    $stmt->execute(['mail' => $email]);
    
    if ($stmt->rowCount() > 0) {
        //Retourne les informations de l'utilisateurs 
        return $stmt->fetch();
    } else {
        // L'email n'existe pas 
        return false;
    }
}

function Verif_validation_connexion($pdo, $Id_users, $role){
    //Fonction qui vérifie si l'utilisateur est validé à partir de son rôle et son Id qui proviennent de la fonction précédente
    if ($role == 'Medecin') {
        $stmt = $pdo->prepare("SELECT `Statut_inscription` FROM `MEDECINS` WHERE `Id_medecin`=:Id_users");
        $stmt->execute(['Id_users' => $Id_users]);
        $validation = $stmt->fetch();
    
    } elseif($role == 'Entreprise') {
        $stmt = $pdo->prepare("SELECT `Verif_inscription` FROM `ENTREPRISES` WHERE `Id_entreprise` = :Id_users");
        $stmt->execute(['Id_users' => $Id_users]);
        $validation = $stmt->fetch();
    }
    else {
        // L'utilisateur n'est pas un medecin ou une entreprise
    return "L'utilisateur n'est pas un medecin ou une entreprise";
    exit;
    }

    if ($validation[0] == 1) {
        // L'utilisateur est validé
        return true;
    } else {
        // L'utilisateur n'est pas validé
        return false;
    }
}

function Compare_mdp_connexion($pdo, $mdp, $mdp_Bdd){

    // Comparer le mot de passe en clair avec le haché
    if (password_verify($mdp, $mdp_Bdd)) {
        // Le mot de passe est correct
        return true;
    } else {
        // Le mot de passe est incorrect
        return false;
    }
}

function RecupereInformations($pdo, $id, $role){
if ($role == "Patient") {
    $stmt = $pdo->prepare("SELECT * FROM `PATIENTS` WHERE `Id_patient` = :id_user");
    $stmt->execute(["id_user"=> $id]);
    $validation = $stmt->fetch();
}
elseif ($role == "Medecin") {
    $stmt = $pdo->prepare("SELECT * FROM `MEDECINS` WHERE `Id_medecin` = :id_user");
    $stmt->execute(["id_user"=> $id]);
    $validation = $stmt->fetch();
}
else {
    $stmt = $pdo->prepare("SELECT * FROM `ENTREPRISES` WHERE `Id_entreprise` = :id_user");
    $stmt->execute(["id_user"=> $id]);
    $validation = $stmt->fetch();
}
if ($validation != []) {
    return $validation;
}else {
    $_SESSION["ErrorCode"] = 2;
    header('Location: /projet-website/Connexion/Form1_connexion.php');
    exit;
}}
?>