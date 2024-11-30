<?php
session_start();
$servername = $_SESSION["servername"];



function List_entreprise(string $servername, int $id_entreprise) {

    $conn = Connexion_base();

    try {
        $sql = "
        SELECT E.* 
        FROM ENTREPRISES E
        JOIN FONCTIONS F ON E.Id_entreprise = F.Id_entreprise
        WHERE F.Id_Fonction = :Id_Fonction
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':Id_Fonction', $Id_Fonction, PDO::PARAM_INT);
    $stmt->execute();
    
    // Récupérer les résultats
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return [];
    
    } finally {
    // Fermer la connexion
    Fermer_base($conn);
    }
}


function Valider_inscription(string $servername) {
    try {
        $bdd = new PDO($servername, 'root', '');
        echo 'connexion réussie';
        } 
        catch (Exception $e) {
            echo 'connexion échouée';
            die ('Erreur : ' . $e->getMessage () );
        }
}




//Fonctions  liées aux essais
//méthode entreprise
function Demander_Medecin_essai(int $Id_essai, int $Id_medecin){
    $conn = Connexion_base();
    try {
    $requete = $conn -> prepare("INSERT INTO MEDECIN_ESSAIS(`Id_medecin`, `Id_essai`, `Statut_medecin`) VALUES (?,?,'Sollicite')");
    $requete -> execute(array($Id_medecin, $Id_essai));
    echo "Nouveau médecin sollicité avec succès pour cet essai!";
    Fermer_base();
    } catch (PDOException $e) {
    echo "Erreur bdd: " . $e->getMessage(); }
    try{
        Generer_notif(3,$Id_essai, $Id_medecin);
    }
     catch (PDOException $e) {
    echo "Erreur notification: " . $e->getMessage(); }

}


//méthode médecin
function Postuler_Medecin_Essai(int $Id_essai, int $Id_medecin){
    $conn = Connexion_base();
    try{
        $requete = $conn -> prepare("INSERT INTO MEDECIN_ESSAIS(`Id_medecin`, `Id_essai`, `Statut_medecin`) VALUES (?,?,'En attente')");
        $requete -> execute(array($Id_medecin, $Id_essai))
        echo "Demande de participation enregistrée avec succès pour cet essai!";
        Fermer_base();
        } catch (PDOException $e) {
        echo "Erreur bdd: " . $e->getMessage(); }
        try{
            $conn = Connexion_base();
            $requete_bis = $conn -> prepare("SELECT `Id_entreprise` FROM `ESSAIS_CLINIQUES` WHERE `Id_essai`=?");
            $requete_bis->execute(array($Id_essai));
            $tableau = $requete_bis->fetch();
            $Id_entreprise = $tableau["Id_entreprise"];
            Generer_notif(5,$Id_essai, $Id_entreprise);
            Fermer_base();
        }
         catch (PDOException $e) {
        echo "Erreur notification: " . $e->getMessage(); }

    }

//méthode enterprise
function Retirer_Medecin_Essai(int $Id_essai, int $Id_medecin){
    $conn = Connexion_base();
    try{
        $requete = $conn -> prepare("UPDATE `MEDECIN_ESSAIS` SET `Statut_medecin` = 'Retire' WHERE (`Id_essai` = ? AND `Id_medecin` = ?");
        $requete -> execute(array($Id_essai, $Id_medecin));
        echo "Medecin retiré de l'essai avec succès!";
        Fermer_base();
    }
 catch (PDOException $e) {
    echo "Erreur bdd: " . $e->getMessage(); 
}
    try{
        Generer_notif(18, $Id_essai, $$Id_medecin);
    }
    catch (PDOException $e) {
        echo "Erreur notif: " . $e->getMessage(); 
    }
    Verif_nbMedecin_Essai($Id_essai);
}

//méthode entreprise/admin catch (PDOException $e) {
      
