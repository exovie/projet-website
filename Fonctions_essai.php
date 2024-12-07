<?php


error_reporting(E_ALL); // Active le rapport de toutes les erreurs
ini_set('display_errors', 1); // Affiche les erreurs à l'écran
ini_set('display_startup_errors', 1); // Affiche les erreurs au démarrage de PHP


// Inclure le module ou fichier PHP
//include 'module.php';

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
//se pencher sur l'histoire de statut en pause des médecins dans suspendre essais

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
        echo "Erreur bdd: " . $e->getMessage(); }
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
            $Id_medecin = $medecin['Id_medecin']
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
        $requete_statut = $conn -> prepare("SELECT `Statut` FROM `ESSAIS_CLINIQUES WHERE `Id_essai`=?");
        $requete_statut -> execute(array($Id_essai));
        $tableau_statut = $requete_statut -> fetch();
        $statut = $tableau_statut['Statut'];
        //si l'essai n'est pas encore publié pour les patients, modification des données
        if($statut == 'En attente'){
            $requete_update = $conn -> prepare("UPDATE `ESSAIS_CLINIQUES` 
        SET(`Titre` = ?, `Contexte` = ?, `Objectif_essai`= ?, `Design_etude` = ?, `Criteres_evaluation`= ?,`Resultats_attendus`= ?, `Nb_medecins`=?, `Nb_patients` = ?) WHERE `Id_essai`=?");
            $requete_update = execute(array($Titre, $Contexte, $Objectif_essai, $Design_etude, $Criteres_evaluation, $Resultats_attendus, $Nb_medecins, $Nb_patients));
        
        //Envoi des notifs aux médecins de l'essai
        $requete_medecin = $conn -> prepare("SELECT `Id_medecin` FROM `MEDECIN_ESSAIS` WHERE `Id_essai`=?");
        $requete_medecin->execute(array($Id_essai));
        $tableau = $requete_medecin->fetch();
        $Liste_medecin = $tableau["Id_medecin"];
        foreach ($Liste_medecin as $Id_medecin) {
            //Generer_notif(16, $Id_medecin);
        }}
        Fermer_base($conn);
        echo "Notifs envoyées avec succès";
    }
    catch (PDOException $e) {
        echo "Erreur bdd/notifs: " . $e->getMessage();
}

}

//Méthode médecin
function Modif_Infos_Essai(int $Id_patient, int $Id_essai, int $Poids, string $Traitements, string $Allergies){

    try{
        $conn = Connexion_base();
        //Modification du poids, des traitements et des allergies
        $requete_update = $conn -> prepare("UPDATE `PATIENTS` SET(`Poids` = ?, `Traitements` = ?, `Allergies`= ?)  WHERE `Id_patient`=?");
        $requete_update = execute(array($Poids, $Traitements, $Allergies, $Id_patient));
        Fermer_base($conn);
        echo "Info patients modifiées avec succès";
    }
    catch (PDOException $e) {
        echo "Erreur bdd: " . $e->getMessage();
}
}



//méthode entreprise
//vérifiée
function Demander_Medecin_essai(int $Id_essai, int $Id_medecin){
    echo 'hello';
    try {
    $conn = Connexion_base();
    $requete = $conn -> prepare("INSERT INTO MEDECIN_ESSAIS(`Id_medecin`, `Id_essai`, `Statut_medecin`) VALUES (?,?,'Sollicite')");
    $requete -> execute(array($Id_medecin, $Id_essai));
    echo "Nouveau médecin sollicité avec succès pour cet essai!";
    Fermer_base($conn);
    } catch (PDOException $e) {
    echo "Erreur bdd: " . $e->getMessage(); }
    try{
        //Generer_notif(3,$Id_essai, $Id_medecin);
    }
     catch (PDOException $e) {
    echo "Erreur notification: " . $e->getMessage(); }

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
        } catch (PDOException $e) {
        echo "Erreur bdd: " . $e->getMessage(); }
        try{
            $conn = Connexion_base();
            $requete_bis = $conn -> prepare("SELECT `Id_entreprise` FROM `ESSAIS_CLINIQUES` WHERE `Id_essai`=?");
            $requete_bis->execute(array($Id_essai));
            $tableau = $requete_bis->fetch();
            $Id_entreprise = $tableau["Id_entreprise"];
            //Generer_notif(5,$Id_essai, $Id_entreprise);
            Fermer_base($conn);
        }
         catch (PDOException $e) {
        echo "Erreur notification: " . $e->getMessage(); }

    }
