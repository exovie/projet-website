<?php

function Err_connexion($numErr) {
    switch($numErr){
        case "Err1": 
            echo "Nous n'avons pas de compte associé à cette adresse email";
            break;
        case "Err2":
            echo "le mot de passe ou l'identifiant est incorrect";
            break;
        case "Err3":
            echo "Le compte n'a pas encore été validé";
            break;
        default:
            echo "Une erreur est survenue";
            break;
    }

}

function Connexion_base($db_name): PDO {
    $host = 'localhost';
    $user = 'root';
    $password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
    
}
return $pdo;
}

function Fermer_base(PDO &$conn): void {
    $conn = null;
}

function Get_id($db_name, string $table, string $column): array {
    // Connexion à la base de données
    $conn = Connexion_base($db_name);

    try {
        // Préparation de la requête SQL pour récupérer toutes les valeurs de la colonne spécifiée
        $sql = "SELECT $column FROM $table;";
        $stmt = $conn->prepare($sql);

        // Exécution de la requête
        $stmt->execute();

        // Récupérer toutes les valeurs de la colonne dans un tableau
        $values = $stmt->fetchAll(PDO::FETCH_COLUMN);

        return $values;
    } catch (PDOException $e) {
        // Gestion des erreurs de base de données
        echo "Erreur: " . $e->getMessage();
        return [];
    } finally {
    // Fermer la connexion
    Fermer_base($conn);
    }
}


function List_entreprise(string $db_name, int $id_entreprise): array {

    $conn = Connexion_base($db_name);

    try {
        $sql = "
    SELECT *
    FROM ENTREPRISES
    WHERE Id_entreprise = :Id_entreprise;
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':Id_entreprise', $id_entreprise, PDO::PARAM_INT);
    $stmt->execute();
        
    // Récupérer les résultats
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    $sql = "
    SELECT ESSAIS_CLINIQUES.Titre
    FROM ESSAIS_CLINIQUES
    JOIN ENTREPRISES ON ESSAIS_CLINIQUES.Id_entreprise = ENTREPRISES.Id_entreprise
    JOIN USERS ON ENTREPRISES.Id_entreprise = USERS.Id_user
    WHERE ENTREPRISES.Id_entreprise = :Id_entreprise;
    ";
            
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':Id_entreprise', $id_entreprise, PDO::PARAM_INT);
    $stmt->execute();

    $clinical_trials = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sql = "
    SELECT
        MEDECINS.Nom AS Nom_Medecin,
        MEDECINS.Profile_picture
    FROM 
        ESSAIS_CLINIQUES
    JOIN 
        ENTREPRISES ON ESSAIS_CLINIQUES.Id_entreprise = ENTREPRISES.Id_entreprise
    JOIN 
        USERS ON ENTREPRISES.Id_entreprise = USERS.Id_user
    JOIN 
        MEDECIN_ESSAIS ON ESSAIS_CLINIQUES.Id_essai = MEDECIN_ESSAIS.Id_essai
    JOIN 
        MEDECINS ON MEDECIN_ESSAIS.Id_medecin = MEDECINS.Id_medecin
    WHERE 
        ENTREPRISES.Id_entreprise = :Id_entreprise;
        ";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':Id_entreprise', $id_entreprise, PDO::PARAM_INT);
    $stmt->execute();

    $medecins = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($resultats as $entreprise) {
        echo '<div class = "entreprise">';
            echo '<h1>' . $entreprise['Nom_entreprise'] . '</h1>';
            echo '<p> Téléphones : '. $entreprise['Telephone']. '</p>';
            echo '<p> Siret : ' . $entreprise['Siret'] . '</p>';
            echo '<p class="clinical-trials"> Nombre d\'essais cliniques : ' . count($clinical_trials) . '</p>';
            echo '<p> Nos medecins partenaires : </p>';
            echo '<ul id="medecins">';
            $counter = 0;
            foreach ($medecins as $medecin) {
                if ($counter >= 5) break; // Arrête après 5 éléments
                if ($medecin['Profile_picture'] == null){
                    echo '<li> <img src="Pictures/defaultPicture.png" alt="pictureProfil" class="fixed_picture" style="cursor: pointer;"> </li>';
                } else {
                echo '<li>' . htmlspecialchars($medecin['Nom_Medecin']) . '</li>';
                }
                $counter++; // Incrémente après l'affichage
            }
            echo '</ul>';
        echo '</div>';
        
    }

    return [
        'entreprise' => $resultats,
        'clinical_trials' => $clinical_trials,
        'medecins' => $medecins
    ];

    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return [];
    
    } finally {
    // Fermer la connexion
    Fermer_base($conn);
    }

}

