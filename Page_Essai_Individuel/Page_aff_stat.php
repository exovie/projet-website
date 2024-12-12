<?php
session_start();
//Connexion à la base
include_once '../Notifications/fonction_notif.php';
include_once ("../Fonctions.php");
$conn = Connexion_base();

include_once("Aff_Statistiques.php");

if (isset($_POST['Id_essai'])) {
    $id_essai = $_POST['Id_essai']; // Récupère la valeur si elle existe
}else {
    $id_essai = $_SESSION['Id_essai']; // Sinon, récupère la valeur de la session
}

$data = Stat_data($conn, $id_essai);
$poids_stats = Stat_Poids($data, $conn, $id_essai);
$taille_stats = Stat_Taille($data, $conn, $id_essai);
$age_stats = Stat_Age($data, $conn, $id_essai);
$sexe_stats = Stat_Sexe($data, $conn, $id_essai);

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distribution des Poids, Tailles, Âges et Sexe- Essai Clinique</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href='../website.css'>
    <link rel="stylesheet" href= '../navigationBar.css'>
    <link rel="stylesheet" href='../Notifications/Notifications_style.css'>
    <link rel="stylesheet" href='../Admin/Admin.css'>
    <style>
        /* Conteneur principal pour les graphiques */
        .charts-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr); /* 2 colonnes de taille égale */
            grid-gap: 20px; /* Espacement entre les éléments */
            margin: 20px;
            justify-content: center;
        }

        /* Style commun pour les histogrammes */
        .histogram {
            width: 100%; /* Largeur de 100% pour remplir la cellule de la grille */
            height: 300px; /* Hauteur spécifique aux histogrammes */
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            background-color: #f9f9f9;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Style spécifique pour le camembert */
        .camembert {
            width: 100%; /* Largeur de 100% pour remplir la cellule de la grille */
            height: 300px; /* Hauteur spécifique pour le camembert */
            padding: 20px;
            border: 2px solid #4CAF50;
            background-color: #f0f8ff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <!-- Code de la barre de navigation -->
    <div class="navbar">
        <div id="logo">
            <a href="../Homepage.php">
                <img src="../Pictures/logo.png" alt="minilogo" class="minilogo">
            </a>
        </div>
        <a href="../Essais.php" class="nav-btn">Essais Cliniques</a>

        <!-- Accès à la page de Gestion -->
        <?php if ($_SESSION['role'] == 'Admin'): ?>
            <a href="../Admin/Home_Admin.php" class="nav-btn">Gestion</a>
        <?php endif; ?>

        <!-- Accès à la messagerie -->
        <?php if (isset($_SESSION['Logged_user']) && $_SESSION['Logged_user'] === true): ?>
        <div class="dropdown">
            <a href= "<?= $_SESSION['origin'] ?>#messagerie">
                <img src="../Pictures/letterPicture.png" alt="letterPicture" style="cursor: pointer;">
            </a>
            <!-- Affichage de la pastille -->
            <?php 
            $showBadge = Pastille_nombre($_SESSION['Id_user']);
            if ($showBadge > 0): ?>
                <span class="notification-badge"><?= htmlspecialchars($showBadge) ?></span>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Connexion / Inscription -->
        <div class="dropdown">
            <a>
                <img src="../Pictures/pictureProfil.png" alt="pictureProfil" style="cursor: pointer;">
            </a>
            <div class="dropdown-content">
            <?php if (isset($_SESSION['Logged_user']) && $_SESSION['Logged_user'] === true): ?>
                <!-- Options pour les utilisateurs connectés -->
                <?php 
                if ($_SESSION['role'] == 'Medecin') {
                    echo "<h1 style='font-size: 18px; text-align: center;'>Dr " . htmlspecialchars($_SESSION['Nom'], ENT_QUOTES, 'UTF-8') . "</h1>";
                } elseif ($_SESSION['role'] == 'Entreprise') {
                    echo "<h1 style='font-size: 18px; text-align: center;'>" . htmlspecialchars($_SESSION['Nom'], ENT_QUOTES, 'UTF-8') . "®</h1>";
                } elseif(($_SESSION['role']=='Admin')){
                    echo "<h1 style='font-size: 18px; text-align: center;'>Admin</h1>";
                } else{
                    echo "<h1 style='font-size: 18px; text-align: center;'>" . htmlspecialchars($_SESSION['Nom'], ENT_QUOTES, 'UTF-8') . "</h1>";
                }
                if ($_SESSION["role"]!=='Admin'&& $_SESSION['Logged_user'] === true)
                {echo "<a href='../Page_Mes_Infos/Menu_Mes_Infos.php'>Mon Profil</a>";} ?>
                <a href="../Deconnexion.php">Déconnexion</a>
            <?php else: ?>
                <!-- Options pour les utilisateurs non connectés -->
                <a href="../Connexion/Form1_connexion.php#modal">Connexion</a>
                <a href="../Inscription/Form1_inscription.php#modal">S'inscrire</a>
            <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Message Success -->
    <?php 
    if (isset($_SESSION['SuccessCode'])): 
        SuccesEditor($_SESSION['SuccessCode']);
        unset($_SESSION['SuccessCode']); // Nettoyage après affichage
    endif; 
    ?>

    <!-- Message Erreur -->
    <?php 
    if (isset($_SESSION['ErrorCode'])): 
        ErrorEditor($_SESSION['ErrorCode']);
        unset($_SESSION['ErrorCode']); // Nettoyage après affichage
    endif; 
    ?>
    
    <!-- Messagerie -->
    <div id="messagerie" class="messagerie">
        <div class="messagerie-content">
            <!-- Lien de fermeture qui redirige vers Home_Admin.php -->
            <a href="<?= $_SESSION['origin'] ?>" class="close-btn">&times;</a>
            <h1>Centre de notifications</h1>
            <!-- Contenu de la messagerie -->
            <?php Affiche_notif($_SESSION['Id_user'])?>
        </div>
    </div>

    <!-- Contenu Principal-->
    <div class="content">
    <h1> Statistiques de cet essai</h1>


    <!-- Conteneur pour les graphiques -->
    <div class="charts-container">
        <!-- Histogramme des poids -->
        <div>
            <canvas id="distributionPoids" class="histogram"></canvas>
        </div>

        <!-- Histogramme des tailles -->
        <div>
            <canvas id="distributionTaille" class="histogram"></canvas>
        </div>

        <!-- Histogramme des âges -->
        <div>
            <canvas id="distributionAge" class="histogram"></canvas>
        </div>
    
        <!-- Camembert pour le sexe -->
        <div>
            <canvas id="camembertSexe" class="camembert"></canvas>
        </div> 
    </div>

    <script>
    // Récupérer les données depuis PHP pour chaque graphique (Poids, Taille, Âge et Sexe)
    
    // Poids
    const poidsLabels = <?= $poids_stats['tranches_poids_labels'] ?>;
    const poidsData = <?= $poids_stats['tranches_poids_counts'] ?>;

    // Taille
    const tailleLabels = <?= $taille_stats['tranches_taille_labels'] ?>;
    const tailleData = <?= $taille_stats['tranches_taille_counts'] ?>;

    // Âge
    const ageLabels = <?= $age_stats['tranches_age_labels'] ?>;
    const ageData = <?= $age_stats['tranches_age_counts'] ?>;

    // Sexe
    const sexeData = <?= $sexe_stats['sexe_json'] ?>;
    
    // Histogramme des Poids
    new Chart(document.getElementById('distributionPoids').getContext('2d'), {
        type: 'bar',
        data: {
            labels: poidsLabels,
            datasets: [{
                label: 'Nombre de patients',
                data: poidsData,
                backgroundColor: '#36A2EB'
            }]
        },
        options: {
            plugins: {
                title: { display: true, text: 'Distribution des Poids' }
            },
            scales: {
                x: { title: { display: true, text: 'Poids (kg)' } },
                y: { title: { display: true, text: 'Nombre de patients' }, beginAtZero: true }
            }
        }
    });

    // Histogramme des Tailles
    new Chart(document.getElementById('distributionTaille').getContext('2d'), {
        type: 'bar',
        data: {
            labels: tailleLabels,
            datasets: [{
                label: 'Nombre de patients',
                data: tailleData,
                backgroundColor: '#FF6384'
            }]
        },
        options: {
            plugins: {
                title: { display: true, text: 'Distribution des Tailles' }
            },
            scales: {
                x: { title: { display: true, text: 'Taille (cm)' } },
                y: { title: { display: true, text: 'Nombre de patients' }, beginAtZero: true }
            }
        }
    });

    // Histogramme des Âges
    new Chart(document.getElementById('distributionAge').getContext('2d'), {
        type: 'bar',
        data: {
            labels: ageLabels,
            datasets: [{
                label: 'Nombre de patients',
                data: ageData,
                backgroundColor: '#4CAF50'
            }]
        },
        options: {
            plugins: {
                title: { display: true, text: 'Distribution des Âges' }
            },
            scales: {
                x: { title: { display: true, text: 'Âge (ans)' } },
                y: { title: { display: true, text: 'Nombre de patients' }, beginAtZero: true }
            }
        }
    });

    // Camembert pour le Sexe
    new Chart(document.getElementById('camembertSexe').getContext('2d'), {
        type: 'pie',
        data: {
            labels: Object.keys(sexeData),
            datasets: [{
                data: Object.values(sexeData), // Nombre d'hommes et de femmes
                backgroundColor: ['#FF9F40', '#FF6384']
            }]
        },
        options: {
            plugins: {
                title: { 
                    display: true,
                    text: 'Proportion Hommes/Femmes de cet essai'
                }
            }
        }
    });
</script>
<button class="back-btn" onclick="window.location.href='<?php echo $_SESSION['origin']; ?>'">Retour</button>

</body>
</html>

