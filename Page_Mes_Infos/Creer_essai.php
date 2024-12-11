<?php
/*Création des essais */

session_start();
include("Connexion_base.php");
$role_user=$_SESSION['role'];
id_user=$_SESSION['id_user'];

$conn= Connexion_base();

    try {

        // Si l'utilisateur n'est pas une entreprise, ne pas créer l'essai
        if ($role_user !== 'Entreprise') {
            return "Vous devez être une entreprise pour créer un essai clinique.";
        }
        
        // Générer un Id_essai unique
        $stmt = $conn->query("SELECT MAX(Id_essai) AS max_id FROM essais_cliniques");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $newIdEssai = ($result['max_id'] ?? 0) + 1;

        //Verification de l'existence des données
        $titre = isset($_POST['Titre']) ? $_POST['Titre'] : null;
        $contexte = isset($_POST['Contexte']) ? $_POST['Contexte'] : null;

        // Récupérer les données du formulaire
        $titre = $_POST['Titre'];
        $contexte = $_POST['Contexte'];
        $objectif_essai = $_POST['Objectif_essai'];
        $design_etude = $_POST['Design_etude'];
        $critere_evaluation = $_POST['Critere_evaluation'];
        $resultats_attendus = $_POST['Resultats_attendus'];
        $nb_medecins = intval($_POST['Nb_medecins']);
        $nb_patients = intval($_POST['Nb_patients']);
        $date_creation = date('Y-m-d'); // Date actuelle
        $statut = "En attente";

        // Insertion des données dans la base de données
        $query = "
            INSERT INTO essais_cliniques (
                Id_essai, Titre, Contexte, Objectif_essai, Design_etude,
                Critere_evaluation, Resultats_attendus, Date_lancement, Date_fin, Date_creation, Statut,
                Id_entreprise, Nb_medecins, Nb_patients 
            ) VALUES (
                :Id_essai, :Titre, :Contexte, :Objectif_essai, :Design_etude,
                :Critere_evaluation, :Resultats_attendus, NULL, NULL, :Date_creation, :Statut,
                :Id_entreprise, :Nb_medecins, :Nb_patients
            )
        ";

        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':Id_essai' => $newIdEssai,
            ':Titre' => $titre,
            ':Contexte' => $contexte,
            ':Objectif_essai' => $objectif_essai,
            ':Design_etude' => $design_etude,
            ':Critere_evaluation' => $critere_evaluation,
            ':Resultats_attendus' => $resultats_attendus,
            ':Nb_medecins' => $nb_medecins,
            ':Nb_patients' => $nb_patients,
            ':Date_creation' => $date_creation,
            ':Statut' => $statut,
            ':Id_entreprise' => $id_user,
        ]);

        echo "L'essai clinique a été créé avec succès.";
    } catch (PDOException $e) {
        return "Erreur : " . $e->getMessage();
    }
    $conn= NULL;

?>
