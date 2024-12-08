<?php
/*Fichier php qui regroupe les fonctions utiliser pour la page Mes Infos*/

//session_start();
include("../Fonctions.php");
$conn = Connexion_base();

//$role_user=$_SESSION['role'];
//id_user=$_SESSION['id_user'];

//$id_user=6;
//$id_entreprise= $id_user;
//$id_user = isset($_GET['id_user']) ? (int)$_GET['id_user'] : 6;

// Fonction pour récupérer les informations de l'utilisateur
function getUserInfo($conn, int $id_user) {

    //Récupérer le role de l'Id_user
    $sql = $conn -> prepare("SELECT role FROM users WHERE Id_user=:id_user");
    $sql->execute(['id_user' => $id_user]);
    $role=$sql->fetch(PDO::FETCH_ASSOC)['role'] ?? null;

    switch ($role) {
        case 'Patient':
            $query = "SELECT * FROM patients WHERE Id_patient = :id_user";
            break;
        case 'Medecin':
            $query = "SELECT * FROM medecins WHERE Id_medecin = :id_user";
            break;
        case 'Entreprise':
            $query = "SELECT * FROM entreprises WHERE Id_entreprise = :id_user";
            break;
        default:
            return null;
    }

    $stmt = $conn->prepare($query);
    $stmt->execute(['id_user' => $id_user]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fonction pour mettre à jour les informations
function updateUserInfo($conn, int $id_user) {

    //Récupérer le role de l'Id_user
    $sql = $conn -> prepare("SELECT Role FROM users WHERE Id_user=:id_user");
    //$query->bindParam(':id_user', $id_user, PDO::PARAM_INT);
    $sql->execute(['id_user' => $id_user]);
    $role=$sql->fetch(PDO::FETCH_ASSOC);
    $roleValue=$role['Role'];
    print_r($_POST);
    switch ($roleValue) {
        case 'Patient':
            $query = "UPDATE patients SET Nom = :Nom, Prenom = :Prenom, Date_naissance = :Date_naissance,
                      Sexe = :Sexe, Telephone = :Telephone, Taille = :Taille, Poids = :Poids, 
                      Traitements = :Traitements, Allergies = :Allergies
                      WHERE Id_Patients = :id_user";
            break;
        case 'Medecin':
            $query = "UPDATE medecins SET Nom = :Nom, Prenom = :Prenom, Specialite = :Specialite,
                      Telephone = :Telephone, Matricule = :Matricule WHERE Id_medecin = :id_user";
            break;
        case 'Entreprise':
            echo "Rentre dans entreprise";
            $query = "UPDATE entreprises SET Nom_entreprise = :Nom_entreprise, Telephone = :Telephone, Profil_picture = :Profil_picture,
                      Siret = :Siret WHERE Id_entreprise = :id_user";
            echo "Passer";
            break;
        default:
            return false;
    }

    $data = $_POST;
    $data['id_user'] = $id_user; // Ajout de l'ID utilisateur
    // print_r($data);
    // $stmt = $conn->prepare($query);
    // return $stmt->execute($data);
    try {
        echo "debut try";
        $stmt = $conn->prepare($query);
        $result = $stmt->execute($data);
        print_r($result);
        if ($result) {
            echo "Mise à jour réussie.";
            return true;
        } else {
            echo "Échec de la mise à jour.";
            return false;
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        return false;
    
}}


function getHistoriqueEssais($conn, int $id_user) {

    try {
        //Récupérer le role de l'Id_user
        $sql = $conn -> prepare("SELECT Role FROM users WHERE Id_user=:id_user");
        //$query->bindParam(':id_user', $id_user, PDO::PARAM_INT);
        $sql->execute(['id_user' => $id_user]);
        $role=$sql->fetch(PDO::FETCH_ASSOC);
        $roleValue=$role['Role'];
        switch ($roleValue) {
            case 'Patient':
                $query = "SELECT Titre, Statut, Date_creation
                          FROM patients_essais 
                          JOIN essais_cliniques  ON patients_essais.Id_essai = essais_cliniques.Id_essai
                          WHERE patients_essais.Id_patient = :id_user";
                break;
            case 'Medecin':
                $query = "SELECT Titre, Statut, Date_creation
                          FROM medecin_essais 
                          JOIN essais_cliniques  ON medecin_essais.Id_essai = essais_cliniques.Id_essai
                          WHERE medecin_essais.Id_medecin = :id_user";
                break;
                case 'Entreprise':
                $query = "SELECT Titre, Statut, Date_creation
                          FROM essais_cliniques
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