//vérifiée
//méthode enterprise
function Retirer_Medecin_Essai(int $Id_essai, int $Id_medecin){

    try{
        $conn = Connexion_base();
        $requete = $conn -> prepare("UPDATE `MEDECIN_ESSAIS` SET `Statut_medecin` = 'Retire' WHERE (`Id_essai` = ? AND `Id_medecin` = ?)");
        $requete -> execute(array($Id_essai, $Id_medecin));
        echo "Medecin retiré de l'essai avec succès!";
        Fermer_base($conn);
    }
 catch (PDOException $e) {
    echo "Erreur bdd: " . $e->getMessage(); 
}
    try{
        //Generer_notif(18, $Id_essai, $$Id_medecin);
    }
    catch (PDOException $e) {
        echo "Erreur notif: " . $e->getMessage(); 
    }
    //Verif_nbMedecin_Essai($Id_essai);
}

//méthode entreprise/admin catch (PDOException $e) {
// se pencher sur le statut en pause
//fonctionne pour changer l'état de l'essai mais affiche une erreur
function Suspendre_Essai(int $Id_essai){

    try{
        $conn = Connexion_base();
        $requete = $conn -> prepare("UPDATE `ESSAIS_CLINIQUES` SET `Statut` = 'Suspendu' WHERE `Id_essai` = ?");
        $requete -> execute(array($Id_essai));
        echo "Essai suspendu avec succès!";
        Fermer_base($conn);
        $requete_bis = $conn -> prepare("UPDATE `MEDECIN_ESSAIS` SET `Statut_medecin` = '' WHERE (`Id_essai` = ? AND `Id_medecin` = ?)"); //a-t-on vraiment besoin d'un statut en pause pour les médecins? dans ce cas là, le rajouter dans la ligne et la bdd
        $requete_bis -> execute(array($Id_essai, $Id_medecin));
        echo "Medecin mis en pause de l'essai  succès!";
        Fermer_base($conn);
    }
    catch (PDOException $e) {
        echo "Erreur bdd: " . $e->getMessage(); 
    }
    try{
        //Envoi à l'entreprise
        $conn = Connexion_base();
        $requete_bis = $conn -> prepare("SELECT `Id_entreprise` FROM `ESSAIS_CLINIQUES` WHERE `Id_essai`=?");
        $requete_bis->execute(array($Id_essai));
        $tableau = $requete_bis->fetch();
        $Id_entreprise = $tableau["Id_entreprise"];
       // Generer_notif(17, $Id_entreprise);
        // Envoi aux médecins
        $requete_ter = $conn -> prepare("SELECT `Id_medecin` FROM `MEDECIN_ESSAIS` WHERE `Id_essai`=?");
        $requete_ter->execute(array($Id_essai));
        $tableau = $requete_ter->fetch();
        $Liste_medecin = $tableau["Id_medecin"];
        foreach ($Liste_medecin as $Id_medecin) {
            //Generer_notif(17, $Id_essai, $Id_medecin);
        }
        Fermer_base($conn);
        echo "Notifs envoyées avec succès";
    }
    catch (PDOException $e) {
        echo "Erreur notif: " . $e->getMessage(); 
    }

   
}


