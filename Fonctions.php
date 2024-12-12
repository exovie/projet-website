<?php
$db_name = "mysql:host=localhost;dbname=website_db"; 
$_SESSION['db_name'] = $db_name;


function Connexion_base(): PDO {
    $host = 'localhost';
    $user = 'root';
    $password = '';
    $db_name = $_SESSION['db_name'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
    
}
return $pdo;
}


function Fermer_base($conn): void {
    $conn = null;
}

function Get_id(string $table, string $column): array {
    // Connexion à la base de données
    $conn = Connexion_base();

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


function Get_entreprise_data(int $id_entreprise): array {

    $conn = Connexion_base();

    try {
        // Récupérer les informations de l'entreprise
        $sql = "
            SELECT *
            FROM ENTREPRISES
            WHERE Id_entreprise = :Id_entreprise AND
            Verif_inscription = 1;
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':Id_entreprise', $id_entreprise, PDO::PARAM_INT);
        $stmt->execute();
        $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Récupérer les essais cliniques
        $sql = "
            SELECT ESSAIS_CLINIQUES.Titre
            FROM ESSAIS_CLINIQUES
            JOIN ENTREPRISES ON ESSAIS_CLINIQUES.Id_entreprise = ENTREPRISES.Id_entreprise
            WHERE ENTREPRISES.Id_entreprise = :Id_entreprise AND
            ESSAIS_CLINIQUES.Statut IN ('Termine', 'En cours', 'fini');
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':Id_entreprise', $id_entreprise, PDO::PARAM_INT);
        $stmt->execute();
        $clinical_trials = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Récupérer les médecins partenaires
        $sql = "
            SELECT
                MEDECINS.Nom AS Nom_Medecin,
                MEDECINS.Profile_picture
            FROM 
                ESSAIS_CLINIQUES
            JOIN 
                ENTREPRISES ON ESSAIS_CLINIQUES.Id_entreprise = ENTREPRISES.Id_entreprise
            JOIN 
                MEDECIN_ESSAIS ON ESSAIS_CLINIQUES.Id_essai = MEDECIN_ESSAIS.Id_essai
            JOIN 
                MEDECINS ON MEDECIN_ESSAIS.Id_medecin = MEDECINS.Id_medecin
            WHERE 
                ENTREPRISES.Id_entreprise = :Id_entreprise AND
                MEDECIN_ESSAIS.Statut_medecin IN ('Actif', 'Termine');
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':Id_entreprise', $id_entreprise, PDO::PARAM_INT);
        $stmt->execute();
        $medecins = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'entreprise' => $resultats,
            'clinical_trials' => $clinical_trials,
            'medecins' => $medecins
        ];

    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return [];
    } finally {
        Fermer_base($conn);
    }
}

function Display_entreprise_data(array $data) {
    if (empty($data['entreprise'])) {
        echo '<p>Aucune entreprise trouvée.</p>';
        return;
    }

    foreach ($data['entreprise'] as $entreprise) {
        echo '<div class="entreprise">';
        echo '<div class="image_nom">';
        if ($entreprise['Profile_picture'] == null){
            echo '<li> <img src="Pictures/defaultPicture.png" alt="pictureProfil" class="fixed_picture" style="cursor: pointer;"> </li>';
        } else {
            $profilePictureData = base64_encode($entreprise['Profile_picture']);
            echo '<li><img src="data:image/png;base64,' . $profilePictureData . '" alt="Photo de profil" class="fixed_picture" style="cursor: pointer;"></li>';
        }
        echo '<h1>' . htmlspecialchars($entreprise['Nom_entreprise']) . '</h1>';
        echo '</div>';
        echo '<p>Téléphones : ' . htmlspecialchars($entreprise['Telephone']) . '</p>';
        echo '<p>Siret : ' . htmlspecialchars($entreprise['Siret']) . '</p>';
        echo '<p class="clinical-trials">Nombre d\'essais cliniques : ' . count($data['clinical_trials']) . '</p>';
        echo '<p>Nos médecins partenaires :</p>';
        echo '<ul id="medecins">';
        
        $counter = 0;
        foreach ($data['medecins'] as $medecin) {
            if ($counter >= 5) break; // Limite à 5 médecins
            if (empty($medecin['Profile_picture'])) {
                echo '<li><img src="Pictures/defaultPicture.png" alt="profile picture" class="fixed_picture" style="cursor: pointer;"></li>';
            } else {
                echo '<li>' . htmlspecialchars($medecin['Nom_Medecin']) . '</li>';
            }
            $counter++;
        }
        
        echo '</ul>';
        echo '</div>';
    }
}


