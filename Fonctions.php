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




//Fonctions  liées aux essais

function Demander_Medecin_essai(int $Id_essai, int $Id_medecin){
    $conn = Connexion_base();
    try {
    $requete = $conn -> prepare("INSERT INTO MEDECIN_ESSAIS(Id_medecin, Id_essai) VALUES (?,?,'Sollicite')");
    $requete -> execute(array($Id_medecin, $Id_essai))
    $req = $stmt ->prepare("INSERT INTO personnesSondees(`identifiant`, `email`, `passwd`, `admin`) VALUES (?,?,?,0)");
    $req->execute(array($identifiant, $mail, $mdp) );
    echo "Nouveau médecin sollicité avec succès pour cet essai!";
    } catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}}


?>  




