<?php
session_start();
$servername = $_SESSION["servername"];



function List_entreprise(string $servername, int $id_entreprise) {

    $conn = Connexion_base();

    try {
        $sql = "
        SELECT E.* 
        FROM ENTREPRISES E
        JOIN FONCTIONS F ON E.Id_entreprise = F.Id_entreprise
        WHERE F.Id_Fonction = :Id_Fonction
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':Id_Fonction', $Id_Fonction, PDO::PARAM_INT);
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
?>  

