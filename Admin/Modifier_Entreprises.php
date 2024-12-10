<?php
//Connection à la base
include('Connexion_base.php');
$conn=Connexion_base();


if (!isset($_GET['id'])) {
    header('Location: Liste_entreprises.php');
    exit;
}

$id_entreprise = intval($_GET['id']);
$query = "SELECT Id_entreprise, Nom_entreprise, Telephone, Siret FROM ENTREPRISES WHERE Id_entreprise = :id_entreprise";
$stmt = $conn->prepare($query);
$stmt->execute(['id_entreprise' => $id_entreprise]);
$entreprise = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$entreprise) {
    echo "Entreprise introuvable.";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Modifier Entreprise</title>
    <link rel="stylesheet" href='website.css'>
    <link rel="stylesheet" href= 'navigationBar.css'>
    <style>
        /* Styles généraux pour la page */
        body {
            background-color: turquoise; /* Fond de la page en turquoise */
            font-family: Arial, sans-serif; /* Police pour le texte */
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center; /* Centrage horizontal */
            align-items: center; /* Centrage vertical */
            height: 100vh; /* Hauteur de la fenêtre de vue */
            text-align: center; /* Centrer le texte */
        }

        /* Conteneur principal pour le contenu centré */
        .container {
            background-color: white; /* Fond blanc pour les éléments */
            padding: 30px;
            border-radius: 10px; /* Coins arrondis */
            width: 60%; /* Largeur du conteneur */
            max-width: 500px; /* Limite la largeur du conteneur */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1); /* Ombre autour du conteneur */
            margin-top: 10vh; /* Décale le conteneur vers le bas (1/3 de la hauteur de la fenêtre) */
            text-align: center; /* Centre le texte dans le conteneur */
            justify-content: flex-start;
            margin-top: 200px;
        }

        /* Styles pour le titre */
        h1 {
            margin-bottom: 20px; /* Espacement en dessous du titre */
            color: #333; /* Couleur du titre */
            font-size: 24px; /* Taille du titre */
        }

        /* Styles pour le formulaire */
        form {
            display: flex;
            flex-direction: column; /* Alignement des éléments en colonne */
            gap: 15px; /* Espacement entre les éléments du formulaire */
        }

        label {
            font-size: 15px; /* Taille de la police pour les labels */
            display: inline-block;
            margin-bottom: 5px;
        }

        input[type="text"], select {
            padding: 10px;
            font-size: 16px;
            border: 2px solid #ccc; /* Bordure grise pour les champs */
            border-radius: 5px; /* Coins arrondis pour les champs */
            width: 100%; /* Les champs prennent toute la largeur disponible */
            box-sizing: border-box; /* Assure que padding et border ne dépassent pas la largeur */
        }

        /* Styles des boutons */
        button {
            padding: 10px 20px;
            color: white; /* Couleur du texte du bouton */
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            width: 48%; /* Largeur des boutons */
        }

        /* Bouton "Enregistrer les modifications" (vert) */
        .save-btn {
            background-color: #4CAF50;
        }
        .save-btn:hover {
            background-color: #45a049; /* Couleur au survol */
        }

        /* Bouton "Retour à la page précédente" (rouge) */
        .cancel-btn {
            background-color: #f44336;
        }
        .cancel-btn:hover {
            background-color: #d32f2f; /* Couleur au survol */
        }
    </style>
</head>
<body>
     <!-- Code de la barre de navigation -->
     <div class="navbar">
        <div id="logo">
            <a href="Homepage.php">
                <img src="Pictures/logo.png" alt="minilogo" class="minilogo">
            </a>
        </div>
        <a href="Essais.php" class="nav-btn">Essais Cliniques</a>
        <a href="Entreprises.php" class="nav-btn">Entreprise</a>
        <a href="Contact.php" class="nav-btn">Contact</a>
        <div class="dropdown">
            <a href="Homepage.php">
                <img src="Pictures/letterPicture.png" alt="letterPicture" style="cursor: pointer;">
            </a>
        </div>
        <div class="dropdown">
            <a>
                <img src="Pictures/pictureProfil.png" alt="pictureProfil" style="cursor: pointer;">
            </a>
            <div class="dropdown-content">
            <?php if (isset($_SESSION['Logged_user']) && $_SESSION['Logged_user'] === true): ?>
                <!-- Options pour les utilisateurs connectés -->
                <?php 
                if ($_SESSION['role'] == 'Medecin') {
                    echo "<h1 style='font-size: 18px; text-align: center;'>Dr " . htmlspecialchars($_SESSION['Nom'], ENT_QUOTES, 'UTF-8') . "</h1>";
                } elseif ($_SESSION['role'] == 'Entreprise') {
                    echo "<h1 style='font-size: 18px; text-align: center;'>" . htmlspecialchars($_SESSION['Nom'], ENT_QUOTES, 'UTF-8') . "®</h1>";
                } else {
                    echo "<h1 style='font-size: 18px; text-align: center;'>" . htmlspecialchars($_SESSION['Nom'], ENT_QUOTES, 'UTF-8') . "</h1>";
                }
                ?>
                <a href="#">Mon Profil</a>
                <a href="Deconnexion.php">Déconnexion</a>
            <?php else: ?>
                <!-- Options pour les utilisateurs non connectés -->
                <a href="Connexion/Form1_connexion.php#modal">Connexion</a>
                <a href="Inscription/Form1_inscription.php#modal">S'inscrire</a>
            <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="container">
        <h1>Modifier les informations de l'Entreprise</h1>
        <form id="form-modification" method="POST" action="Enregistrer_modif.php">
            <input type="hidden" name="role" value="Entreprise">
            <input type="hidden" name="id" value="<?= htmlspecialchars($entreprise['Id_entreprise']) ?>">
            <label>Nom de l'entreprise <input type="text" name="Nom" value="<?= htmlspecialchars($entreprise['Nom_entreprise']) ?>"></label><br>
            <label>Téléphone <input type="text" name="Telephone" value="<?= htmlspecialchars($entreprise['Telephone']) ?>"></label><br>
            <label>Siret <input type="text" name="Siret" value="<?= htmlspecialchars($entreprise['Siret']) ?>"></label><br>
            <!-- Conteneur des boutons -->
            <div class="buttons-container">
                <button type="submit" class="save-btn">Enregistrer les modifications</button>
            </div>
        </form>
        <form id="form-modification" method="POST" action="Liste_Entreprises.php">
            <!-- Conteneur des boutons -->
            <div class="buttons-container">
                <button type="submit" class="cancel-bt">Retour à la liste des entreprises</button>
            </div>
        </form>
        </div>
</body>
</html>