function Get_essais($role) {
    $conn = Connexion_base();
    $statuses = [
        'visiteur' => 'Recrutement',
        'patient' => 'Recrutement',
        'medecin' => 'En attente',
        'admin' => null,
        'entreprise' => null // Utilisation de null pour indiquer "tous les statuts"
    ];
    
    if (!isset($statuses[$role])) {
        return [];
    }

    try {
        // Construction dynamique de la requête
        $sql = "
            SELECT 
                EC.*,
                GROUP_CONCAT(DISTINCT CONCAT(M.Nom, ' ', M.Prenom) SEPARATOR ', ') AS Nom_Medecin,
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
    
        $sql .= "GROUP BY EC.Id_essai
                ORDER BY Date_creation ASC;";
    
        $stmt = $conn->prepare($sql);
    
        // Lier la variable seulement si le statut est défini
        if ($statuses[$role] !== null) {
            $stmt->bindParam(':Statut', $statuses[$role], PDO::PARAM_STR);
        }
        
        $stmt->execute();
            
        // Récupérer les résultats
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return [];
    
    } finally {
        // Fermer la connexion
        Fermer_base($conn);
    }

}

function Display_essais($resultats) {
    foreach ($resultats as $essai) {
        echo '<button name="essai_indi" value="' . htmlspecialchars($essai['Id_essai']) . '" type="submit" class="search-button">';
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
        echo '</ul> </button>';
    }
}

function List_Medecin($id_medecin): array {
    if (!is_int($id_medecin)) {
        return [];
    }
    $conn = Connexion_base();

    try { 
        $sql = "
    SELECT *
    FROM MEDECINS
    WHERE Id_medecin = :id_medecin;
    SELECT *
    FROM MEDECINS
    WHERE Id_medecin = :id_medecin;
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_medecin', $id_medecin, PDO::PARAM_INT);
    $stmt->bindParam(':id_medecin', $id_medecin, PDO::PARAM_INT);
    $stmt->execute();
    
    // Récupérer les résultats
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return [];
    
    } finally {
    // Fermer la connexion
    Fermer_base($conn);
    }
}

function display_medecin($medecin) {
    $medecin = $medecin[0];
    echo '<ul class = "medecins">';
    echo '<div class = "image_nom">';
    if ($medecin['Profile_picture'] == null){
        echo '<li> <img src="Pictures/defaultPicture.png" alt="pictureProfil" class="fixed_picture" style="cursor: pointer;"> </li>';
    } else {
        $profilePictureData = base64_encode($medecin['Profile_picture']);
        echo '<li><img src="data:image/png;base64,' . $profilePictureData . '" alt="Photo de profil" class="fixed_picture" style="cursor: pointer;"></li>';
    }
    echo '<li class = "noms_medecins">' . htmlspecialchars($medecin['Nom']) . " " .htmlspecialchars($medecin['Prenom']). '</li>';
    echo '</div>';
    echo '<li class = "specialite">' . htmlspecialchars($medecin['Specialite']) . '</li>';
    echo '</ul>';
}


