<?php
/*Création des essais */
session_start();
include("../Fonctions.php");
$role_user=$_SESSION['role'];
$id_user=$_SESSION['Id_user'];

$conn= Connexion_base();
    try {
        // Si l'utilisateur n'est pas une entreprise, ne pas créer l'essai
        if ($role_user !== 'Entreprise') {
            return "Vous devez être une entreprise pour créer un essai clinique.";
        }

        //Verification de l'existence des données
        $titre = isset($_POST['Titre']) ? $_POST['Titre'] : null;
        $contexte = isset($_POST['Contexte']) ? $_POST['Contexte'] : null;

        // Récupérer les données du formulaire
        $titre = $_POST['Titre'];
        $contexte = $_POST['Contexte'];
        $objectif_essai = $_POST['Objectif_essai'];
        $design_etude = $_POST['Design_etude'];
        $critere_evaluation = $_POST['Critere_evaluation'];
        $resultats_attendus = $_POST['Resultats_attendus'];
        $nb_medecins = intval($_POST['Nb_medecins']);
        $nb_patients = intval($_POST['Nb_patients']);
        $date_creation = date('Y-m-d'); // Date actuelle
        $statut = "En attente";

        // Insertion des données dans la base de données
        $query = "
            INSERT INTO ESSAIS_CLINIQUES (
                Titre, Contexte, Objectif_essai, Design_etude,
                Critere_evaluation, Resultats_attendus, Date_lancement, Date_fin, Date_creation, Statut,
                Id_entreprise, Nb_medecins, Nb_patients 
            ) VALUES (
                :Titre, :Contexte, :Objectif_essai, :Design_etude,
                :Critere_evaluation, :Resultats_attendus, NULL, NULL, :Date_creation, :Statut,
                :Id_entreprise, :Nb_medecins, :Nb_patients
            )
        ";

        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':Titre' => $titre,
            ':Contexte' => $contexte,
            ':Objectif_essai' => $objectif_essai,
            ':Design_etude' => $design_etude,
            ':Critere_evaluation' => $critere_evaluation,
            ':Resultats_attendus' => $resultats_attendus,
            ':Nb_medecins' => $nb_medecins,
            ':Nb_patients' => $nb_patients,
            ':Date_creation' => $date_creation,
            ':Statut' => $statut,
            ':Id_entreprise' => $id_user,
        ]);


        echo "<!DOCTYPE html>
        <html>
        <head>
            <title>Création Essai </title>
            <link rel=\"stylesheet\" href='../website.css'>
            <link rel=\"stylesheet\" href='../navigationBar.css'>
            <link rel=\"stylesheet\" href='../Notifications/Notifications_style.css'>
            <link rel=\"stylesheet\" href='Admin.css'>
            <style>
                .message-container {
                    background-color: white;
                    border-radius: 10px;
                    padding: 30px;
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                    text-align: center;
                    width: 50%;
                    max-width: 400px;
                }
                .message-container h1 {
                    color: red;
                    font-size: 20px;
                    margin-bottom: 20px;
                }
                .message-container button {
                    background-color: #4CAF50;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 16px;
                }
                .message-container button:hover {
                    background-color: #45a049;
                }
            </style>
        </head>
        <body>
        <!-- Code de la barre de navigation -->
        <div class=\"navbar\">
            <div id=\"logo\">
                <a href='../Homepage.php'>
                    <img src=../Pictures/logo.png alt=\"minilogo\" class=\"minilogo\">
                </a>
            </div>
            <a href='../Essais.php' class=\"nav-btn\">Essais Cliniques</a>

            <!-- Accès à la page de Gestion -->
            <a href='Home_Admin.php' class=\"nav-btn\">Gestion</a>

            <!-- Connexion / Inscription -->
            <div class=\"dropdown\">
                <a>
                    <img src=../Pictures/pictureProfil.png alt=\"pictureProfil\" style=\"cursor: pointer;\">
                </a>
                </div>
            </div>
        </div>
        <div class='content'>
        <div class='message-container'>
            <h1>L'essai clinique a été créé avec succès</h1>
            <button onclick=window.location.href='Menu_Mes_Infos.php'>Retour à la page d'accueil</button>
        </div>
        </div>
        </body>
        </html>";
    } catch (PDOException $e) {
        return "Erreur : " . $e->getMessage();
    }
    $conn= NULL;
?>
