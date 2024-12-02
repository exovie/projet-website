<?php 

function Verif_mail_connexion($pdo, $email) {
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
    }

    if ($validation == 1) {
        // L'utilisateur est validé
        return true;
    } else {
        // L'utilisateur n'est pas validé
        return false;
    }
}

function Compare_mdp_connexion($pdo, $id_users, $mdp){
    $stmt = $pdo->prepare("SELECT `Passwd` FROM `USERS` WHERE `Id_user` = :id_users");
    $stmt->execute([""=> $id_users]);
    $mdp_Bdd = $stmt->fetch();

    // Comparer le mot de passe en clair avec le haché
    if (password_verify($$mdp, $mdp_Bdd)) {
        echo "Le mot de passe est correct.";
    } else {
        echo "Le mot de passe est incorrect.";
    }
}
?>