//Méthode patient
function Postuler_Patient_Essai(int $Id_essai, int $Id_patient){
   
    try{
        $conn = Connexion_base();
        $requete = $conn -> prepare("INSERT INTO `PATIENTS_ESSAIS(`Id_patient`, `Id_essai`, `Date_participation`,`Statut_participation`) VALUES (?,?,'0000/00/00','En attente')");  //quand met-on la date?
        $requete -> execute(array($Id_medecin, $Id_essai));
        echo "Demande de participation enregistrée avec succès pour cet essai!";
        Fermer_base($conn);
        } catch (PDOException $e) {
        echo "Erreur bdd: " . $e->getMessage(); }
    
    try{
        $conn = Connexion_base();
        $requete_bis = $conn -> prepare("SELECT `Id_medecin` FROM `MEDECIN_ESSAIS` WHERE `Id_essai`=?");
        $requete_bis->execute(array($Id_essai));
        $tableau = $requete_bis->fetch();
        $Liste_medecin = $tableau["Id_medecin"];
        foreach ($Liste_medecin as $Id_medecin) {
        //Generer_notif(10, $Id_essai,$Id_medecin);}
        Fermer_base($conn);
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
        echo "Patient retiré avec succès de l'essai";
    }
    catch (PDOException $e) {
        echo "Erreur bdd: " . $e->getMessage(); }
    try{
        $conn = Connexion_base();
        //Generer_notif(12,$Id_essai, $Id_patient); //on ne sait pas qui a pris la décision donc notif pas ultra adaptée (on prévient le patient qu'il s'est bien retiré en gros)
        $requete_bis = $conn -> prepare("SELECT `Id_medecin` FROM `MEDECIN_ESSAIS` WHERE `Id_essai`=?");
        $requete_bis->execute(array($Id_essai));
        $tableau = $requete_bis->fetch();
        $Liste_medecin = $tableau["Id_medecin"];
        foreach ($Liste_medecin as $Id_medecin) {
        //Generer_notif(12, $Id_essai,$Id_medecin);
        } //on prévient tous les médecins de l'essai
        Fermer_base($conn);
    }
    catch (PDOException $e) {
        echo "Erreur notif: " . $e->getMessage(); }
    }
    

function Verif_nbMedecin_Essai(int $Id_essai){

    try{ 

        $conn = Connexion_base();
        $requete_compte = $conn -> prepare("SELECT COUNT(*) AS 'nombre' FROM `MEDECIN_ESSAIS`WHERE (`Id_essai` = ? AND `Statut_medecin` = 'Actif')");
        $requete_compte -> execute(array($Id_essai));
        $tableau_compte = $requete_compte-> fetch();
        $nb_medecin = $tableau_compte['nombre']; 
        if ($nb_medecin > 1){
        $requete = $conn -> prepare("UPDATE `ESSAIS_CLINIQUES` SET `Statut` = 'Recrutement' WHERE `Id_essai` = ?");
        $requete -> execute(array($Id_essai));
        Fermer_base($conn);
        echo "Patient retiré avec succès de l'essai";}
    }
    catch (PDOException $e) {
        echo "Erreur bdd: " . $e->getMessage(); }
    try{
        $conn = Connexion_base();
        //Notif aux  médecins
        $requete_bis = $conn -> prepare("SELECT `Id_medecin` FROM `MEDECIN_ESSAIS` WHERE `Id_essai`=?");
        $requete_bis->execute(array($Id_essai));
        $tableau = $requete_bis->fetch();
        $Liste_medecin = $tableau["Id_medecin"];
        foreach ($Liste_medecin as $Id_medecin) {
        //Generer_notif(7, $Id_essai,$Id_medecin);
        }
        //Notif à l'entreprise
        $requete_bis = $conn -> prepare("SELECT `Id_entreprise` FROM `ESSAIS_CLINIQUES` WHERE `Id_essai`=?");
        $requete_bis->execute(array($Id_essai));
        $tableau_bis = $requete_bis->fetch();
        $Id_entreprise = $tableau_bis["Id_entreprise"];
        //Generer_notif(7, $Id_essai, $Id_entreprise);
        Fermer_base($conn);
        echo "Notif envoyées avec succès";
    }
    catch (PDOException $e) {
        echo "Erreur bdd/notif: " . $e->getMessage(); }
    }

    
function Verif_nbPatient_Essai(int $Id_essai){
 
    try{
        $conn = Connexion_base();
        //on récupère le nombre de patient
        $requete_patient = $conn -> prepare("SELECT `Id_patient` FROM `PATIENTS_ESSAIS` WHERE (`Id_essai`= ? AND `Statut_participation` = 'Actif')");
        $requete_patient -> execute(array($Id_essai));
        $tableau_patient = $requete_patient -> fetch();
        $liste_patient = $tableau_patient["Id_patient"];
        $nb_patient = count($liste_patient);
        // on récupère le nombre de patients nécessaire
        $requete_necessaire = $conn -> prepare("SELECT `Nb_patients` FROM `ESSAIS_CLINIQUES` WHERE `Id_essai` = ?");
        $requete_necessaire -> execute(array($Id_essai));
        $tableau_necessaire = $requete_necessaire -> fetch();
        $nb_necessaire = $tableau_necessaire["Nb_patients"];
        if($nb_patient>= $nb_necessaire){
            $requete = $conn -> prepare("UPDATE `ESSAIS_CLINIQUES` SET `Statut` = 'En cours' WHERE `Id_essai` = ?");
            $requete -> execute(array($Id_essai));
            Fermer_base($conn);
            echo "Essai lancé avec succès";
            }
    }
    catch (PDOException $e) {
        echo "Erreur bdd: " . $e->getMessage(); }
    try{
        $conn = Connexion_base();
        //envoi des notifications
        //aux patients
        foreach ($liste_patient as $Id_patient){
            //Generer_notif(15, $Id_essai, $Id_patient);
        }
        // à l'entreprise
        $requete_entreprise = $conn -> prepare("SELECT `Id_entreprise` FROM `ESSAIS_CLINIQUES` WHERE `Id_essai`=?");
        $requete_entreprise->execute(array($Id_essai));
        $tableau_entreprise = $requete_entreprise->fetch();
        $Id_entreprise = $tableau_entreprise["Id_entreprise"];
        //Generer_notif(15, $Id_essai, $Id_entreprise);
        //aux médecins
        $requete_medecin= $conn -> prepare("SELECT `Id_medecin` FROM `MEDECIN_ESSAIS` WHERE `Id_essai`=?");
        $requete_medecin->execute(array($Id_essai));
        $tableau_medecin = $requete_medecin->fetch();
        $Liste_medecin = $tableau_medecin["Id_medecin"];
        foreach ($Liste_medecin as $Id_medecin) {
        //Generer_notif(15, $Id_essai,$Id_medecin);}
        Fermer_base($conn);
        echo "notifs envoyées avec succès";
    }}
    catch (PDOException $e) {
        echo "Erreur bdd/notifs: " . $e->getMessage(); }

}

//Méthode entreprise
function Traiter_Candidature_Medecin(int $Id_essai, int $Id_medecin, string $Reponse){
    // reponse égale à "oui" ou "non"
    
    try{
        $conn = Connexion_base();
    if($Reponse == 'oui'){
        $requete_oui = $conn -> prepare("UPDATE `MEDECINS_ESSAIS` SET `Statut` = 'Actif' WHERE `Id_medecin` = ?");
        $requete_oui -> execute(array($Id_medecin));
        //on prévient le médecin de l'acceptation
        //Generer_notif(22, $Id_essai, $Id_medecin);
        
    }
    if($Reponse == 'non'){
        $requete_non = $conn -> prepare("DELETE FROM `MEDECINS_ESSAIS` WHERE `Id_medecin` = ?");
        $requete_non -> execute(array($Id_medecin));
        //on prévient le médecin du refus
        //Generer_notif(23, $Id_essai, $Id_medecin);
        
    }
    else{
        echo 'la réponse doit être oui ou non';
    }}
    //gérer le verif nombre medecin
    catch (PDOException $e) {
        echo "Erreur bdd/notifs: " . $e->getMessage(); }
}

//Méthode médecins
function Traiter_Candidature_Patient(int $Id_entreprise, int $Id_patient, string $Reponse){
    // reponse égale à "oui" ou "non"

    try{
        $conn = Connexion_base();
    if($Reponse == 'oui'){
        $requete_oui = $conn -> prepare("UPDATE `PATIENTS_ESSAIS` SET `Statut` = 'Actif' WHERE `Id_patient` = ?");
        $requete_oui -> execute(array($Id_patient));
        // on prévient le patient de la validation
        //Generer_notif(11, $Id_essai, $Id_patient);
        // on ajoute la date de participation du patient
        $DateduJour = date("Y-m-d H:i:s");
        $requete_date = $conn -> prepare("UPDATE `PATIENTS_ESSAIS` SET `Date_participation` = ? WHERE `Id_patient` = ?");
        $requete_date -> execute(array($DateduJour, $Id_patient));
        //on vérifie si le nombre de patient est atteint
        Verif_nbPatient_Essai($Id_essai);
    }
    if($Reponse == 'non'){
        $requete_non = $conn -> prepare("UPDATE `PATIENTS_ESSAIS` SET `Statut` = 'Refus' WHERE `Id_patient` = ?");
        $requete_non -> execute(array($Id_patient));
        // on prévient le patient du refus
        //Generer_notif(21, $Id_essai, $Id_patient);
    }
    else{
        echo 'la réponse doit être oui ou non';
    }}
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
        // Retourner TRUE si actif, sinon FALSE
        return $est_actif['is_active'] > 0;
        Fermer_base($conn);}
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
        return $est_actif['is_active'] > 0;
        Fermer_base($conn);}
    catch (PDOException $e){
        echo "Erreur bdd/notifs: " . $e->getMessage(); }

}

