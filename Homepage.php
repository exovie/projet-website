<?php
session_start();
$_SESSION['origin'] = 'Homepage';
$db_name = "mysql:host=localhost;dbname=website_db"; 
$_SESSION['db_name'] = $db_name;
include 'Fonctions.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Clinicou - HomePage</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="'utf-8">
    <link rel="stylesheet" href='website.css'>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: rgba(64, 224, 208, 0.2);
            display: flex;
            flex-direction: column;
            align-items: center; /* Centre le contenu horizontalement */
        }
        h1 {
            color: rgb(24, 98, 104);
        }
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%; 
            background-color: turquoise;
            display: flex;
            justify-content: space-around;
            align-items: center;
            padding: 10px 0;
            z-index: 1000; /* Assure que la barre reste au-dessus des autres éléments */
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Optionnel : ombre */
            border-bottom: 2px solid #2a9d8f; /* Bordure basse */
        }
        /* Style des boutons de navigation */
        .nav-btn {
            background-color: rgb(24, 98, 104);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        /* Effet de survol du bouton */
        .nav-btn:hover {
            background-color: rgb(29, 186, 197);
        }
        .content {
            margin-top: 60px; /* Pour laisser de l'espace sous la barre de navigation */
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center; /* Centre le contenu horizontalement */
            text-align: center; /* Centre le texte */
        }

        /* Conteneur pour l'image et le menu déroulant */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        /* Menu déroulant */
        .dropdown-content {
            display: none;
            position: absolute;
            top: 100%; /* Affiche le menu juste en dessous de l'image */
            left: 50%;
            transform: translateX(-50%); /* Centre le menu par rapport à l'image */
            background-color: white;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1;
            min-width: 150px;
            max-width: calc(100vw - 20px); /* Assure que le menu ne dépasse pas les bords de la fenêtre */
            overflow-x: auto; /* Permet le défilement horizontal si nécessaire */
            border-radius: 8px; /* Coins arrondis */
        }

        .dropdown-content a {
            display: block;
            padding: 10px;
            color: black;
            text-decoration: none;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        /* Affiche le menu lorsque l'image est survolée */
        .dropdown:hover .dropdown-content {
            display: block;
        }
    </style>
</head>
<body>

    <!-- Conteneur fixe en haut de la page -->
    <div class="navbar">
        <div id="logo">
            <a href="Homepage.php">
                <img src="Pictures/minilogo.png" alt="minilogo">
            </a>
        </div>
        <button class="nav-btn">Essais Cliniques</button>
        <button class="nav-btn">Entreprise</button>
        <button class="nav-btn">Contact</button>
        <div class="dropdown">
            <a href="Homepage.php">
                <img src="Pictures/letterPicture.png" alt="pictureProfil" style="cursor: pointer;">
            </a>
            </div>
        <div class="dropdown">
            <a href="Homepage.php">
                <img src="Pictures/pictureProfil.png" alt="pictureProfil" style="cursor: pointer;">
            </a>
            <div class="dropdown-content">
                <?php if (isset($_SESSION['Logged_user']) && $_SESSION['Logged_user'] === true): ?>
                    <!-- Options pour les utilisateurs connectés -->
                    <a href="#">Mon Profil</a>
                    <a href="#">Déconnexion</a>
                <?php else: ?>
                    <!-- Options pour les utilisateurs non connectés -->
                    <a href="#">Connexion</a>
                    <a href="Inscription/Form1_inscription.php#modal">S'inscrire</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Message Success Inscription -->
    <?php if (isset ($_SESSION['inscriptionSuccess'])): 
        $inscriptionSuccessMessage = 'Votre inscription a bien été enregistrée.' ?>
        <div id="modal" class="modal" style="display: flex; text-align: center;">
            <div class="modal-content">
            <p class="validation-message"><?php echo htmlspecialchars($inscriptionSuccessMessage); ?></p>
                        <?php unset($_SESSION['inscriptionSuccess']); ?>
            <p>Si votre inscription concerne un compte Médecin ou Entreprise,votre demande est soumise à la validation d' administateur. </p> 
            <p> Si vous vous êtes inscrit en tant que Patient,vous pouvez déjà vous connecter pour candidater à l'un de nos essais cliniques !</p>
            <a href="/projet-website/Homepage.php" class="close-btn">&times;</a>
            </div>
        </div>
    <?php endif; ?>

    <!-- Contenu principal -->
    <div class="content">
        <h1>Bienvenue sur notre site</h1>
        <p>Contenu de la page...</p>
        <div>
            <img src="Pictures/logo.png" alt="logo" id="grologo">
            <h1 id="main_page">Clinicou, le site des <strong>essais cliniques !</strong></h1>
            <h2>Le site qui vous permet de vous inscrire <em>facilement</em> pour crever pour big pharma</h2>
            <p class="sarcasm">Un max de fun</p>
            <p><a href="https://www.linkedin.com/in/oussamaammar/">Pour plus d'informations</a></p>
            <img src="https://media.wired.com/photos/5f87340d114b38fa1f8339f9/master/w_1600%2Cc_limit/Ideas_Surprised_Pikachu_HD.jpg" alt="Surprised Pikachu">
            <p>L'eau, dans 20, 30 ans <br> il n'y en aura plus</p>
            <p><a href="#main_page">retour au début</a></p>
        </div>
    </div>
</body>
</html>

<!--
<form action="TD2_exo3.php" method="post">
  <label for="nom">Entrez le produit voulu et la quantité au format suivant :</label>
  <input type="text" id="nom" placeholder="produit, quantité" name="nom" required>
  
  <br><br>
  <input type="submit" value="Envoyer">
</form> 
-->
