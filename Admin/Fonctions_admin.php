    <?php
    include("../Fonctions.php");
    session_start();
    function Save_BdD_modif(){
        $conn = Connexion_base();
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['modifications'])) {
        $modifications = $_SESSION['modifications'];
        $role = $modifications['role'];
        $id = $modifications['id'];
        $data = $modifications['data'];
        try {
    
            if ($role === 'Patient') {
                $query = "UPDATE PATIENTS SET 
                            Nom = :Nom, 
                            Prenom = :Prenom, 
                            Sexe = :Sexe, 
                            Telephone = :Telephone 
                          WHERE Id_patient = :id";
            } elseif ($role === 'Medecin') {
                $query = "UPDATE MEDECINS SET 
                            Nom = :Nom, 
                            Prenom = :Prenom, 
                            Specialite = :Specialite, 
                            Matricule = :Matricule, 
                            Telephone = :Telephone
                          WHERE Id_medecin = :id";
            } elseif ($role === 'Entreprise') {
                $query = "UPDATE ENTREPRISES SET 
                            Nom_entreprise = :Nom_entreprise, 
                            Telephone = :Telephone, 
                            Siret = :Siret 
                          WHERE Id_entreprise = :id";
            } else {
                throw new Exception("Rôle invalide.");
            }
            
            $stmt = $conn->prepare($query);
            $data['id'] = $id;
            $stmt->execute($data);
            unset($_SESSION['modifications']);
    
        } catch (Exception $e) {
            echo "<div style='color: red; text-align: center; margin-top: 20%;'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
            exit;
        }
    } else {
        echo "<div style='color: red; text-align: center; margin-top: 20%;'>Aucune modification à appliquer.</div>";
        exit;
    }
}
    ?>