//fonction qui vérifie si un médecin est déjà en charge d'un essai donnée: return True ou False
function Verif_Participation_Medecin($Id_medecin, $Id_essai){
    try{
        $conn = Connexion_base();
        $requete = $conn -> prepare("SELECT COUNT(*) AS is_active FROM `MEDECIN_ESSAIS` WHERE (`Id_essai` = ? AND `Id_medecin` = ?)");
        $requete -> execute(array($Id_essai, $Id_medecin));
        $est_actif = $requete->fetch(PDO::FETCH_ASSOC);
        // Retourner TRUE si actif, sinon FALSE
        return $est_actif['is_active'] > 0;
        Fermer_base($conn);}
    catch (PDOException $e){
        echo "Erreur bdd/notifs: " . $e->getMessage(); }
}     






function Afficher_Essai($Id_essai){
    $conn = Connexion_base();
    $stmt = $conn->prepare("SELECT * FROM `ESSAIS_CLINIQUES` WHERE `Id_essai` = ?");
    $stmt->execute(array($Id_essai));

    // Récupérer les résultats
    $resultats = $stmt->fetch(PDO::FETCH_ASSOC);
    echo '<ul class="indiv_trials">';
    echo '<li class="indiv_trial_title"> ' . htmlspecialchars($resultats['Titre']) . '</li><br><br>'; 
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
function Afficher_Patients($Id_essai, $Statut_participation, $Id_entreprise){
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
        foreach ($tableau_patients as $patient) {
            echo '<tr>
                <td>' . htmlspecialchars($patient["Nom"]) . '</td>
                <td>' . htmlspecialchars($patient["Prenom"]) . '</td>
                <td>';
                if ($Statut_participation == 'Actif'){
                    echo '
                        <button class="btn" onclick="A(' . htmlspecialchars($patient["Id_patient"]) . ')">Consulter la fiche</button>   
                        <button class="btn delete" onclick="Retirer_Patient_Essai(' . htmlspecialchars($Id_essai) . ', ' . htmlspecialchars($patient["Id_patient"]) . ')">Retirer de l\'essai</button>
                    ';
                }
                if ($Statut_participation == 'En attente'){
                    echo '
                        <button class="btn delete" onclick="Traiter_Candidature_Patient('. htmlspecialchars($Id_entreprise).',' . htmlspecialchars($patient["Id_patient"]) . ', ' . htmlspecialchars('oui') . ')">Accepter le patient</button>
                        <button class="btn delete" onclick="Traiter_Candidature_Patient('. htmlspecialchars($Id_entreprise).',' . htmlspecialchars($patient["Id_patient"]) . ', ' . htmlspecialchars('non') . ')">Refuser le patient</button>
                    ';
                }

                echo '</tr>';
                
        }
    }
    echo '</tbody></table>';
}

function Afficher_Medecins($Id_essai, $Statut_medecin, $Id_entreprise){
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
                      
                        <button class="btn delete" onclick="Retirer_Medecin_Essai(' . htmlspecialchars($Id_essai) . ', ' . htmlspecialchars($medecin["Id_medecin"]) . ')">Retirer de l\'essai</button>
                    ';
                }
                if ($Statut_medecin == 'En attente'){
                    echo '
                        <button class="btn delete" onclick="Traiter_Candidature_Medecin('. htmlspecialchars($Id_entreprise).',' . htmlspecialchars($medecin["Id_medecin"]) . ', ' . htmlspecialchars('oui') . ')">Accepter le médecin</button>
                        <button class="btn delete" onclick="Traiter_Candidature_Medecin('. htmlspecialchars($Id_entreprise).',' . htmlspecialchars($medecin["Id_medecin"]) . ', ' . htmlspecialchars('non') . ')">Refuser le médecin</button>
                    ';
                }

                echo '</tr>';
                
        }
    }
    echo '</tbody></table>';
}

function A() {
    // Définir la logique pour la fonction
}

function Generer_Notif($code, $Id_essai, $Id_destinataire){}
?>  





