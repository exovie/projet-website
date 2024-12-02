<?php
session_start();
$dbname = $_SESSION["dbname"];

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

    foreach ($resultats as $entreprise) {
        echo '<ul>';
        echo '<li>Nom entreprise : ' . $entreprise['Nom_entreprise'] . '</li>';
        echo '<li>Telephone : ' . $entreprise['Telephone'] . '</li>';
        echo '</ul>';
    }
    
    foreach ($clinical_trials as $essai_clinique) {
        echo '<ul>';
        echo '<li>Titre : ' . $essai_clinique['Titre'] . '</li>';
        echo '<li>Contexte : ' . $essai_clinique['Contexte'] . '</li>';
        echo '<li>Objectif de l\'essai : ' . $essai_clinique['Objectif_essai'] . '</li>';
        echo '<li>Design de l\'étude : ' . $essai_clinique['Design_etude'] . '</li>';
        echo '<li>Critère d\'évaluation : ' . $essai_clinique['Critere_evaluation'] . '</li>';
        echo '<li>Résultats attendus : ' . $essai_clinique['Resultats_attendus'] . '</li>';
        echo '<li>Date de lancement : ' . $essai_clinique['Date_lancement'] . '</li>';
        echo '<li>Date de fin : ' . $essai_clinique['Date_fin'] . '</li>';
        echo '<li>Date de création : ' . $essai_clinique['Date_creation'] . '</li>';
        echo '<li>Statut : ' . $essai_clinique['Statut'] . '</li>';
        echo '</ul>';
    }

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

function Valider_inscription(string $servername) {
    try {
        $bdd = new PDO($servername, 'root', '');
        echo 'connexion réussie';
        } 
        catch (Exception $e) {
            echo 'connexion échouée';
            die ('Erreur : ' . $e->getMessage () );
        }
}