function recherche_EC($liste_EC, $recherche, $filtres) { 
    // Remove comments from the input
    $recherche = preg_replace('/\/\*.*?\*\//s', '', $recherche); // Remove block comments
    $recherche = preg_replace('/\/\/.*?(\r?\n|$)/', '', $recherche); // Remove line comments
    // Vérification du titre (recherche)
    $recherche = mb_strtolower($recherche, 'UTF-8');
    $recherche = convertirAccent($recherche);
    $pattern = "/$recherche/";

    // Filtrer selon les critères des filtres
    $phaseFilter = $filtres[0];
    $companyFilter = $filtres[1];

    $results = [];
    
    try {
        foreach ($liste_EC as $EC) { 
            $title = mb_strtolower($EC['Titre'], 'UTF-8');
            $title = convertirAccent($title);
            if (!preg_match($pattern, $title)) {
                continue; // Passer au prochain essai si le titre ne correspond pas
            }

            // Filtre de phase
            if ($phaseFilter !== 'Tous' && substr($EC['Titre'], -strlen($phaseFilter)) !== $phaseFilter) {
                continue;
            }

            // Filtre de l'entreprise
            if ($companyFilter !== 'Tous' && $EC['Nom_Entreprise'] !== $companyFilter) {
                continue;
            }

            // Si tous les filtres sont passés, ajouter l'essai à la liste des résultats
            $results[] = $EC;
        }

        // Affichage des résultats
        if (!empty($results)) {
            foreach ($results as $essai) {
                echo '<button name="essai_indi" value="' . htmlspecialchars($essai['Id_essai']) . '" type="submit" class="button">';
                echo '<ul class="trials">';
                echo '<li class="trial_title">' . htmlspecialchars($essai['Titre']) . '</li>';
                echo '<li class="trial_company">' . htmlspecialchars($essai['Nom_Entreprise']) . '</li>';
                echo '<li>' . htmlspecialchars($essai['Objectif_essai']) . '</li>';

                // Vérifier si un médecin est associé à cet essai
                if (!empty($essai['Nom_Medecin'])) {
                    echo '<li><strong>Médecins associés :</strong> ' . htmlspecialchars($essai['Nom_Medecin']) . '</li>';
                } else {
                    echo '<li><strong>Médecin associé :</strong> Aucun médecin assigné</li>';
                }
                echo '</ul> </button>';
            }
        } else {
            echo '<p>Aucun essai ne correspond à votre recherche.</p>';
        }
    } catch (Exception $e) {
        // Capture de l'exception et affichage du message d'erreur
        echo '<p class="error">Une erreur est survenue lors de la recherche : ' . htmlspecialchars($e->getMessage()) . '</p>';
    }

    return $results;
}


function convertirAccent($texte) {
    // Remplacer les caractères accentués par leur équivalent sans accent
    $texte = preg_replace('/[éèêë]/u', 'e', $texte);  // Remplace les 'é', 'è', 'ê', 'ë' par 'e'
    $texte = preg_replace('/[àâä]/u', 'a', $texte);  // Remplace les 'à', 'â', 'ä' par 'a'
    $texte = preg_replace('/[îï]/u', 'i', $texte);   // Remplace les 'î', 'ï' par 'i'
    $texte = preg_replace('/[ôö]/u', 'o', $texte);   // Remplace les 'ô', 'ö' par 'o'
    $texte = preg_replace('/[ùûü]/u', 'u', $texte);  // Remplace les 'ù', 'û', 'ü' par 'u'
    $texte = preg_replace('/[ç]/u', 'c', $texte);    // Remplace 'ç' par 'c'
    $texte = preg_replace('/[ÿ]/u', 'y', $texte);    // Remplace 'ÿ' par 'y'
    return $texte;
}

function enterprise_filter($liste_EC) {
    echo "<option value='Tous'>Toutes les entreprises</option>";
    foreach ($liste_EC as $EC) {
        $enterpriseName = htmlspecialchars($EC['Nom_Entreprise'], ENT_QUOTES, 'UTF-8');
        echo '<option value="' . $enterpriseName . '">' . $enterpriseName . '</option>';
    }
}

function get_company(int $id_company): array {
    $conn = Connexion_base();

    try {
        $sql = "
    SELECT *
    FROM ENTREPRISES
    WHERE Id_entreprise = :id_entreprise;
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_entreprise', $id_company, PDO::PARAM_INT);
    $stmt->execute();
    
    // Récupérer les résultats
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return [];
    
    } finally {
    // Fermer la connexion
    Fermer_base($conn);
    }
}

function get_patient(int $id_patient): array {
    $conn = Connexion_base();

    try {
        $sql = "
    SELECT *
    FROM PATIENTS
    WHERE Id_patient = :id_patient;
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_patient', $id_patient, PDO::PARAM_INT);
    $stmt->execute();
    
    // Récupérer les résultats
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return [];
    
    } finally {
    // Fermer la connexion
    Fermer_base($conn);
    }
}

