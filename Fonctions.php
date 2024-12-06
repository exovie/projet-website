<?php
session_start();



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




// FONCTIONS




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
        echo'<p class="error-message">' . htmlspecialchars($errorMessage) . '</p>'; 
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
            <p> '. htmlspecialchars($Commentaire). '</p>
            <a href="/projet-website/Homepage.php" class="close-btn">&times;</a>
        </div>
    </div>';
}
?>  

