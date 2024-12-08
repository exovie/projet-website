<?php

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
        $stmt->closeCursor();
        $id_Notif = $pdo->lastInsertId(); // Get the ID of the notification
        
        if ($Id_essai == 'NULL'){
        $users = $pdo->prepare("SELECT Id_user  FROM `USERS` WHERE Role ='Admin';");
        $users->execute();
        $users->closeCursor();
        
        foreach ($users as $user) {
            $stmt = $pdo->prepare("INSERT INTO DESTINATAIRE (Id_notif, Id_destinataire, Statut_notification) VALUES (:id_notif, :id_dest, 'Non ouvert')");
            $stmt->execute(['id_notif'=> $id_Notif, 'id_dest' => $user['Id_user']]);
        }
        } else{
        // Add the recipient to the Destinataire table
        $stmt = $pdo->prepare("INSERT INTO DESTINATAIRE (Id_notif, Id_destinataire, Statut_notification) VALUES (:id_notif, :id_dest, 'Non ouvert')");
        $stmt->execute(['id_notif'=> $id_Notif, 'id_dest' => $Id_destinataire]);
        }
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
    $stmt = $pdo->prepare('SELECT N.*, D.*, ES.Titre, EN.Nom_entreprise FROM DESTINATAIRE D NATURAL JOIN NOTIFICATION N JOIN 
    ESSAIS_CLINIQUES ES ON N.Id_Essai = ES.Id_essai JOIN ENTREPRISES EN ON ES.Id_entreprise = EN.Id_entreprise WHERE D.Id_destinataire = :id_D
    ORDER BY N.Date_Notif DESC');
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
    if ($_SESSION['role'] == 'Medecin'|| $_SESSION['role'] == 'Admin') {
        // Mark the notification as read for all doctors
        $stmt = $pdo->prepare("UPDATE DESTINATAIRE SET Statut_notification = 'Ouvert' WHERE Id_notif = :id_notif");
        $stmt->execute(['id_notif' => $Id_notif]);
    } else {
        // Mark the notification as read for the user
        $stmt = $pdo->prepare("UPDATE DESTINATAIRE SET Statut_notification = 'Ouvert' WHERE Id_notif = :id_notif AND Id_destinataire = :id_user");
        $stmt->execute(['id_notif' => $Id_notif, 'id_user' => $Id_user]);
    }
    Fermer_base($pdo);
};

function Ne_plus_lire_notif($Id_notif, $Id_user) {
        $pdo = Connexion_base();
        if ($_SESSION['role'] == 'Medecin'|| $_SESSION['role'] == 'Admin') {
            // Mark the notification as read for all doctors
            $stmt = $pdo->prepare("UPDATE DESTINATAIRE SET Statut_notification = 'Non Ouvert' WHERE Id_notif = :id_notif");
            $stmt->execute(['id_notif' => $Id_notif]);
        } else {
            // Mark the notification as read for the user
            $stmt = $pdo->prepare("UPDATE DESTINATAIRE SET Statut_notification = 'Non Ouvert' WHERE Id_notif = :id_notif AND Id_destinataire = :id_user");
            $stmt->execute(['id_notif' => $Id_notif, 'id_user' => $Id_user]);
        }
        Fermer_base($pdo);
};