function display_patient_unique ($patient) {
    $patient = $patient[0];
    echo '<ul class = "patients">';
    echo '<li class = "noms_patients">' . htmlspecialchars($patient['Nom']) . " " .htmlspecialchars($patient['Prenom']). '</li>';
    echo '<li class = "age">' . htmlspecialchars($patient['Age']) . '</li>';
    if ($patient['Profile_picture'] == null){
        echo '<li> <img src="Pictures/defaultPicture.png" alt="pictureProfil" class="fixed_picture" style="cursor: pointer;"> </li>';
    } else {
    echo '<li class="fixed_picture">' . htmlspecialchars($patient['Profile_picture']) . '</li>';
    }
    echo '</ul>';
}

function verifier_essai_clinique(int $id_medecin, int $id_patient): bool {
    // Connexion à la base de données
    $conn = Connexion_base();
    
    try {
        // Vérifier si le médecin et le patient sont inscrits au même essai clinique
        $sql = "
        SELECT COUNT(*) AS count
        FROM MEDECIN_ESSAIS m
        JOIN PATIENTS_ESSAIS p ON m.Id_essai = p.Id_essai
        WHERE m.Id_medecin = :id_medecin
        AND p.Id_patient = :id_patient;
        ";
        
        // Préparer et exécuter la requête
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_medecin', $id_medecin, PDO::PARAM_INT);
        $stmt->bindParam(':id_patient', $id_patient, PDO::PARAM_INT);
        $stmt->execute();
        
        // Récupérer le résultat
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Si le résultat est supérieur à 0, cela signifie que le médecin et le patient sont inscrits au même essai
        return $result['count'] > 0;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return false;
    } finally {
        // Fermer la connexion
        Fermer_base($conn);
    }
}

function display_patient_medecin($patient){
    $patient = $patient[0];
    echo '<ul class = "patients">';
    echo '<li>' . htmlspecialchars($patient['Taille']) . '</li>';
    echo '<li>' . htmlspecialchars($patient['Poids']) . '</li>';
    echo '<li>' . htmlspecialchars($patient['Traitements']) . '</li>';
    echo '<li>' . htmlspecialchars($patient['Allergies']) . '</li>';
    echo '<li>' . htmlspecialchars($patient['Cni']) . '</li>';
    echo '</ul>';  
}

function tablename($id_user) {
    $conn = Connexion_base();

    $sql = "
        SELECT 'PATIENTS' AS TableName FROM PATIENTS WHERE Id_patient = ?
        UNION ALL
        SELECT 'MEDECINS' AS TableName FROM MEDECINS WHERE Id_medecin = ?
        UNION ALL
        SELECT 'ENTREPRISES' AS TableName FROM ENTREPRISES WHERE Id_entreprise = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $id_user, PDO::PARAM_INT);
    $stmt->bindParam(2, $id_user, PDO::PARAM_INT);
    $stmt->bindParam(3, $id_user, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Analyse des résultats
    if ($result) {
        return $result['TableName'];
    } else {
        echo "Id_user $id_user n'existe dans aucune table liée.";
    }
}


function Verif_inscription($id_user) {
    // Définitions des correspondances
    $tableau = [
        'MEDECINS' => 'Statut_inscription',
        'ENTREPRISES' => 'Verif_inscription'
    ];
    $id_table = [
        'MEDECINS' => 'Id_medecin',
        'ENTREPRISES' => 'Id_entreprise'
    ];
    
    // Récupération du nom de la table
    $tablename = tablename($id_user);
    
    // Vérification si la table est valide
    if (!isset($tableau[$tablename]) || !isset($id_table[$tablename])) {
        throw new Exception("Nom de table ou colonne invalide.");
    }
    
    $id_name = $id_table[$tablename];
    $column = $tableau[$tablename];

    // Connexion à la base de données
    $conn = Connexion_base();

    // Construction sécurisée de la requête SQL
    $sql = "SELECT $column FROM $tablename WHERE $id_name = :id_user";

    // Préparation de la requête
    $stmt = $conn->prepare($sql);

    // Liage de la variable :id_user
    $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);

    // Exécution de la requête
    try {
        $stmt->execute();
    } catch (PDOException $e) {
        // Gérer les exceptions
        throw new Exception("Erreur lors de l'exécution de la requête : " . $e->getMessage());
    }

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result[$column];
}

