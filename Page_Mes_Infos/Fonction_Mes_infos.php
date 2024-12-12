<?php
/*Fichier php qui regroupe les fonctions utiliser pour la page Mes Infos*/

//session_start();
include_once("../Fonctions.php");
include_once("../Inscription/fonctionInscription.php");
$conn = Connexion_base();


// Fonction pour récupérer les informations de l'utilisateur
function getUserInfo($conn, int $id_user) {

    //Récupérer le role de l'Id_user
    $sql = $conn -> prepare("SELECT Role FROM USERS WHERE Id_user=:id_user");
    $sql->execute(['id_user' => $id_user]);
    $role=$sql->fetch(PDO::FETCH_ASSOC)['Role'] ?? null;
    
    switch ($role) {
        case 'Patient':
            $query = "SELECT Nom, Prenom, Date_naissance, Sexe, Telephone, Profile_picture, Taille, Poids, Traitements, Allergies
            FROM PATIENTS WHERE Id_patient = :id_user";
            break;
        case 'Medecin':
            $query = "SELECT Nom, Prenom, Specialite, Telephone, Matricule FROM MEDECINS WHERE Id_medecin = :id_user";
            break;
        case 'Entreprise':
            $query = "SELECT Nom_entreprise, Telephone, Siret FROM ENTREPRISES WHERE Id_entreprise = :id_user";
            break;
        default:
            return null;
    }

    $stmt = $conn->prepare($query);
    $stmt->execute(['id_user' => $id_user]);
    $result=$stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && isset($result['Profile_picture'])) {
        $profilePictureData = base64_encode($result['Profile_picture']);
        $result['Profile_picture'] = "data:image/png;base64,{$profilePictureData}";
    }
    
    return $result;
}


