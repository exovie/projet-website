<?php

$IndexNotif = [
    1 => "Vous avez une nouvelle demande d’inscription à consulter.",
    2=> "L’essai " . ($Id_essai) . " vient d’être publié. Il débutera lorsque le nombre de médecins requis sera atteint.",
    3=> "L’entreprise " . $Id_entreprise." souhaite vous solliciter pour gérer son nouvel essai ". $Essai.". Veuillez consulter cette annonce.",
    4=> "De nouveaux essais viennent d’être publiés. Ces derniers peuvent vous intéresser. ",
    5=> "Le médecin ". $Id_medecin ." vient de rejoindre l’équipe de l’essai ".$Essai, 
    6=> "Un médecin vient de se retirer de l’équipe de l’essai" . $Id_essai, 
    7=> "Le nombre de médecins requis pour l’essai ".$Id_ssai ."est atteint La phase de recrutement est sur le point de débuter.",
    8=> "L’essai ".$Id_essai." est en pause due au nombre insuffisant de médecin gérant. Veuillez consulter l’annonce de cet essai.",
    9=> "L’essai ".$Id_essai." auquel vous participez est suspendu pour des raisons administratives.",
    10=> "Vous avez une ou plusieurs demande de participation à l’essai". $Id_essai,
    11=> "Votre candidature pour l’essai". $Id_essai ."vient d’être acceptée",
    12=> "Un candidat vient de se retirer de l’essai". $Id_essai,
    13=> "Le nombre de patients requis pour l’essai". $Id_essai ."est atteint La phase de traitement est sur le point de débuter.",
    14=> "La phase de votre essai ".$Id_essai." vient de se terminer. Vous pouvez désormais consulter les résultats de cette phase sur la page correspondante.",
    15=> "Le nombre de candidats requis pour l’essai ".$Id_Essai." vient d’être atteint. La phase de recrutement est donc terminée. ",
    16=> "L'essai clinique ".$Id_essai." a été mis à jour. Veuillez consulter les nouvelles informations.",
    17=> "L’essai ".$Essai." a été suspendu. ",
    18=> "L’entreprise vous a retiré de son essai ".$Id_essai,
    19=> "Un médecin souhaiterait participer à l’essai ".$Essai.". Veuillez consulter sa demande.",
    20=> "Votre phase de traitement est terminée. Merci d’avoir participé",
    21=> "Vous avez été refusé de l’essai ".$Id_essai,
    22=> "Vous avez été accepté dans l’essai ".$Id_essai." en tant que médecin",
    23=> "Vous avez été refusé de l’essai $Id_essai en tant que médecin"
];

function Verif_notif($CodeNotif, $Id_essai, $Id_destinataire){
    //Function that verify is a notifications already exist 
    $pdo = Connexion_base();
    $stmt =  $pdo-> prepare("SELECT DESTINATAIRE.Statut_notification FROM DESTINATAIRE NATURAL JOIN NOTIFICATION WHERE 
    DESTINATAIRE.Id_destinataire = :id_des AND NOTIFICATION.CodeNotif = :code AND NOTIFICATION.Id_Essai = :id_E;");
    $stmt-> execute (['id_des'=> $Id_destinataire, 'code' => $CodeNotif, 'id_E'=> $Id_essai]);
    $reponse = $stmt->fetch();
    if($stmt->rowCount() < 1 || $reponse[0] == 'Ouvert'){
        Fermer_base($pdo);
        return true;
    }
    else{
        Fermer_base($pdo);
        return false;
    }
};

function Generer_notif($CodeNotif, $Id_essai, $Id_destinataire){
    $exist = Verif_notif($CodeNotif, $Id_essai, $Id_destinataire);
    if ($exist) {
        $pdo = Connexion_base();
        // Code to generate the notification if it doesn't exist
        $stmt = $pdo->prepare("INSERT INTO NOTIFICATION (CodeNotif, Id_Essai, Date_Notif) VALUES (:code, :id_essai, :date)");
        $stmt->execute(['code' => $CodeNotif, 'id_essai' => $Id_essai, 'date' => date('Y-m-d H:i:s')]);

        $id_Notif = $pdo->lastInsertId(); // Get the ID of the notification

        // Add the recipient to the Destinataire table
        $stmt = $pdo->prepare("INSERT INTO DESTINATAIRE (Id_notif, Id_destinataire, Statut_notification) VALUES (:id_notif, :id_dest, 'Non ouvert')");
        $stmt->execute(['id_notif'=> $id_Notif, 'id_dest' => $Id_destinataire]);

        Fermer_base($pdo);
        return $id_Notif;
    }else {
        // Notification already exists
        return false; };
};

function Pastille_nombre($id_D) {
    $pdo = Connexion_base();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM DESTINATAIRE WHERE Id_destinataire = :id_D AND Statut_notification = 'Non Ouvert'");
    $stmt->execute(['id_D' => $id_D]);
    $count = $stmt->fetchColumn();

    // Display the count of unread notifications
    Fermer_base($pdo);
    return $count;
};

function List_Notif($Id_D) {
    $pdo = Connexion_base();
    $stmt = $pdo->prepare('SELECT * FROM NOTIFICATION NATURAL JOIN DESTINATAIRE WHERE DESTINATAIRE.Id_destinataire = :id_D');
    $stmt->execute(['id_D'=> $Id_D]);
    if ($stmt->rowCount() > 0) {
        $reponse = $stmt->fetchAll();
        Fermer_base($pdo);
        return $reponse;
    } else {
        Fermer_base($pdo);
        return null;
    }
};

function Lire_notif($Id_notif, $Id_user) {
    $pdo = Connexion_base();
    if ($_SESSION['role'] == 'Medecin') {
        // Mark the notification as read for all doctors
        $stmt = $pdo->prepare("UPDATE DESTINATAIRE SET Statut_notification = 'Ouvert' WHERE Id_notif = :id_notif");
        $stmt->execute(['id_notif' => $Id_notif]);
    } else {
        // Mark the notification as read for the user
        $stmt = $pdo->prepare("UPDATE DESTINATAIRE SET Statut_notification = 'Ouvert' WHERE Id_notif = :id_notif AND Id_destinataire = :id_user");
        $stmt->execute(['id_notif' => $Id_notif, 'id_user' => $Id_user]);
    }
    //if role medecein => ouvert pour tous les medecins
    Fermer_base($pdo);
};

function Ne_plus_lire_no_notif($Id_notif, $Id_user) {
        $pdo = Connexion_base();
        if ($_SESSION['role'] == 'Medecin') {
            // Mark the notification as read for all doctors
            $stmt = $pdo->prepare("UPDATE DESTINATAIRE SET Statut_notification = 'Non Ouvert' WHERE Id_notif = :id_notif");
            $stmt->execute(['id_notif' => $Id_notif]);
        } else {
            // Mark the notification as read for the user
            $stmt = $pdo->prepare("UPDATE DESTINATAIRE SET Statut_notification = 'Non Ouvert' WHERE Id_notif = :id_notif AND Id_destinataire = :id_user");
            $stmt->execute(['id_notif' => $Id_notif, 'id_user' => $Id_user]);
        }
        //if role medecein => ouvert pour tous les medecins
        Fermer_base($pdo);
};
?>