//entreprise en fonction essai
//liste des medecins actifs d'un essai
//statut d'un patient dans un essai

function company_trial ($id_essai) {
    $conn = Connexion_base();

    $sql = "
        SELECT Id_entreprise
        FROM ESSAIS_CLINIQUES
        WHERE ESSAIS_CLINIQUES.Id_essai = :Id_essai;
    "; 

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':Id_essai', $id_essai, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Analyse des résultats
    if ($result) {
        return $result['TableName'];
    } else {
        echo "Id_user $id_essai n'existe dans aucune table liée.";
    }
}


function get_medecin_trial(int $id_medecin, $id_essai): array {
    $conn = Connexion_base();

    try {
        $sql = "
            SELECT Id_medecin
            FROM MEDECINS_ESSAIS
            WHERE MEDECINS_ESSAIS.Id_essai = :Id_essai;
        "; 
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':Id_essai', $id_essai, PDO::PARAM_INT);
    $stmt->execute();
    
    // Récupérer les résultats
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return [];
    
    } finally {
    // Fermer la connexion
    Fermer_base($conn);
    }
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

//-------------------------------------- Notifications --------------------------------------//
// Fonction pour afficher un message d'erreur
function ErrorEditor($errorCode, $modal='false'){
    //if $_SESSION['ErrorCode'] is set, it will display the corresponding error message
    $ScriptError= [
        1 => "qsjk", 
        2=> "Erreur lors de la récupération des informations",
        3=> "L'email saisi ne correspond à aucun compte. Veuillez le vérifier et réessayer.",
        4=> "Votre compte n'a pas encore été validé par un administrateur. Veuillez réessayer ultérieurement.",
        5=> "Le mot de passe saisi est incorrect. Veuillez réessayer.",
        6=> "Cet email est déjà utilisé. Veuillez en choisir un autre ou vous connecter.",
        7=> "Erreur lors de la protection du mot de passe.",
        8=> "Erreur lors de l'ajout de l'utilisateur.",
    ];

    // Check if the error code exists in the array
    $errorMessage = isset($ScriptError[$errorCode]) ? $ScriptError[$errorCode] : "Erreur inconnue.";


    //If the error code is display on another modal 
    if ($modal == 'true') {
        echo'<p class="error-message">' . htmlspecialchars($errorMessage). '</p>'; 
    }
    else {
    //Display the modal with the message
    echo '
    <div id="modal" class="modal" style="display: flex; text-align: center;">
        <div class="modal-content">
        <p class="error-message">' . htmlspecialchars($errorMessage) . '</p>
            <a href="/projet-website/Homepage.php" class="close-btn">&times;</a>
        </div>
    </div>';
    }
}

// Fonction pour afficher un message de succès
function SuccesEditor($SuccessCode){
    $ScriptSucces= [
        1=> 'Votre inscription a bien été enregistrée.',
        2=> 'Bienvenue ' . $_SESSION['Nom'] .'. Ravi de vous revoir !',
        3=> 'Votre déconnexion a bien été prise en compte.',
        4=> 'Votre candidature a bien été enregistrée. Vous recevrez une notification dès qu\'elle sera traitée.'
    ];


    $CommSucces = [
        1 => 'Si votre inscription concerne un compte Médecin ou Entreprise,votre demande est soumise à la validation d\' administateur. </p>' . 
        '<p> Si vous vous êtes inscrit en tant que Patient,vous pouvez déjà vous connecter pour candidater à l\'un de nos essais cliniques !', 

        3=> 'Au plaisir de vous revoir.'
    ];

    $SuccesMessage = isset($ScriptSucces[$SuccessCode]) ? $ScriptSucces[$SuccessCode] :'Succès inconnu.';
    $Commentaire = isset($CommSucces[$SuccessCode]) ? $CommSucces[$SuccessCode] : '';

    // Affiche le modal avec le message
    echo '
    <div id="modal" class="modal" style="display: flex; text-align: center;">
        <div class="modal-content">
            <p class="validation-message">' . htmlspecialchars($SuccesMessage) . '</p>
            <p> '. ($Commentaire). '</p>
            <a href="/projet-website/Homepage.php" class="close-btn">&times;</a>
        </div>
    </div>';}
?>
