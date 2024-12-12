<?php
/*Fichier php qui regroupe les fonctions utiliser pour modifier un essai déjà créer*/


include("Connexion_base.php");
$conn = Connexion_base();

// Fonction pour récupérer les informations de l'essai
function getEssaiInfo($conn, int $id_essai) {

    //Récupérer les informations de l'essai
    $sql = $conn -> prepare("SELECT Titre, Contexte, Objectif_essai,  Design_etude, Critere_evaluation, Resultats_attendus, 
    Date_creation, Date_lancement,Statut, Nb_medecins, Nb_patients
    FROM ESSAIS_CLINIQUES 
    WHERE Id_essai=:id_essai");
    $sql->execute(['id_essai' => $id_essai]);
    $result=$sql->fetch(PDO::FETCH_ASSOC);
    return $result;
}


// Fonction pour mettre à jour les informations de l'essai
function updateEssaiInfo($conn, int $Id_essai) {
    try {
        // Récupérer les données non modifiables
        $sql = $conn->prepare("SELECT Date_creation, Date_lancement, Statut FROM ESSAIS_CLINIQUES WHERE Id_essai = :id_essai");
        $sql->execute(['id_essai' => $Id_essai]);
        $dataEssai = $sql->fetch(PDO::FETCH_ASSOC);

        if (!$dataEssai) {
            echo "Essai introuvable.";
            return false;
        }

        // Construire les données à mettre à jour
        $query = "UPDATE ESSAIS_CLINIQUES 
                  SET Titre = :Titre, Contexte = :Contexte, Objectif_essai = :Objectif_essai, 
                      Design_etude = :Design_etude, Critere_evaluation = :Critere_evaluation, 
                      Resultats_attendus = :Resultats_attendus, Nb_medecins = :Nb_medecins, Nb_patients = :Nb_patients
                  WHERE Id_essai = :Id_essai";

        $update = $conn->prepare($query);

        // Nettoyer et valider les données transmises via POST
        $data = [
            'Titre' => $_POST['Titre'] ?? '',
            'Contexte' => $_POST['Contexte'] ?? '',
            'Objectif_essai' => $_POST['Objectif_essai'] ?? '',
            'Design_etude' => $_POST['Design_etude'] ?? '',
            'Critere_evaluation' => $_POST['Critere_evaluation'] ?? '',
            'Resultats_attendus' => $_POST['Resultats_attendus'] ?? '',
            'Nb_medecins' => $_POST['Nb_medecins'] ?? 0,
            'Nb_patients' => $_POST['Nb_patients'] ?? 0,
            'Id_essai' => $Id_essai, // Clé primaire pour la condition WHERE
        ];

        // Exécuter la requête
        $result = $update->execute($data);

        if ($result) {
            return true; //Cas où la MAJ a bien été faite
        } else {
            return false; //Cas où la MAJ n'a pas fonctionner
        }

    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return false;
    }
}




?>