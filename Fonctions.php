<?php
session_start();
$dbname = $_SESSION["dbname"];



function List_entreprise(string $servername, int $id_entreprise): array {

    $conn = Connexion_base();

    try {
        $sql = "
    SELECT *
    FROM ENTREPRISES
    WHERE Id_entreprise = :Id_entreprise
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':Id_entreprise', $id_entreprise, PDO::PARAM_INT);
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

function List_Medecin(string $servername, int $id_medecin): array {
    $conn = Connexion_base();

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