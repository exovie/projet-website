<?php

include_once 'Notifications/fonction_notif.php';

function Connexion_base(){
    $host = 'localhost';
    $dbname = 'website_db';
    $user = 'root';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    } 
    return $pdo;
    }


function Fermer_base($pdo){
    // Fermeture de la connexion
    $pdo = null; // Cela libère la connexion
}

//Fonctions  liées aux essais

//attention aux dates, elles sont initialisées à 000/00/00 à la création, il faut donc les update et non les insert
//verif_nombremedecin, manque le cas où le nombre re passe en dessous de 2, changer le 2 d'ailleurs

function Get_Entreprise($Id_essai) {
    try{
    $conn = Connexion_base();
    $stmt = $conn->prepare("SELECT Id_entreprise FROM ESSAIS_CLINIQUES WHERE Id_essai = ?");
    $stmt->execute(array($Id_essai));
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['Id_entreprise'];}
    catch (PDOException $e) {
        echo "Erreur bdd: " . $e->getMessage(); 
    }}
    
function Get_Statut_Patient($Id_essai, $Id_patient) {
    try {
        $conn = Connexion_base();
        $stmt = $conn->prepare("SELECT Statut_participation FROM PATIENTS_ESSAIS WHERE Id_essai = ? AND Id_patient = ?");
        $stmt->execute(array($Id_essai, $Id_patient));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérification si un résultat est trouvé
        if ($result) {
            return $result['Statut_participation'];
        } else {
            // Si aucun résultat trouvé, renvoyer une valeur par défaut ou null
            return null;
        }
    } catch (PDOException $e) {
        // Gérer l'erreur proprement
        error_log("Erreur BDD dans Get_Statut_Patient: " . $e->getMessage());
        return null; // Renvoyer null en cas d'erreur
    }
}
 
function Get_Statut_Medecin($Id_essai, $Id_medecin) {
    try {
        $conn = Connexion_base();
        $stmt = $conn->prepare("SELECT Statut_medecin FROM MEDECIN_ESSAIS WHERE Id_essai = ? AND Id_medecin = ?");
        $stmt->execute(array($Id_essai, $Id_medecin));
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérification si un résultat est trouvé
        if ($result) {
            return $result['Statut_medecin'];
        } else {
            // Si aucun résultat trouvé, renvoyer une valeur par défaut ou null
            return null;
        }
    } catch (PDOException $e) {
        // Gérer l'erreur proprement
        error_log("Erreur BDD dans Get_Statut_Medecin: " . $e->getMessage());
        return null; // Renvoyer null en cas d'erreur
    }
}



function Ajout_Bdd_Essai(int $Id_entreprise, string $Titre, string $Contexte, string $Objectif_essai, string $Design_etude, string $Criteres_evaluation, string $Resultats_attendus, int $Id_essai_precedent, int $Nb_medecins, int $Nb_patients) {
    try{
        $conn = Connexion_base();
        $DateduJour = date('Y-m-d');
        $requete = $conn -> prepare("INSERT INTO ESSAIS_CLINIQUES(`Titre`, `Contexte`, `Objectif_essai`, `Design_etude`, `Criteres_evaluation`, 
        `Resultats_attendus`, `Date_lancement`, `Date_fin`, `Date_creation`, `Id_essai_precedent`, `Statut`, `Id_entreprise`, `Nb_medecins`, `Nb_patients`) VALUES (?,?,?,?,?,?,'0000/00/00','0000/00/00', ?,?, 'En attente',?, ?,?)");// on ne connait pas encore date lancement et fin
        $requete -> execute(array($Titre, $Contexte, $Objectif_essai, $Design_etude, $Criteres_evaluation,$Resultats_attendus,$DateduJour, $Id_essai_precedent, $Id_entreprise, $Nb_medecins, $Nb_patients));
        echo "Essai ajouté avec succès";
        Fermer_base($conn);

    }
    catch (PDOException $e) {
        $_SESSION['ErrorCode'] = 10;
        header("Location: " . $_SESSION['origin']);
        exit;
    }
    try{
        $conn = Connexion_base();
        //récupération de l'id_essai qui vient d'être publié
        $requete_essai = $conn -> prepare("SELECT MAX(`Id_essai`) AS 'Id_essai_publié' FROM `ESSAIS_CLINIQUES`");
        $requete_essai -> execute();
        $tableau_essai = $requete_essai -> fetch();
        $Id_essai = $tableau_essai["Id_essai_publié"];
        //envoi des notifications aux médecins
        $requete_medecin = $conn -> prepare("SELECT `Id_medecin` FROM `MEDECINS`");
        $requete_medecin -> execute();
        $tableau_medecin = $requete_medecin -> fetchAll();
        $liste_medecin = $tableau_medecin["Id_medecin"];
        foreach ($tableau_medecin as $medecin) {
            $Id_medecin = $medecin['Id_medecin'];
            Generer_notif(4, $Id_essai, $Id_medecin);  
        }
        //envoi à l'entreprise
        Generer_notif(2, $Id_essai, $Id_entreprise);
        Fermer_base($conn);}
        catch (PDOException $e) {
            echo "Erreur bdd: " . $e->getMessage();
    }}