function Suspendre_Essai(int $Id_essai){
    $conn = Connexion_base();
    try{
        $requete = $conn -> prepare("UPDATE `ESSAIS_CLINIQUES` SET `Statut` = 'Retire' WHERE `Id_essai` = ?");
        $requete -> execute(array($Id_essai));
        echo "Essai mis en pause avec succès!";
        Fermer_base();
        $requete_bis = $conn -> prepare("UPDATE `MEDECIN_ESSAIS` SET `Statut_medecin` = '' WHERE (`Id_essai` = ? AND `Id_medecin` = ?"); //a-t-on vraiment besoin d'un statut en pause pour les médecins? dans ce cas là, le rajouter dans la ligne et la bdd
        $requete_bis -> execute(array($Id_essai, $Id_medecin));
        echo "Medecin mis en pause de l'essai  succès!";
        Fermer_base();
    }
    catch (PDOException $e) {
        echo "Erreur bdd: " . $e->getMessage(); 
    }
    try{
        $conn = Connexion_base();
        $requete_bis = $conn -> prepare("SELECT `Id_entreprise` FROM `ESSAIS_CLINIQUES` WHERE `Id_essai`=?");
        $requete_bis->execute(array($Id_essai));
        $tableau = $requete_bis->fetch();
        $Id_entreprise = $tableau["Id_entreprise"];
        Generer_notif(17, $Id_entreprise);
        $conn = Connexion_base();
        $requete_ter = $conn -> prepare("SELECT `Id_medecin` FROM `MEDECIN_ESSAIS` WHERE `Id_essai`=?");
        $requete_ter->execute(array($Id_essai));
        $tableau = $requete_ter->fetch();
        $Liste_medecin = $tableau["Id_medecin"];
        foreach ($Liste_medecin as $Id_medecin) {
            Generer_notif(17, $Id_medecin);
        }
        Fermer_base();
        echo "Notifs envoyées avec succès";
    }
    catch (PDOException $e) {
        echo "Erreur notif: " . $e->getMessage(); 
    }

   
}

function Verif_nbMedecin_Essai($Id_essai){

}
//Méthode patient
function Postuler_Patient_Essai($Id_essai, $Id_patient){
    $conn = Connexion_base();
    try{
        $requete = $conn -> prepare("INSERT INTO `PATIENTS_ESSAIS(`Id_patient`, `Id_essai`, `Statut_participation`) VALUES (?,?,'00/00/0000','En attente')");  //quand met-on la date?
        $requete -> execute(array($Id_medecin, $Id_essai))
        echo "Demande de participation enregistrée avec succès pour cet essai!";
        Fermer_base();
        } catch (PDOException $e) {
        echo "Erreur bdd: " . $e->getMessage(); }
    
    try{
        $conn = Connexion_base();
        $requete_bis = $conn -> prepare("SELECT `Id_medecin` FROM `MEDECIN_ESSAIS` WHERE `Id_essai`=?");
        $requete_bis->execute(array($Id_essai));
        $tableau = $requete_bis->fetch();
        $Liste_medecin = $tableau["Id_medecin"];
        foreach ($Liste_medecin as $Id_medecin) {
        Generer_notif(10, $Id_essai,$Id_medecin);}
        Fermer_base();
    }
    catch (PDOException $e) {
        echo "Erreur notif: " . $e->getMessage(); }
    }

//Méthode patient, médecin

function Retirer_Patient_Essai($Id_essai, $Id_patient){
    $conn = Connexion_base();
    try{
        $requete = $conn -> prepare("UPDATE `PATIENTS_ESSAIS` SET `Statut` = 'Abandon' WHERE (`Id_essai` = ? AND `Id_patient` = ?");
        $requete -> execute(array($Id_essai, $Id_patient));
        Fermer_base();
        echo "Patient retiré avec succès de l'essai";
    }
    catch (PDOException $e) {
        echo "Erreur bdd: " . $e->getMessage(); }
    try{
        $conn = Connexion_base();
        Generer_notif(12,$Id_essai, $Id_patient); //on ne sait pas qui a pris la décision donc notif pas ultra adaptée (on prévient le patient qu'il s'est bien retiré en gros)
        $requete_bis = $conn -> prepare("SELECT `Id_medecin` FROM `MEDECIN_ESSAIS` WHERE `Id_essai`=?");
        $requete_bis->execute(array($Id_essai));
        $tableau = $requete_bis->fetch();
        $Liste_medecin = $tableau["Id_medecin"];
        foreach ($Liste_medecin as $Id_medecin) {
        Generer_notif(12, $Id_essai,$Id_medecin);} //on prévient tous les médecins de l'essai
        Fermer_base();
    }
    catch (PDOException $e) {
        echo "Erreur notif: " . $e->getMessage(); }
    }
    



?>  



