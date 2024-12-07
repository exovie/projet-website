<?php

include 'Fonctions.php';

function Valider_inscription($id_user) {
    $conn = Connexion_base();
        try {
            // Vérifier si l'utilisateur est dans la table MEDECINS
            $queryMedecin = "SELECT Id_medecin FROM MEDECINS WHERE Id_medecin = ?";
            $stmtMedecin = $conn->prepare($queryMedecin);
            $stmtMedecin->execute([$id_user]);
    
            if ($stmtMedecin->rowCount() > 0) {
                // Si l'utilisateur est un médecin, mettre à jour le statut
                $updateQuery = "UPDATE MEDECINS SET Verif_inscription = 1 WHERE Id_medecin = ?";
                $stmtUpdate = $conn->prepare($updateQuery);
                $stmtUpdate->execute([$id_user]);
                return "Statut_inscription mis à jour pour le médecin avec l'ID $id_user.";
            }
    
            // Vérifier si l'utilisateur est dans la table ENTREPRISES
            $queryEntreprise = "SELECT Id_entreprise FROM ENTREPRISES WHERE Id_entreprise = ?";
            $stmtEntreprise = $conn->prepare($queryEntreprise);
            $stmtEntreprise->execute([$id_user]);
    
            if ($stmtEntreprise->rowCount() > 0) {
                // Si l'utilisateur est une entreprise, mettre à jour le statut
                $updateQuery = "UPDATE ENTREPRISES SET Verif_inscription = 'True' WHERE Id_entreprise = ?";
                $stmtUpdate = $conn->prepare($updateQuery);
                $stmtUpdate->execute([$id_user]);
                return "Statut_inscription mis à jour pour l'entreprise avec l'ID $id_user.";
            }
    
            // Si l'utilisateur n'est trouvé dans aucune des deux tables
            return "L'utilisateur avec l'ID $id_user n'est ni un médecin ni une entreprise.";
        } catch (PDOException $e) {
            return "Erreur : " . $e->getMessage();
        }
     finally {
    // Fermer la connexion
    Fermer_base($conn);
    }
}

function refus_inscription($id_user) {
    $conn = Connexion_base();
    try {
        // Vérifier si l'utilisateur est dans la table MEDECINS
        $queryMedecin = "SELECT Id_medecin FROM MEDECINS WHERE Id_medecin = ?";
        $stmtMedecin = $conn->prepare($queryMedecin);
        $stmtMedecin->execute([$id_user]);

        if ($stmtMedecin->rowCount() > 0) {
            // Si l'utilisateur est un médecin, supprimer l'enregistrement
            $deleteQuery = "DELETE FROM MEDECINS WHERE Id_medecin = ?";
            $stmtDelete = $conn->prepare($deleteQuery);
            $stmtDelete->execute([$id_user]);
            return "L'utilisateur médecin avec l'ID $id_user a été supprimé.";
        }

        // Vérifier si l'utilisateur est dans la table ENTREPRISES
        $queryEntreprise = "SELECT Id_entreprise FROM ENTREPRISES WHERE Id_entreprise = ?";
        $stmtEntreprise = $conn->prepare($queryEntreprise);
        $stmtEntreprise->execute([$id_user]);

        if ($stmtEntreprise->rowCount() > 0) {
            // Si l'utilisateur est une entreprise, supprimer l'enregistrement
            $deleteQuery = "DELETE FROM ENTREPRISES WHERE Id_entreprise = ?";
            $stmtDelete = $conn->prepare($deleteQuery);
            $stmtDelete->execute([$id_user]);
            return "L'utilisateur entreprise avec l'ID $id_user a été supprimé.";
        }

        // Si l'utilisateur n'est trouvé dans aucune des deux tables
        return "L'utilisateur avec l'ID $id_user n'est ni un médecin ni une entreprise.";
    } catch (PDOException $e) {
        return "Erreur : " . $e->getMessage();
    } finally {
        // Fermer la connexion
        Fermer_base($conn);
    }
}

function display_users() {
    $medecins = Get_id('MEDECINS', 'Id_medecin');
    $entreprises = Get_id('ENTREPRISES', 'Id_entreprise');
    $patients = Get_id('PATIENTS', 'Id_patient');
}