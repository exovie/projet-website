<?php
/*Fonction qui attribue une date à la variable $Date_publication 
lorsque le nombre de médecins requis est atteint*/

include(Connexion_base.php);
$conn=Connection_base();

//Récupérartion de l'ID essai
$id_essai = $_GET['id_essai'];

function SetDatePublication ($conn, int $id_essai){

    try {

        $query= $conn -> prepare("
        SELECT Nb_medecins, Date_publication 
        FROM essais_cliniques 
        WHERE Id_essai = :id_essai
        ");
        $query->bindParam(':id_essai', $id_essai, PDO::PARAM_INT);
        $query->execute();

        //Récupération des résultats
        $essai=$query->fetch(PDO::FETCH_ASSOC);

        $nb_medecins_requis = $essai['Nb_medecins'];
        $date_publication = $essai['Date_publication'];

        // Vérifier si une date a déjà été attribuée
        if ($date_publication !== null) {
            return "L'essai ID $id_essai possède déjà une date de publication attribuée.";
        }

        else{
            // Compter le nombre de médecins actifs inscrits à l'essai
        $query = $conn->prepare("
        SELECT COUNT(*) AS nb_medecins_actifs
        FROM medecins_essais
        WHERE Id_essai = :id_essai AND Statut_medecin = 'Actif'
    ");

    $query->execute(['id_essai' => $id_essai]);
    $result = $query->fetch(PDO::FETCH_ASSOC);
    $nb_medecins_actifs = $result['nb_medecins_actifs'];

    // Vérifier si le nombre de médecins actifs est suffisant
    if ($nb_medecins_actifs >= $nb_medecins_requis) {
        // Si le nb est suffisant, on met la date d'aujourd'hui
        $query = $conn->prepare("
            UPDATE essais_cliniques
            SET Date_publication = :date_publication
            WHERE Id_essai = :id_essai
        ");
        $query->execute([
            'date_creation' => date('Y-m-d'),
            'id_essai' => $id_essai
        ]);
        return "Date attribuée avec succès pour l'essai ID $id_essai.";
        //+ Il faut générer une notif
    } else {
        return "Nombre insuffisant de médecins actifs pour l'essai ID $id_essai. 
                Actifs: $nb_medecins_actifs / Requis: $nb_medecins_requis.";
    }
        }
        
    } catch (PDOException $e) {
        return "Erreur lors de l'attribution de la date : " . $e->getMessage();
    }

    
}




?>