// Fonction pour mettre à jour les informations
function updateUserInfo($conn, int $id_user) {
    try {
        // Récupérer le rôle de l'utilisateur
        $sql = $conn->prepare("SELECT Role FROM USERS WHERE Id_user=:id_user");
        $sql->execute(['id_user' => $id_user]);
        $role = $sql->fetch(PDO::FETCH_ASSOC);
        if (!$role) {
            echo "Utilisateur introuvable.";
            return false;
        }

        $roleValue = $role['Role'];
        $query = "";
        switch ($roleValue) {
            case 'Patient':
                $query = "UPDATE PATIENTS SET Nom = :Nom, Prenom = :Prenom, Date_naissance = :Date_naissance,
                          Sexe = :Sexe, Telephone = :Telephone, Profile_picture= :Profile_picture, Taille = :Taille, Poids = :Poids, Traitements = :Traitements, Allergies = :Allergies
                          WHERE Id_patient = :id_user";

                break;
            case 'Medecin':
                $query = "UPDATE MEDECINS SET Nom = :Nom, Prenom = :Prenom, Specialite = :Specialite,Telephone = :Telephone, 
                Matricule = :Matricule,Profile_picture = :Profile_picture WHERE Id_medecin = :id_user";
                break;
            case 'Entreprise':
                $query = "UPDATE ENTREPRISES SET Nom_entreprise = :Nom_entreprise, Telephone = :Telephone, Profile_picture = :Profile_picture,
                          Siret = :Siret WHERE Id_entreprise = :id_user";
                break;
            default:
                echo "Rôle non pris en charge : $roleValue";
                return false;
        }

        $data = $_POST;
        $data['id_user']= $id_user;
        // Vérifier si un fichier a été téléchargé
         if (isset($_FILES['Profile_picture']) && $_FILES['Profile_picture']['error'] == UPLOAD_ERR_OK) {
        // Si un fichier a été téléchargé, on le récupère et on le convertit en BLOB
        $profilePicture = file_get_contents($_FILES['Profile_picture']['tmp_name']);
        $data['Profile_picture'] = $profilePicture;  // Stocker l'image téléchargée
        } else {
        // Si aucune nouvelle image n'a été téléchargée, garder l'ancienne image
        // Vous devez peut-être récupérer l'image existante depuis la base de données
                if ($roleValue=='Patient'){
                    $stmt = $conn->prepare("SELECT Profile_picture FROM PATIENTS WHERE Id_patient = :id_user");
                } elseif ($roleValue=='Medecin'){
                    $stmt = $conn->prepare("SELECT Profile_picture FROM MEDECINS WHERE Id_medecin = :id_user");
                }else {
                    $stmt = $conn->prepare("SELECT Profile_picture FROM ENTREPRISES WHERE Id_entreprise = :id_user");
                }
            $stmt->execute(['id_user' => $id_user]);
            $existingProfilePicture = $stmt->fetch(PDO::FETCH_ASSOC);
        // Si une image existante est trouvée, la conserver dans $data['Profile_picture']
        if ($existingProfilePicture) {
            $data['Profile_picture'] = $existingProfilePicture['Profile_picture'];
        }
    }
        
        // Réorganiser les clés dans l'ordre souhaité
        $newArray = [];
        $profilePicture = isset($data['Profile_picture']) ? $data['Profile_picture'] : null;  // Conserver la valeur de Profile_picture si elle existe

        //Réorganiser selon le rôle à mettre à jour (car contraint par ValidateResponsesByRole)
        if ($roleValue=='Patient'|| $roleValue=='Entreprise'){
            foreach ($data as $key => $value) {
                // Si on arrive sur Telephone, on ajoute d'abord Telephone puis Profile_picture
                if ($key === 'Telephone') {
                    $newArray['Telephone'] = $value;
                    if ($existingProfilePicture !== null) {
                        $newArray['Profile_picture'] = $existingProfilePicture['Profile_picture'];
                        $existingProfilePicture = null; // On s'assure de ne pas rajouter deux fois Profile_picture
                }
                } else {
                    // Ajouter les autres éléments normalement
                    if ($key !== 'Profile_picture') {
                    $newArray[$key] = $value;
                }
                }
            }}
        elseif ($roleValue=='Medecin'){
            foreach ($data as $key => $value) {
                // Si on arrive sur Telephone, on ajoute d'abord Telephone puis Profile_picture
                if ($key === 'Telephone') {
                    $newArray['Telephone'] = $value;
                    if ($existingProfilePicture !== null) {
                        $newArray['Profile_picture'] = $existingProfilePicture['Profile_picture'];
                        $existingProfilePicture = null; // On s'assure de ne pas rajouter deux fois Profile_picture
                }
                } else {
                    // Ajouter les autres éléments normalement
                    if ($key !== 'Profile_picture') {
                    $newArray[$key] = $value;
                }
                }
            }}
        else{
            echo "Rôle non pris en charge : $roleValue";
        }

        // Si Profile_picture n'a pas été inséré et qu'il y a une image, on l'ajoute à la fin
        if ($existingProfilePicture!== null) {
        $newArray['Profile_picture'] = $profilePicture['Profile_picture'];
        }

        // Résultat dans $newArray : les données réorganisées avec Profile_picture juste après Telephone
        $data = $newArray;
        
        //Changement des clés en valeurs numériques
        $numericArray = array_values($data);

        // Vérification du format des réponses
        $errorMessages = '';
        
        if ($role == 'Patient'){
            $date = $_POST['Date_naissance'];
            $ageErr= Verif_age($date); 
            if ($ageErr == false) {
            $errorMessages= $errorMessages."Vous devez être majeur pour vous inscrire.";
            }
        }

        $errors = validateResponsesByRole($roleValue, $numericArray);
        
        if (!empty($errors)) {
         // S'il y a des erreurs, on les affiche
        $errorMessages = '';
        if (!empty($errors)) {
            foreach ($errors as $error) {
                $errorMessages .= $error;
            }
        }

        $_SESSION['FormsErr'] = $errorMessages;
        Fermer_base($conn);
        header('Location: Menu_Mes_Infos.php#modal');
        } else {
        // Si pas d'erreur, on passe à la page suivante
        
        $update = $conn->prepare($query);

        $_SESSION['reponsesInscription'] = ($_POST); 
        Fermer_base($conn);
        header("Location: Menu_Mes_Infos.php#modal");
        $result = $update->execute($data);
        
        if ($result) {
            echo "Mise à jour réussie.";
            return true;
        } else {
            echo "Échec de la mise à jour.";
            return false;
        }
    }} catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return false;
    }
}


function getHistoriqueEssais($conn, int $id_user) {

    try {
        //Récupérer le role de l'Id_user
        $sql = $conn -> prepare("SELECT Role FROM USERS WHERE Id_user=:id_user");
        //$query->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $sql->execute(['id_user' => $id_user]);
        $role=$sql->fetch(PDO::FETCH_ASSOC);
        $roleValue=$role['Role'];
        switch ($roleValue) {
            case 'Patient':
                $query = "SELECT e.Id_essai, e.Titre, e.Statut, e.Date_fin, e.Date_creation
                FROM PATIENTS_ESSAIS p JOIN ESSAIS_CLINIQUES e ON p.Id_essai = e.Id_essai
                WHERE p.Id_patient = :id_user ORDER BY e.Date_creation DESC";
                break;
            case 'Medecin':
                $query = " SELECT e.Id_essai, e.Titre, e.Statut, e.Date_fin, e.Date_creation
                FROM MEDECIN_ESSAIS m JOIN ESSAIS_CLINIQUES e ON m.Id_essai = e.Id_essai
                WHERE m.Id_medecin = :id_user ORDER BY e.Date_creation DESC";
                break;
                case 'Entreprise':
                $query = "SELECT Id_essai, Titre, Statut, Date_fin, Date_creation
                          FROM ESSAIS_CLINIQUES
                          WHERE Id_entreprise = :id_user";
            break;
        default:
            return false;
    }
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $stmt->execute();
        // Récupérer les résultats
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return [];
    }
}


?>