function Obtenir_statut_notification($id_notif, $id_destinataire) {
    $pdo = Connexion_base();

    $stmt = $pdo->prepare(
        'SELECT Statut_notification FROM DESTINATAIRE WHERE Id_notif = :id_notif AND Id_destinataire = :id_destinataire');
    $stmt->execute(['id_notif' => $id_notif,'id_destinataire' => $id_destinataire]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    Fermer_base($pdo);
    // Retourner le statut si trouvé, sinon indiquer que la notification n'existe pas
    return $result ? $result['Statut_notification'] : 'Notification introuvable';
};

function Affiche_notif($Id_D) {
    $All = List_Notif($Id_D);
    
    foreach ($All as $Notif) {
        //Définition des variables
        $CodeNotif = $Notif['CodeNotif'];
        $Date = $Notif['Date_Notif'];
        $Entreprise = $Notif['Nom_entreprise'];
        $titre_Essai = $Notif['Titre'];
        $Id_Essai = $Notif['Id_Essai']; 
        $Id_Notif = $Notif['Id_notif'];

        $IndexNotif = [
            1 => "Vous avez une nouvelle demande d’inscription à consulter.",
            2=> "L’essai " . $titre_Essai . " vient d’être publié. Il débutera lorsque le nombre de médecins requis sera atteint.",
            3=> "L’entreprise " . $Entreprise." souhaite vous solliciter pour gérer son nouvel essai ". $titre_Essai.". Veuillez consulter cette annonce.",
            4=> "De nouveaux essais viennent d’être publiés. Ces derniers peuvent vous intéresser. ",
            5=> "Un médecin vient de rejoindre l’équipe de l’essai ".$titre_Essai, 
            6=> "Un médecin vient de se retirer de l’équipe de l’essai" . $titre_Essai, 
            7=> "Le nombre de médecins requis pour l’essai ".$titre_Essai ."est atteint La phase de recrutement est sur le point de débuter.",
            8=> "L’essai ".$titre_Essai." est en pause due au nombre insuffisant de médecin gérant. Veuillez consulter l’annonce de cet essai.",
            9=> "L’essai ".$titre_Essai." auquel vous participez est suspendu pour des raisons administratives.",
            10=> "Vous avez une ou plusieurs demande de participation à l’essai". $titre_Essai,
            11=> "Votre candidature pour l’essai". $titre_Essai ."vient d’être acceptée",
            12=> "Un candidat vient de se retirer de l’essai". $titre_Essai,
            13=> "Le nombre de patients requis pour l’essai". $titre_Essai ."est atteint La phase de traitement est sur le point de débuter.",
            14=> "La phase de votre essai ".$titre_Essai." vient de se terminer. Vous pouvez désormais consulter les résultats de cette phase sur la page correspondante.",
            15=> "Le nombre de candidats requis pour l’essai ".$titre_Essai." vient d’être atteint. La phase de recrutement est donc terminée. ",
            16=> "L'essai clinique ".$titre_Essai." a été mis à jour. Veuillez consulter les nouvelles informations.",
            17=> "L’essai ".$titre_Essai." a été suspendu. ",
            18=> "L’entreprise vous a retiré de son essai ".$titre_Essai,
            19=> "Un médecin souhaiterait participer à l’essai ".$titre_Essai.". Veuillez consulter sa demande.",
            20=> "Votre phase de traitement est terminée. Merci d’avoir participé",
            21=> "Vous avez été refusé de l’essai ".$titre_Essai,
            22=> "Vous avez été accepté dans l’essai ".$titre_Essai." en tant que médecin",
            23=> "Vous avez été refusé de l’essai $titre_Essai en tant que médecin"
        ];

        // Détermine la classe du tableau en fonction du statut
        $tableClass = ($Notif['Statut_notification'] == 'Non Ouvert') ? 'opened-notifications' : 'non-notifications';
        
        echo "<table class='$tableClass'>";

        echo "<tr>";
        echo "<td class='notif-column'>";

        
        // Statut de la notification
        if ($Notif['Statut_notification'] == 'Non Ouvert') {
            echo "
            <form method='post' action='votre_script.php' style='display: inline;'>
                <input type='hidden' name='action' value='Lire_notif'>
                <input type='hidden' name='id_notification' value='" . htmlspecialchars($Notif['Id_notif']) . "'>
                <button type='submit' style='border: none; background: none; padding: 0;'>
                    <img src='../Pictures/open_eyes.png' alt='Marquer comme lu' title='Marquer comme lu' style='width: 24px; height: 24px;'>
                </button>
            </form>";
        } else {
            echo "
            <form method='post' action='votre_script.php' style='display: inline;'>
                <input type='hidden' name='action' value='Ne_plus_lire_notif'>
                <input type='hidden' name='id_notification' value='" . htmlspecialchars($Notif['Id_notif']) . "'>
                <button type='submit' style='border: none; background: none; padding: 0;'>
                    <img src='../Pictures/eyes_close.png' alt='Marquer comme non lu' title='Marquer comme non lu' style='width: 24px; height: 24px;'>
                </button>
            </form>";
        }

        

        echo "</td>";

        // Contenu de la notification
        echo "
        <td>
            <form method='post' action='Redirect_notif.php' style='margin: 0;'>
                <input type='hidden' name='id_essai' value='" . htmlspecialchars($Id_Essai) . "'>
                <input type='hidden' name='code_notif' value='" . htmlspecialchars($CodeNotif) . "'>
                <input type='hidden' name='id_notif' value='" . htmlspecialchars($Id_Notif) . "'>
                <button type='submit' style='background: none; border: none; color: inherit; font: inherit; cursor: pointer; text-align: left;'>
                    " . htmlspecialchars($IndexNotif[$CodeNotif]) . "
                </button>
            </form>
        </td>";
        
        // Date de la notification
        echo "<td class='date-column'>". htmlspecialchars($Date) ."</td>";
        echo "</tr>";

        echo "</table>";
    }
}


?>
