<?php
/*Fonction qui calcule et génère les statistiques d’un essai lorsque que la phase de recrutement est finie*/

session_start();

//Connexion à la base
include("Connexion_base.php");
$conn = Connexion_base();

$id_essai = isset($_GET['id_essai']);

function Stat_data($conn, int $id_essai){
    try {
        // Requête pour récupérer les poids et les sexes des patients
        $query = $conn->prepare("
            SELECT Poids, Sexe, Taille, Date_naissance
            FROM patients 
            JOIN patients_essais ON patients.Id_patient = patients_essais.Id_patient
            WHERE patients_essais.Id_essai = :id_essai
        ");

        $query->bindParam(':id_essai', $id_essai, PDO::PARAM_INT);
        $query->execute();

        // Récupérer les résultats
        $patients = $query->fetchAll(PDO::FETCH_ASSOC);
        return $patients;
    }
        catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
            die();
        }

}

function Stat_Poids($data, $conn, int $id_essai){

    try{
        $poids = array_column($data, 'Poids');

        // Calcul du poids maximum arrondi au multiple de 5
        $poids_max = ceil(max($poids) / 5) * 5;

        // Générer les intervalles de 5kg sur l'histogramme
        $tranches = [];
        $intervalle = 5;
        for ($i = 40; $i < $poids_max; $i += $intervalle) {
            $tranches["$i - " . ($i + $intervalle)] = 0;
         }

        // Regrouper les poids dans les tranches
        foreach ($poids as $p) {
            foreach ($tranches as $range => &$count) {
                [$min, $max] = explode(' - ', $range);
                if ($p >= $min && $p < $max) {
                    $count++;
                    break;
                }
            }}
        
        //Return et Conversion en JSON 
        return [
            'poids' => $poids,
            'tranches_poids_labels' => json_encode(array_keys($tranches)),
            'tranches_poids_counts' => json_encode(array_values($tranches))
        ];

    }catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        die();
    }
}

function Stat_Taille($data, $conn, int $id_essai){

    try{
        $tailles = array_column($data, 'Taille');
        // Calcul du minimum et du maximum des tailles, ajustés aux multiples de 5
        $taille_min = floor(min($tailles) / 5) * 5; // Plus petit multiple de 5 inférieur
        $taille_max = ceil(max($tailles) / 5) * 5;  // Plus grand multiple de 5 supérieur

        // Générer les tranches (catégories)
        $tranches_taille = [];
        $intervalle = 5;
        for ($i = $taille_min; $i < $taille_max; $i += $intervalle) {
            $tranches_taille["$i - " . ($i + $intervalle)] = 0;
        }
    
        // Regrouper les tailles dans les tranches
        foreach ($tailles as $t) {
            foreach ($tranches_taille as $range => &$count) {
                [$min, $max] = explode(' - ', $range);
                if ($t >= $min && $t < $max) {
                    $count++;
                    break;
                }
            }
        }
    
        //Conversion en JSON
        return [
            'taille' => $tailles,
            'tranches_taille_labels' => json_encode(array_keys($tranches_taille)),
            'tranches_taille_counts' => json_encode(array_values($tranches_taille))
        ];

    }catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        die();
    }

}


function Stat_Age($data, $conn, int $id_essai){

    try{
        $dates_naissance = array_column($data, 'Date_naissance'); // Attribut 'Date_naissance'

        // Calculer l'âge actuel de chaque patient
        $ages = [];
        foreach ($dates_naissance as $date_naissance) {
            $birthDate = new DateTime($date_naissance);
            $today = new DateTime();
            $age = $today->diff($birthDate)->y; // Calcul de l'âge en années
            $ages[] = $age;
        }

        // Calcul du minimum et du maximum des âges, ajustés aux multiples de 5
        $age_min = floor(min($ages) / 5) * 5; // Plus petit multiple de 5 inférieur
        $age_max = ceil(max($ages) / 5) * 5;  // Plus grand multiple de 5 supérieur

        // Générer les tranches (catégories)
        $tranches_age = [];
        $intervalle = 5;
        for ($i = $age_min; $i < $age_max; $i += $intervalle) {
            $tranches_age["$i - " . ($i + $intervalle)] = 0;
        }

        // Regrouper les âges dans les tranches
        foreach ($ages as $age) {
            foreach ($tranches_age as $range => &$count) {
                [$min, $max] = explode(' - ', $range);
                if ($age >= $min && $age < $max) {
                    $count++;
                    break;
                }
            }
        }
        //Conversion en JSON
        return [
            'age' => $ages,
            'tranches_age_labels' => json_encode(array_keys($tranches_age)),
            'tranches_age_counts' => json_encode(array_values($tranches_age))
        ];

    }catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        die();
    }
}

function Stat_Sexe($data, $conn, int $id_essai){

    try{
        $sexes = array_column($data, 'Sexe');

        // Préparer les données de sexe pour le camembert
        $sexe_count = array_count_values($sexes); // Exemple : ['H' => 5, 'F' => 3]
  
        return [
            'sexe' => $sexes,
            'sexe_json' => json_encode($sexe_count),
        ];
    }catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        die();
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distribution des Poids, Tailles, Âges et Sexe- Essai Clinique</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href='website.css'>
    <link rel="stylesheet" href= 'navigationBar.css'>
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

        /* Optionnel : style pour les titres */
        h1 {
            text-align: center;
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


<h1>Distribution des Poids, Tailles, Âges et Sexe pour l'Essai ID : <?= htmlspecialchars($id_essai) ?></h1>

<?php 
$data = Stat_data($conn, $id_essai);
$poids_stats = Stat_Poids($data, $conn, $id_essai);
$taille_stats = Stat_Taille($data, $conn, $id_essai);
$age_stats = Stat_Age($data, $conn, $id_essai);
$sexe_stats = Stat_Sexe($data, $conn, $id_essai);
?>

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

</body>
</html>