function List_essai($role, $db_name) {
    $conn = Connexion_base($db_name);
    $statuses = [
        'visiteur' => 'Recrutement',
        'patient' => 'Recrutement',
        'medecin' => 'En attente',
        'admin' => null,
        'entreprise' => null // Utilisation de null pour indiquer "tous les statuts"
    ];
    
    try {
        // Construction dynamique de la requête
        $sql = "
            SELECT 
                EC.*,
                CONCAT(M.Nom, ' ', M.Prenom) AS Nom_Medecin,
                E.Nom_entreprise AS Nom_Entreprise
            FROM 
                ESSAIS_CLINIQUES EC
            LEFT JOIN MEDECIN_ESSAIS ME ON EC.Id_essai = ME.Id_essai
            LEFT JOIN MEDECINS M ON ME.Id_medecin = M.Id_medecin
            LEFT JOIN ENTREPRISES E ON EC.Id_entreprise = E.Id_entreprise
        ";
    
        // Si le statut n'est pas "tous", on ajoute une clause WHERE
        if ($statuses[$role] !== null) {
            $sql .= "WHERE EC.Statut = :Statut ";
        }
    
        $sql .= "ORDER BY Date_creation ASC;";
    
        $stmt = $conn->prepare($sql);
    
        // Lier la variable seulement si le statut est défini
        if ($statuses[$role] !== null) {
            $stmt->bindParam(':Statut', $statuses[$role], PDO::PARAM_STR);
        }
        
        $stmt->execute();
            
        // Récupérer les résultats
        $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($resultats as $essai) {
            echo '<ul class = "trials">';
            echo '<li class = "trial_title">' . htmlspecialchars($essai['Titre']) . '</li>';
            echo '<li class = "trial_company">' . htmlspecialchars($essai['Nom_Entreprise']) . '</li>';
            echo '<li>' . htmlspecialchars($essai['Objectif_essai']) . '</li>';
           
            // Vérifier si un médecin est associé à cet essai
            if (!empty($essai['Nom_Medecin'])) {
                echo '<li><strong>Médecins associés :</strong> ' . htmlspecialchars($essai['Nom_Medecin']) . '</li>';
            } else {
                echo '<li><strong>Médecin associé :</strong> Aucun médecin assigné</li>';
            }
            echo '</ul>';
        }

        return [
            'essai clinique' => $resultats,
        ];

        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
            return [];
        
        } finally {
        // Fermer la connexion
        Fermer_base($conn);
        }

}

function List_Medecin(string $db_name, int $id_medecin): array {
    $conn = Connexion_base($db_name);

    try {
        $sql = "
    SELECT *
    FROM ENTREPRISES
    WHERE Id_entreprise = :Id_entreprise
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':Id_entreprise', $id_medecin, PDO::PARAM_INT);
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
    return $resultats;
}

function Valider_inscription(string $servername, $db_name) {
    $conn = Connexion_base($db_name);

    try {
        $sql = "
    SELECT *
    FROM ENTREPRISES
    WHERE Id_entreprise = :Id_entreprise;
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':Id_entreprise', $id_entreprise, PDO::PARAM_INT);
    $stmt->execute();
        
    // Récupérer les résultats
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    $sql = "
    SELECT ESSAIS_CLINIQUES.Titre, ESSAIS_CLINIQUES.Contexte, ESSAIS_CLINIQUES.Objectif_essai, 
    ESSAIS_CLINIQUES.Design_etude, ESSAIS_CLINIQUES.Critere_evaluation, 
    ESSAIS_CLINIQUES.Resultats_attendus, ESSAIS_CLINIQUES.Date_lancement, 
    ESSAIS_CLINIQUES.Date_fin, ESSAIS_CLINIQUES.Date_creation, ESSAIS_CLINIQUES.Statut
    FROM ESSAIS_CLINIQUES
    JOIN ENTREPRISES ON ESSAIS_CLINIQUES.Id_entreprise = ENTREPRISES.Id_entreprise
    JOIN USERS ON ENTREPRISES.Id_entreprise = USERS.Id_user
    WHERE ENTREPRISES.Id_entreprise = :Id_entreprise;
    ";
            
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':Id_entreprise', $id_entreprise, PDO::PARAM_INT);
    $stmt->execute();

    $clinical_trials = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'entreprise' => $resultats,
        'clinical_trials' => $clinical_trials
    ];

    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return [];
    
    } finally {
    // Fermer la connexion
    Fermer_base($conn);
    }
}