function Modif_Description_Essai(int $Id_essai,string $Titre, string $Contexte, string $Objectif_essai, string $Design_etude, string $Criteres_evaluation,string $Resultats_attendus, int $Nb_medecins, int $Nb_patients ){

    try{
        $conn = Connexion_base();
        $requete_statut = $conn -> prepare("SELECT `Statut` FROM `ESSAIS_CLINIQUES` WHERE `Id_essai`=?");
        $requete_statut -> execute(array($Id_essai));
        $tableau_statut = $requete_statut -> fetch();
        $statut = $tableau_statut['Statut'];
        //si l'essai n'est pas encore publié pour les patients, modification des données
        if($statut == 'En attente'){
            $requete_update = $conn->prepare("UPDATE `ESSAIS_CLINIQUES` SET `Titre` = ?, `Contexte` = ?, `Objectif_essai` = ?, `Design_etude` = ?, `Criteres_evaluation` = ?, 
                `Resultats_attendus` = ?, `Nb_medecins` = ?, `Nb_patients` = ?  WHERE `Id_essai` = ?");
            $requete_update -> execute(array($Titre, $Contexte, $Objectif_essai, $Design_etude, $Criteres_evaluation, $Resultats_attendus, $Nb_medecins, $Nb_patients));
        
        //Envoi des notifs aux médecins de l'essai
        $requete_medecin = $conn -> prepare("SELECT `Id_medecin` FROM `MEDECIN_ESSAIS` WHERE `Id_essai`=?");
        $requete_medecin->execute(array($Id_essai));
        $tableau = $requete_medecin->fetchAll();
        foreach ($tableau as $medecin) {
            $Id_medecin = $medecin['Id_medecin'];
            Generer_notif(16, $Id_essai, $Id_medecin); // Fonction pour générer une notification
        }}
        Fermer_base($conn);
        echo "Notifs envoyées avec succès";
    }
    catch (PDOException $e) {
        $_SESSION['ErrorCode'] = 11;
        header("Location: " . $_SESSION['origin']);
        exit;
}

}

//Méthode médecin
function Modif_Infos_Patient(int $Id_patient, int $Id_essai, int $Poids, string $Traitements, string $Allergies){

    try{
        $conn = Connexion_base();
        //Modification du poids, des traitements et des allergies
        $requete_update = $conn -> prepare("UPDATE `PATIENTS` SET `Poids` = ?, `Traitements` = ?, `Allergies`= ?  WHERE `Id_patient`=?");
        $requete_update -> execute(array($Poids, $Traitements, $Allergies, $Id_patient));
        Fermer_base($conn);
        echo "Info patients modifiées avec succès";
    }
    catch (PDOException $e) {
        $_SESSION['ErrorCode'] = 9;
        header("Location: " . $_SESSION['origin']);
        exit;
}
}



//méthode entreprise
//vérifiée
function Demander_Medecin_essai(int $Id_essai, int $Id_medecin){
    try {
    $conn = Connexion_base();
    $requete = $conn -> prepare("INSERT INTO MEDECIN_ESSAIS(`Id_medecin`, `Id_essai`, `Statut_medecin`) VALUES (?,?,'Sollicite')");
    $requete -> execute(array($Id_medecin, $Id_essai));
    echo "Nouveau médecin sollicité avec succès pour cet essai!";
    Fermer_base($conn);
    Generer_notif(3,$Id_essai, $Id_medecin);
    }
    catch (PDOException $e) {
        $_SESSION['ErrorCode'] = 12;
        header("Location: " . $_SESSION['origin']);
        exit; }

}

//vérifiée
//méthode médecin: médecin qui demande à participer à un essai
function Postuler_Medecin_Essai(int $Id_essai, int $Id_medecin){
    try{
        $conn = Connexion_base();
        $requete = $conn -> prepare("INSERT INTO MEDECIN_ESSAIS(`Id_medecin`, `Id_essai`, `Statut_medecin`) VALUES (?,?,'En attente')");
        $requete -> execute(array($Id_medecin, $Id_essai));
        echo "Demande de participation enregistrée avec succès pour cet essai!";
        Fermer_base($conn);
        $Id_enterprise = Get_Entreprise($Id_essai);
        Generer_notif(5,$Id_essai, $Id_medecin);}
         catch (PDOException $e) {
        echo "Erreur notification: " . $e->getMessage(); }
    }

//vérifiée
//méthode entreprise et médecin
function Retirer_Medecin_Essai(int $Id_essai, int $Id_medecin){

    try{
        $conn = Connexion_base();
        $requete = $conn -> prepare("UPDATE `MEDECIN_ESSAIS` SET `Statut_medecin` = 'Retire' WHERE (`Id_essai` = ? AND `Id_medecin` = ?)");
        $requete -> execute(array($Id_essai, $Id_medecin));
        Fermer_base($conn);
        Generer_notif(18, $Id_essai, $Id_medecin);
        //vérifier le statut 
        $conn = Connexion_base();
        $requete_statut = $conn -> prepare("SELECT `Statut` FROM `ESSAIS_CLINIQUES` WHERE `Id_essai` = ?");
        $requete_statut -> execute(array($Id_essai));
        $tableau = $requete_statut->fetch();
        $statut_essai = $tableau['Statut'];
        if($statut_essai == 'Recrutement' || $statut_essai == 'En cours'){
            Verif_nbMedecin_Essai($Id_essai, 'ok');
            }
        Fermer_base($conn);

    }
    catch (PDOException $e) {
        echo "Erreur notif: " . $e->getMessage(); 
    }

}

//méthode entreprise/admin 

//fonctionne pour changer l'état de l'essai mais affiche une erreur
function Suspendre_Essai(int $Id_essai){

    try{
        $conn = Connexion_base();
        $requete = $conn -> prepare("UPDATE `ESSAIS_CLINIQUES` SET `Statut` = 'Suspendu' WHERE `Id_essai` = ?");
        $requete -> execute(array($Id_essai));
        echo "Essai suspendu avec succès!";
        Fermer_base($conn);
        //Envoi à l'entreprise
        $Id_entreprise = Get_Entreprise($Id_essai);
         Generer_notif(17, $Id_essai, $Id_entreprise);
        // Envoi aux médecins
        $conn = Connexion_base();
        $requete_ter = $conn -> prepare("SELECT `Id_medecin` FROM `MEDECIN_ESSAIS` WHERE `Id_essai`=?");
        $requete_ter->execute(array($Id_essai));
        $tableau_medecin = $requete_ter->fetchAll();
        foreach ($tableau_medecin as $medecin) {
            $Id_medecin = $medecin['Id_medecin'];
            Generer_notif(17, $Id_essai, $Id_medecin);
        }
        Fermer_base($conn);
    }
    catch (PDOException $e) {
        echo "Erreur notif: " . $e->getMessage(); 
    }
   
}

function Relancer_Essai(int $Id_essai){

    try{
        $conn = Connexion_base();
        $requete = $conn -> prepare("UPDATE `ESSAIS_CLINIQUES` SET `Statut` = 'En cours' WHERE `Id_essai` = ?");
        $requete -> execute(array($Id_essai));
        echo "Essai relancé avec succès!";
        Fermer_base($conn);
        //Envoi à l'entreprise
        $Id_entreprise = Get_Entreprise($Id_essai);
         Generer_notif(17, $Id_essai, $Id_entreprise);
        // Envoi aux médecins
        $conn = Connexion_base();
        $requete_ter = $conn -> prepare("SELECT `Id_medecin` FROM `MEDECIN_ESSAIS` WHERE `Id_essai`=?");
        $requete_ter->execute(array($Id_essai));
        $tableau_medecin = $requete_ter->fetchAll();
        foreach ($tableau_medecin as $medecin) {
            $Id_medecin = $medecin['Id_medecin'];
            //Generer_notif(17, $Id_essai, $Id_medecin);
        }
        Fermer_base($conn);
    }
    catch (PDOException $e) {
        echo "Erreur notif: " . $e->getMessage(); 
    }
   
}


//Méthode patient
function Postuler_Patient_Essai(int $Id_essai, int $Id_patient){
   
    try{
        $conn = Connexion_base();
        $DateduJour = date('Y-m-d');
        $requete = $conn -> prepare("INSERT INTO `PATIENTS_ESSAIS`(`Id_patient`, `Id_essai`, `Date_participation`,`Statut_participation`) VALUES (?,?,?,'En attente')");  //quand met-on la date?
        $requete -> execute(array($Id_patient, $Id_essai, $DateduJour));
        echo "Demande de participation enregistrée avec succès pour cet essai!";
        Fermer_base($conn);
        } catch (PDOException $e) {
        echo "Erreur bdd: " . $e->getMessage(); }
    
    try{
        $conn = Connexion_base();
        $requete_bis = $conn->prepare("SELECT `Id_medecin` FROM `MEDECIN_ESSAIS` WHERE `Id_essai`=?");
        $requete_bis->execute(array($Id_essai));
        // Récupérer tous les résultats de la requête
        $tableau = $requete_bis->fetchAll();
        // Vérifier si le tableau contient des résultats
        if ($tableau) {
            foreach ($tableau as $medecin) {
                // Accéder à chaque 'Id_medecin' dans les résultats
                $Id_medecin = $medecin['Id_medecin'];
                // Appeler la fonction Generer_notif pour chaque médecin
                Generer_notif(10, $Id_essai, $Id_medecin);
                Fermer_base($conn);
            }
        } else {
            echo "Aucun médecin trouvé pour cet essai.";
        }}
    catch (PDOException $e) {
        echo "Erreur notif: " . $e->getMessage(); }
    }

//Méthode patient, médecin

function Retirer_Patient_Essai(int $Id_essai, int $Id_patient){
  
    try{
        $conn = Connexion_base();
        $requete = $conn -> prepare("UPDATE `PATIENTS_ESSAIS` SET `Statut_participation` = 'Abandon' WHERE (`Id_essai` = ? AND `Id_patient` = ?)");
        $requete -> execute(array($Id_essai, $Id_patient));
        Fermer_base($conn);
    }
    catch (PDOException $e) {
        echo "Erreur bdd: " . $e->getMessage(); }
    try{
        $conn = Connexion_base();
        //Generer_notif(12,$Id_essai, $Id_patient); //on ne sait pas qui a pris la décision donc notif pas ultra adaptée (on prévient le patient qu'il s'est bien retiré en gros)
        $requete_bis = $conn->prepare("SELECT `Id_medecin` FROM `MEDECIN_ESSAIS` WHERE `Id_essai`=?");
        $requete_bis->execute(array($Id_essai));
        $tableau = $requete_bis->fetchAll();  // Récupère toutes les lignes
        
        // Si vous voulez accéder à la liste des Id_medecin, vous devez boucler sur chaque ligne
        foreach ($tableau as $medecin) {
            $Id_medecin = $medecin['Id_medecin'];  // Accède à la colonne 'Id_medecin'
            Generer_notif(12, $Id_essai, $Id_medecin);  // Appel de la fonction pour chaque Id_medecin
        }
        //on prévient tous les médecins de l'essai
        Fermer_base($conn);
    }
    catch (PDOException $e) {
        echo "Erreur notif: " . $e->getMessage(); }
    }
    

    function Verif_nbMedecin_Essai(int $Id_essai, string $etat) {
        try {        
            // Récupération de la liste des médecins de l'essai, du nombre requis et de l'Id_entreprise
            $conn = Connexion_base();
            $requete = $conn->prepare("SELECT MEDECIN_ESSAIS.Id_medecin,  ESSAIS_CLINIQUES.Nb_medecins, ESSAIS_CLINIQUES.Id_entreprise 
                FROM  ESSAIS_CLINIQUES LEFT JOIN MEDECIN_ESSAIS ON MEDECIN_ESSAIS.Id_essai = ESSAIS_CLINIQUES.Id_essai WHERE ESSAIS_CLINIQUES.Id_essai = ?");
            $requete->execute(array($Id_essai));
            $tableau = $requete->fetchAll(PDO::FETCH_ASSOC);
            // Calcul du nombre de médecins et récupération des autres informations
            $nb_medecin = count(array_filter($tableau, fn($row) => $row['Id_medecin'] !== null)); // Nombre de médecins associés
            $nb_medecins_requis = $tableau[0]['Nb_medecins'];
            $Id_entreprise = $tableau[0]['Id_entreprise'];
            Fermer_base($conn);
            // Cas où l'essai n'avait pas le bon nombre de médecins et l'a maintenant
            if ($etat === 'pas_ok' && $nb_medecin >= $nb_medecins_requis) {
                // Mise à jour du statut de l'essai à "Recrutement"
                $conn = Connexion_base();
                $requete = $conn->prepare("UPDATE `ESSAIS_CLINIQUES` SET `Statut` = 'Recrutement' WHERE `Id_essai` = ?");
                $requete->execute(array($Id_essai));
                // Notification à l'entreprise
                Generer_notif(7, $Id_essai, $Id_entreprise);
                // Notification aux médecins
                foreach ($tableau as $medecin) {
                    if ($medecin['Id_medecin'] !== null) {
                        Generer_notif(7, $Id_essai, $medecin['Id_medecin']);}}
                Fermer_base($conn);
            }
            // Cas où l'essai avait déjà le nombre de médecins requis mais ne l'a plus
            elseif ($etat === 'ok' && $nb_medecin < $nb_medecins_requis) {
                // Mise à jour du statut de l'essai à "Suspendu"
                $conn = Connexion_base();
                $requete = $conn->prepare("UPDATE `ESSAIS_CLINIQUES` SET `Statut` = 'Suspendu' WHERE `Id_essai` = ?");
                $requete->execute(array($Id_essai));
                // Notification à l'entreprise
                Generer_notif(8, $Id_essai, $Id_entreprise);
                // Notification aux médecins
                foreach ($tableau as $medecin) {
                    if ($medecin['Id_medecin'] !== null) {
                        Generer_notif(8, $Id_essai, $medecin['Id_medecin']);}}
                Fermer_base($conn);
            }
        } catch (PDOException $e) {
            echo "Erreur bdd: " . $e->getMessage();
        }
    }
    


    function Verif_nbPatient_Essai(int $Id_essai){
 
        try{
            $conn = Connexion_base();
            // On récupère le nombre de patients dans l'essai
            $requete_patient = $conn -> prepare("SELECT `Id_patient` FROM `PATIENTS_ESSAIS` WHERE `Id_essai`= ? AND `Statut_participation` = 'Actif'");
            $requete_patient -> execute(array($Id_essai));
            $tableau_patient = $requete_patient -> fetchAll();  // Utilisation de fetchAll() pour récupérer tous les patients
            $nb_patient = count($tableau_patient);  // Comptage du nombre de patients
    
            // On récupère le nombre de patients nécessaires pour l'essai
            $requete_necessaire = $conn -> prepare("SELECT `Nb_patients` FROM `ESSAIS_CLINIQUES` WHERE `Id_essai` = ?");
            $requete_necessaire -> execute(array($Id_essai));
            $tableau_necessaire = $requete_necessaire -> fetch();
            $nb_necessaire = $tableau_necessaire["Nb_patients"];
    
            // Si le nombre de patients est suffisant, on change le statut de l'essai
            if($nb_patient >= $nb_necessaire){
                $requete = $conn -> prepare("UPDATE `ESSAIS_CLINIQUES` SET `Statut` = 'En cours' WHERE `Id_essai` = ?");
                $requete -> execute(array($Id_essai));
                Verif_nbMedecin_Essai($Id_essai, 'ok'); //vérification du nombre de médecin au cas où des médecins ont été retiré pendant le recrutement
                Fermer_base($conn);
                echo "Essai lancé avec succès";
            }
        }
        catch (PDOException $e) {
            echo "Erreur bdd: " . $e->getMessage(); 
        }
    
        try{
            $conn = Connexion_base();
            // Envoi des notifications
            // Aux patients
            foreach ($tableau_patient as $patient) {
                $Id_patient = $patient['Id_patient'];  // Récupération de l'ID du patient
                Generer_notif(15, $Id_essai, $Id_patient);  // Envoi de notification au patient
            }
    
            // Notification à l'entreprise
            $Id_entreprise = Get_Entreprise($Id_essai);
            Generer_notif(15, $Id_essai, $Id_entreprise);  // Envoi de notification à l'entreprise
    
            // Notification aux médecins
            $requete_medecin = $conn -> prepare("SELECT `Id_medecin` FROM `MEDECIN_ESSAIS` WHERE `Id_essai` = ?");
            $requete_medecin->execute(array($Id_essai));
            $tableau_medecin = $requete_medecin->fetchAll();
            foreach ($tableau_medecin as $medecin) {
                $Id_medecin = $medecin['Id_medecin'];
                Generer_notif(15, $Id_essai, $Id_medecin);  // Envoi de notification au médecin
            }
    
            Fermer_base($conn);
            echo "Notifications envoyées avec succès";
        }
        catch (PDOException $e) {
            echo "Erreur bdd/notifs: " . $e->getMessage(); 
        }
    }
    

//Méthode entreprise et medecin
function Traiter_Candidature_Medecin(int $Id_essai, int $Id_medecin, int $Reponse){
    // reponse égale à 1 (oui) ou 0 (non)
    try{
        $conn = Connexion_base();
    if($Reponse == 1){
     
        $requete_oui = $conn -> prepare("UPDATE `MEDECIN_ESSAIS` SET `Statut_medecin` = 'Actif' WHERE `Id_medecin` = ?");
        $requete_oui -> execute(array($Id_medecin));
        Fermer_base($conn);
        //on prévient le médecin de l'acceptation
        Generer_notif(22, $Id_essai, $Id_medecin);
        //vérifier le statut 
        $conn = Connexion_base();
        $requete_statut = $conn -> prepare("SELECT `Statut` FROM `ESSAIS_CLINIQUES` WHERE `Id_essai` = ?");
        $requete_statut -> execute(array($Id_essai));
        $tableau = $requete_statut->fetch();
        $statut_essai = $tableau["Statut"];
        if($statut_essai == 'En attente'){
            Verif_nbMedecin_Essai($Id_essai, 'pas ok');}
        Fermer_base($conn);
        
    }
    if($Reponse == 0){
        $requete_non = $conn -> prepare("DELETE FROM `MEDECIN_ESSAIS` WHERE `Id_medecin` = ?");
        $requete_non -> execute(array($Id_medecin));
        //on prévient le médecin du refus
        Generer_notif(23, $Id_essai, $Id_medecin); 
    }

    Fermer_base($conn);}
    //gérer le verif nombre medecin
    catch (PDOException $e) {
        echo "Erreur bdd/notifs: " . $e->getMessage(); }
}

//Méthode médecins
function Traiter_Candidature_Patient(int $Id_essai, int $Id_patient, int $Reponse){
    // reponse égale à 1 (oui) ou 0 (non)

    try{
        $conn = Connexion_base();
    if($Reponse == 1){
        $requete_oui = $conn -> prepare("UPDATE `PATIENTS_ESSAIS` SET `Statut_participation` = 'Actif' WHERE `Id_patient` = ?");
        $requete_oui -> execute(array($Id_patient));
        // on prévient le patient de la validation
        Generer_notif(11, $Id_essai, $Id_patient);
        // on ajoute la date de participation du patient
        $DateduJour = date("Y-m-d H:i:s");
        $requete_date = $conn -> prepare("UPDATE `PATIENTS_ESSAIS` SET `Date_participation` = ? WHERE `Id_patient` = ?");
        $requete_date -> execute(array($DateduJour, $Id_patient));
        //on vérifie si le nombre de patient est atteint
        Verif_nbPatient_Essai($Id_essai);
    }
    elseif($Reponse == 0){
        $requete_non = $conn -> prepare("UPDATE `PATIENTS_ESSAIS` SET `Statut_participation` = 'Refus' WHERE `Id_patient` = ?");
        $requete_non -> execute(array($Id_patient));
        // on prévient le patient du refus
        Generer_notif(21, $Id_essai, $Id_patient);
    }
    else{
        echo 'la réponse doit être oui ou non';
    }
    Fermer_base($conn);
}
    catch (PDOException $e) {
        echo "Erreur bdd/notifs: " . $e->getMessage(); }

    Fermer_base($conn);
}



//fonction qui vérifie si le patient est déjà accepté dans un essai: return True ou False
function Verif_Participation_Patient($Id_patient){
    try{
        $conn = Connexion_base();
        $requete = $conn -> prepare("SELECT COUNT(*) AS is_active FROM `PATIENTS_ESSAIS` WHERE (`Id_patient` = ? AND `Statut_participation` = 'Actif')");
        $requete -> execute(array($Id_patient));
        $est_actif = $requete->fetch(PDO::FETCH_ASSOC);
        Fermer_base($conn);
        // Retourner TRUE si actif, sinon FALSE
        return $est_actif['is_active'] > 0;
       }
    catch (PDOException $e){
        echo "Erreur bdd/notifs: " . $e->getMessage(); }
}

//fonction qui renvoie un booléen vérifiant si un patient est dans cet essai
function Verif_Patient_Cet_Essai($Id_essai, $Id_patient){
    try{
        $conn = Connexion_base();
        $requete = $conn -> prepare("SELECT COUNT(*) AS is_active FROM `PATIENTS_ESSAIS` WHERE (`Id_patient` = ? AND `Id_essai` = ? AND (`Statut_participation` = 'Actif' OR  `Statut_participation` = 'En attente'))");
        $requete -> execute(array($Id_patient, $Id_essai));
        $est_actif = $requete->fetch(PDO::FETCH_ASSOC);
        // Retourner TRUE si actif, sinon FALSE
        Fermer_base($conn);
        return $est_actif['is_active'] > 0;
        }
    catch (PDOException $e){
        echo "Erreur bdd/notifs: " . $e->getMessage(); }

}

//fonction qui vérifie si un médecin est déjà en charge d'un essai donnée: return True ou False
function Verif_Participation_Medecin($Id_medecin, $Id_essai){
    try{
        $conn = Connexion_base();
        $requete = $conn -> prepare("SELECT COUNT(*) AS is_active FROM `MEDECIN_ESSAIS` WHERE (`Id_essai` = ? AND `Id_medecin` = ? AND (`Statut_medecin` = 'Actif' OR `Statut_medecin` = 'Termine'))");
        $requete -> execute(array($Id_essai, $Id_medecin));
        $est_actif = $requete->fetch(PDO::FETCH_ASSOC);
        // Retourner TRUE si actif, sinon FALSE
        Fermer_base($conn);
        return $est_actif['is_active'] > 0;
        }
    catch (PDOException $e){
        echo "Erreur bdd/notifs: " . $e->getMessage(); }
}     


function Afficher_Essai($Id_essai){
    $conn = Connexion_base();
    $stmt = $conn->prepare("SELECT ESSAIS_CLINIQUES.*, ENTREPRISES.Nom_entreprise  FROM ESSAIS_CLINIQUES 
    INNER JOIN ENTREPRISES ON ESSAIS_CLINIQUES.Id_entreprise = ENTREPRISES.Id_entreprise WHERE ESSAIS_CLINIQUES.Id_essai = ?");
    $stmt->execute(array($Id_essai));
    $resultats = $stmt->fetch(PDO::FETCH_ASSOC);
    Fermer_base($conn);
//on recupère les nom des médecins
    $conn = Connexion_base();
    $requete_medecin = $conn -> prepare("SELECT CONCAT('Dr. ', MEDECINS.Prenom, ' ', MEDECINS.Nom) AS Medecin
    FROM MEDECINS INNER JOIN MEDECIN_ESSAIS ON MEDECINS.Id_medecin = MEDECIN_ESSAIS.Id_medecin
    WHERE MEDECIN_ESSAIS.Id_essai = ?  AND (MEDECIN_ESSAIS.Statut_medecin = 'Actif' OR MEDECIN_ESSAIS.Statut_medecin = 'Termine')");
     $requete_medecin -> execute(array($Id_essai));
     $medecins = $requete_medecin -> fetchAll(PDO::FETCH_ASSOC);
    Fermer_base($conn);
    echo '<ul class="indiv_trials">';
    echo '<li class="indiv_trial_title"> ' . htmlspecialchars($resultats['Titre']) . '</li><br>'; 
    echo '<li><strong> Mené par l\'entreprise '. htmlspecialchars($resultats['Nom_entreprise']) .'</strong></li>';
    echo '<li><strong> Encadré par: </strong>';
    $medecins_list = []; // Crée un tableau pour stocker les médecins
foreach ($medecins as $medecin) {
    $medecins_list[] = htmlspecialchars($medecin['Medecin']); // Ajoute chaque médecin au tableau
}

echo implode(', ', $medecins_list); // Affiche les médecins séparés par des virgules
echo '</li><br><br>';
    echo '<li><strong>Contexte :</strong> ' . htmlspecialchars($resultats['Contexte']) . '</li>';
    echo '<li><strong>Objectif de l\'essai :</strong> ' . htmlspecialchars($resultats['Objectif_essai']) . '</li>';
    echo '<li><strong>Design de l\'étude :</strong> ' . htmlspecialchars($resultats['Design_etude']) . '</li>';
    echo '<li><strong>Critère d\'évaluation :</strong> ' . htmlspecialchars($resultats['Critere_evaluation']) . '</li>';
    echo '<li><strong>Résultats attendus :</strong> ' . htmlspecialchars($resultats['Resultats_attendus']) . '</li>';
    echo '</ul>';
    
}

function Recup_Patients($Id_essai){
    try{
        $conn = Connexion_base();
        $requete = $conn-> prepare("SELECT PATIENTS.Id_patient, PATIENTS.Nom, PATIENTS.Prenom FROM PATIENTS
        INNER JOIN PATIENTS_ESSAIS ON PATIENTS.Id_patient = PATIENTS_ESSAIS.Id_patient WHERE ((PATIENTS_ESSAIS.Statut_participation = 'Actif' OR PATIENTS_ESSAIS.Statut_participation = 'Termine')AND PATIENTS_ESSAIS.Id_essai = ?) ORDER BY PATIENTS.Nom, PATIENTS.Prenom ");
        $requete -> execute(array($Id_essai));
        $resultats_actif = $requete->fetchAll(PDO::FETCH_ASSOC);
        Fermer_base($conn);
        $conn = Connexion_base();
        $requete_attente = $conn-> prepare("SELECT PATIENTS.Id_patient, PATIENTS.Nom, PATIENTS.Prenom FROM PATIENTS
        INNER JOIN PATIENTS_ESSAIS ON PATIENTS.Id_patient = PATIENTS_ESSAIS.Id_patient WHERE (PATIENTS_ESSAIS.Id_essai = ? AND PATIENTS_ESSAIS.Statut_participation = 'En attente') ORDER BY PATIENTS.Nom, PATIENTS.Prenom ");
        $requete_attente -> execute(array($Id_essai));
        $resultats_attente = $requete_attente->fetchAll(PDO::FETCH_ASSOC);
        return [$resultats_actif, $resultats_attente];
    }
    catch (PDOException $e){
        echo "Erreur bdd/notifs: " . $e->getMessage(); }
}

function Recup_medecins($Id_essai){
    try{
        $conn = Connexion_base();
        $requete = $conn-> prepare("SELECT MEDECINS.Id_medecin, MEDECINS.Nom, MEDECINS.Prenom, MEDECINS.Specialite, MEDECINS.Matricule, MEDECINS.Telephone FROM MEDECINS
        INNER JOIN MEDECIN_ESSAIS ON MEDECINS.Id_medecin = MEDECIN_ESSAIS.Id_medecin WHERE ((MEDECIN_ESSAIS.Statut_medecin = 'Actif' OR MEDECIN_ESSAIS.Statut_medecin = 'Termine')AND MEDECIN_ESSAIS.Id_essai = ?) ORDER BY MEDECINS.Nom, MEDECINS.Prenom ");
        $requete -> execute(array($Id_essai));
        $resultats_actif = $requete->fetchAll(PDO::FETCH_ASSOC);
        Fermer_base($conn);
        $conn = Connexion_base();
        $requete_attente = $conn-> prepare("SELECT MEDECINS.Id_medecin, MEDECINS.Nom, MEDECINS.Prenom, MEDECINS.Specialite, MEDECINS.Matricule, MEDECINS.Telephone FROM MEDECINS
        INNER JOIN MEDECIN_ESSAIS ON MEDECINS.Id_medecin = MEDECIN_ESSAIS.Id_medecin WHERE (MEDECIN_ESSAIS.Statut_medecin = 'En attente' AND MEDECIN_ESSAIS.Id_essai = ?) ORDER BY MEDECINS.Nom, MEDECINS.Prenom ");
        $requete_attente -> execute(array($Id_essai));
        $resultats_attente = $requete_attente->fetchAll(PDO::FETCH_ASSOC);
        return [$resultats_actif, $resultats_attente];
    }
    catch (PDOException $e){
        echo "Erreur bdd/notifs: " . $e->getMessage(); }
}


//permet l'affichage d'un tableau avec la liste des patients acceptés OU en attente, le statut_participation prend 'ACtif' ou 'En attente'
function Afficher_Patients($Id_essai, $Statut_participation){
    if($Statut_participation=='Actif'){
    $tableau_patients = Recup_Patients($Id_essai)[0];
    echo '<h1>Liste des Patients</h1>';}
    if($Statut_participation=='En attente'){
        $tableau_patients = Recup_Patients($Id_essai)[1];
    echo'<h1>Liste des Patients En Attente</h1>';}

    echo '
        <table>
            <thead>
                <tr>
                
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>';
    if (empty($tableau_patients)) {
        echo '<tr>
                <td colspan="4">Aucun patient trouvé.</td>
              </tr>';
    } else {
        echo '<form method="POST">';
        foreach ($tableau_patients as $patient) {
            echo '<tr>
                <td>' . htmlspecialchars($patient["Nom"]) . '</td>
                <td>' . htmlspecialchars($patient["Prenom"]) . '</td>
                <td>';
                
                if ($Statut_participation == 'Actif'){
                    echo '
                        <button class="btn" onclick="A(' . htmlspecialchars($patient["Id_patient"]) . ')">Consulter la fiche</button>   
                        <button name = "action" value="retirer patient" class="btn delete">Retirer de l\'essai</button>
                    ';
                }
                if ($Statut_participation == 'En attente'){
                    echo '
                        <button name = "action" value="accepter patient" class="btn delete">Accepter le patient</button>
                        <button name = "action" value="refuser patient" class="btn delete">Refuser le patient</button>
                    ';
                }

                echo '</tr>';
                
                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

                }
                if ($_SERVER['REQUEST_METHOD']=== 'POST') {
                 if (isset($_POST['action'])) {
                     if ($_POST['action'] === 'retirer patient') {
                         Retirer_Patient_Essai($Id_essai, $patient["Id_patient"]);
                     }
                     if ($_POST['action'] === 'accepter patient') {
                        Traiter_Candidature_Patient($Id_essai, $patient["Id_patient"],1);
                    }
                    if ($_POST['action'] === 'refuser patient') {
                        Traiter_Candidature_Patient($Id_essai, $patient["Id_patient"],0);
                    }
            }}
        }
        echo '</POST>';
    }    
    echo '</tbody></table>';
}

function Afficher_Medecins($Id_essai, $Statut_medecin){
    if($Statut_medecin=='Actif'){
         $tableau_medecins = Recup_Medecins($Id_essai)[0];
    echo '<h1>Liste des Medecins</h1>';}
    if($Statut_medecin=='En attente'){
        $tableau_medecins = Recup_Medecins($Id_essai)[1];
    echo'<h1>Liste des Medecins En Attente</h1>';}

    echo '
        <table>
            <thead>
                <tr>
                
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Spécialité</th>
                    <th>Matricule</th>
                    <th>Numéro de téléphone</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>';
    if (empty($tableau_medecins)) {
        echo '<tr>
                <td colspan="4">Aucun médecin trouvé.</td>
              </tr>';
    } else {
        echo '<form method="POST">';
        foreach ($tableau_medecins as $medecin) {
            echo '<tr>
                <td>' . htmlspecialchars($medecin["Nom"]) . '</td>
                <td>' . htmlspecialchars($medecin["Prenom"]) . '</td>
                <td>' . htmlspecialchars($medecin["Specialite"]) . '</td>
                <td>' . htmlspecialchars($medecin["Matricule"]) . '</td>
                <td>' . htmlspecialchars($medecin["Telephone"]) . '</td>
                <td>';
               
                if ($Statut_medecin == 'Actif'){
                    echo '
                        <button name = "action" value="retirer medecin" class="btn delete">Retirer de l\'essai</button>
                    ';
                }
                if ($Statut_medecin == 'En attente'){
                    echo '
                    <button name = "action" value="accepter medecin" class="btn delete">Accepter le médecin</button>
                    <button name = "action" value="refuser medecin" class="btn delete">Refuser le médecin</button>
                ';
                
                }

                echo '</tr>';
            }
                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

                }
                if ($_SERVER['REQUEST_METHOD']=== 'POST') {
                 if (isset($_POST['action'])) {
                     if ($_POST['action'] === 'retirer medecin') {
                         Retirer_Medecin_Essai($Id_essai, $medecin['Id_medecin']);
                     }
                     if ($_POST['action'] === 'accepter medecin') {
                        Traiter_Candidature_Medecin($Id_essai, $medecin['Id_medecin'],1);
                    }
                    if ($_POST['action'] === 'accepter medecin') {
                        Traiter_Candidature_Medecin($Id_essai, $medecin['Id_medecin'],0);
                    }
            }}
        }
        echo '</POST>';
    
    echo '</tbody></table>';
 }

function A() {
    // Définir la logique pour la fonction
}



function Generer_Notif($code, $Id_essai, $Id_destinataire){}


function affichage_request_medecin($Id_essai, $praticien){

echo '<h1>Choisissez un médecin parmi cette liste</h1>';
echo '
    <table>
        <thead>
            <tr>
            
                <th>Nom</th>
                <th>Prénom</th>
                <th>Spécialité</th>
                <th>Matricule</th>
                <th>Numéro de téléphone</th>
                <th></th>
            </tr>
        </thead>
        <tbody>';
foreach($praticien as $medecin) {
    $medecin = $medecin[0];
    echo '<tr>
        <td>' . htmlspecialchars($medecin["Nom"]) . '</td>
        <td>' . htmlspecialchars($medecin["Prenom"]) . '</td>
        <td>' . htmlspecialchars($medecin["Specialite"]) . '</td>
        <td>' . htmlspecialchars($medecin["Matricule"]) . '</td>
        <td>' . htmlspecialchars($medecin["Telephone"]) . '</td>
        <td>';
            echo '
                
                <button (' . htmlspecialchars($Id_essai) . ', ' . htmlspecialchars($medecin["Id_medecin"]) . ')">Demander ce médecin</button>
            ';

    
}
echo '</tr>';
        
echo '</tbody></table>';
}

function verif_entreprise($Id_essai, $Id_entreprise) {
$conn = Connexion_base();

try {
    $sql = "
        SELECT EXISTS (
            SELECT 1
            FROM ESSAIS_CLINIQUES
            WHERE Id_entreprise = :Id_entreprise
            AND Id_essai = :Id_essai
        ) AS EssaiTrouve;
    "; 
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':Id_entreprise', $Id_entreprise, PDO::PARAM_INT);
    $stmt->bindParam(':Id_essai', $Id_essai, PDO::PARAM_INT);
    $stmt->execute();
    
    // Récupérer le résultat de la requête
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Si l'essai est trouvé, retourne true, sinon false
    return (bool)$result['EssaiTrouve'];
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
    return false;
} finally {
    // Fermer la connexion
    Fermer_base($conn);
}
}

