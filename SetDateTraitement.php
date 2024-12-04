<?php
/*Fonction qui attribue une date à la variable $Date_lancement
lorsque le nombre de patients requis est atteint*/

include(Connexion_base.php);
$conn=Connection_base();

//Récupérartion de l'ID essai
$id_essai = $_GET['id_essai'];

function SetDatePublication ($conn, int $id_essai){

    try {
        $query= $conn -> prepare("
        SELECT Nb_patients, Date_lancement
        FROM essais_cliniques 
        WHERE Id_essai = :id_essai
        ");
        $query->bindParam(':id_essai', $id_essai, PDO::PARAM_INT);
        $query->execute();

        //Récupération des résultats
        $essai=$query->fetch(PDO::FETCH_ASSOC);

        $nb_patients_requis = $essai['Nb_patients'];
        $date_lancement = $essai['Date_lancement'];

        // Vérifier si une date a déjà été attribuée
        if ($date_lancement !== null) {
            echo "L'essai ID $id_essai possède déjà une date de lancement attribuée.";
        }

        else{
            // Compter le nombre de médecins actifs inscrits à l'essai
        $query = $conn->prepare("
        SELECT COUNT(*) AS nb_patients_actifs
        FROM patients_essais
        WHERE Id_essai = :id_essai AND Statut_participation = 'Actif'
    ");

    $query->execute(['id_essai' => $id_essai]);
    $result = $query->fetch(PDO::FETCH_ASSOC);
    $nb_patients_actifs = $result['nb_patients_actifs'];

    // Vérifier si le nombre de patients actifs est suffisant
    if ($nb_patients_actifs >= $nb_patients_requis) {
        // Si le nb est suffisant, on met la date d'aujourd'hui
        $query = $conn->prepare("
            UPDATE essais_cliniques
            SET Date_lancement = :date_lancement
            WHERE Id_essai = :id_essai
        ");
        $query->execute([
            'date_creation' => date('Y-m-d'),
            'id_essai' => $id_essai
        ]);
        echo "Date attribuée avec succès pour l'essai ID $id_essai.";
        //+ Il faut générer une notif
    } else {
        echo "Nombre insuffisant de patients actifs pour l'essai ID $id_essai. 
                Actifs: $nb_patients_actifs / Requis: $nb_patients_requis.";
    }
        }
        
    } catch (PDOException $e) {
        echo "Erreur lors de l'attribution de la date : " . $e->getMessage();
    }

}




?>