<?php
/*Fonction qui calcule et génère les statistiques d’un essai lorsque que la phase de recrutement est finie*/



function Stat_data($conn, int $id_essai){
    try {
        // Requête pour récupérer les poids et les sexes des patients
        $query = $conn->prepare("
            SELECT Poids, Sexe, Taille, Date_naissance
            FROM patients 
            JOIN patients_essais ON patients.Id_patient = patients_essais.Id_patient
            WHERE patients_essais.Id_essai = :id_essai
        ");

        $query->bindParam(':id_essai', $id_essai, PDO::PARAM_INT);
        $query->execute();

        // Récupérer les résultats
        $patients = $query->fetchAll(PDO::FETCH_ASSOC);
        return $patients;
    }
        catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
            die();
        }

}

function Stat_Poids($data, $conn, int $id_essai){

    try{
        $poids = array_column($data, 'Poids');

        // Calcul du poids maximum arrondi au multiple de 5
        $poids_max = ceil(max($poids) / 5) * 5;

        // Générer les intervalles de 5kg sur l'histogramme
        $tranches = [];
        $intervalle = 5;
        for ($i = 40; $i < $poids_max; $i += $intervalle) {
            $tranches["$i - " . ($i + $intervalle)] = 0;
         }

        // Regrouper les poids dans les tranches
        foreach ($poids as $p) {
            foreach ($tranches as $range => &$count) {
                [$min, $max] = explode(' - ', $range);
                if ($p >= $min && $p < $max) {
                    $count++;
                    break;
                }
            }}
        
        //Return et Conversion en JSON 
        return [
            'poids' => $poids,
            'tranches_poids_labels' => json_encode(array_keys($tranches)),
            'tranches_poids_counts' => json_encode(array_values($tranches))
        ];

    }catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        die();
    }
}

function Stat_Taille($data, $conn, int $id_essai){

    try{
        $tailles = array_column($data, 'Taille');
        // Calcul du minimum et du maximum des tailles, ajustés aux multiples de 5
        $taille_min = floor(min($tailles) / 5) * 5; // Plus petit multiple de 5 inférieur
        $taille_max = ceil(max($tailles) / 5) * 5;  // Plus grand multiple de 5 supérieur

        // Générer les tranches (catégories)
        $tranches_taille = [];
        $intervalle = 5;
        for ($i = $taille_min; $i < $taille_max; $i += $intervalle) {
            $tranches_taille["$i - " . ($i + $intervalle)] = 0;
        }
    
        // Regrouper les tailles dans les tranches
        foreach ($tailles as $t) {
            foreach ($tranches_taille as $range => &$count) {
                [$min, $max] = explode(' - ', $range);
                if ($t >= $min && $t < $max) {
                    $count++;
                    break;
                }
            }
        }
    
        //Conversion en JSON
        return [
            'taille' => $tailles,
            'tranches_taille_labels' => json_encode(array_keys($tranches_taille)),
            'tranches_taille_counts' => json_encode(array_values($tranches_taille))
        ];

    }catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        die();
    }

}


function Stat_Age($data, $conn, int $id_essai){

    try{
        $dates_naissance = array_column($data, 'Date_naissance'); // Attribut 'Date_naissance'

        // Calculer l'âge actuel de chaque patient
        $ages = [];
        foreach ($dates_naissance as $date_naissance) {
            $birthDate = new DateTime($date_naissance);
            $today = new DateTime();
            $age = $today->diff($birthDate)->y; // Calcul de l'âge en années
            $ages[] = $age;
        }

        // Calcul du minimum et du maximum des âges, ajustés aux multiples de 5
        $age_min = floor(min($ages) / 5) * 5; // Plus petit multiple de 5 inférieur
        $age_max = ceil(max($ages) / 5) * 5;  // Plus grand multiple de 5 supérieur

        // Générer les tranches (catégories)
        $tranches_age = [];
        $intervalle = 5;
        for ($i = $age_min; $i < $age_max; $i += $intervalle) {
            $tranches_age["$i - " . ($i + $intervalle)] = 0;
        }

        // Regrouper les âges dans les tranches
        foreach ($ages as $age) {
            foreach ($tranches_age as $range => &$count) {
                [$min, $max] = explode(' - ', $range);
                if ($age >= $min && $age < $max) {
                    $count++;
                    break;
                }
            }
        }
        //Conversion en JSON
        return [
            'age' => $ages,
            'tranches_age_labels' => json_encode(array_keys($tranches_age)),
            'tranches_age_counts' => json_encode(array_values($tranches_age))
        ];

    }catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        die();
    }
}

function Stat_Sexe($data, $conn, int $id_essai){

    try{
        $sexes = array_column($data, 'Sexe');

        // Préparer les données de sexe pour le camembert
        $sexe_count = array_count_values($sexes); // Exemple : ['H' => 5, 'F' => 3]
  
        return [
            'sexe' => $sexes,
            'sexe_json' => json_encode($sexe_count),
        ];
    }catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        die();
    }
}
?>

