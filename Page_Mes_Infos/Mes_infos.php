<?php
session_start();
include("Fonction_Mes_infos.php");


//$id_user =$_SESSION['id_user'];
//$role= $_SESSION['role'];
$id_user = 6;
$id_entreprise= $id_user;

// Récupération des informations de l'utilisateur
$userInfo = getUserInfo($conn, $id_user);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mise à jour des informations de l'utilisateur
    $success = updateUserInfo($conn, $id_user);
    if ($success) {
        $userInfo = getUserInfo($conn, $id_user); // Actualiser les données affichées
        $message = "Vos informations ont été mises à jour avec succès.";
    } else {
        $message = "Erreur lors de la mise à jour des informations.";
    }
}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil utilisateur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .message {
            color: green;
            margin: 10px 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Profil utilisateur</h1>
    <?php if (isset($message)) : ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if ($userInfo) : ?>
        <form method="post">
            <?php foreach ($userInfo as $key => $value): ?>
                <?php if ($key === 'Id_Patients' || $key === 'Id_medecin' || $key === 'Id_entreprise') continue; ?>
                <label for="<?= $key ?>"><?= ucfirst(str_replace('_', ' ', $key)) ?> :</label>
                <input type="text" id="<?= $key ?>" name="<?= $key ?>" value="<?= htmlspecialchars($value) ?>">
            <?php endforeach; ?>
            <button type="submit">Modifier</button>
        </form>
    <?php else: ?>
        <p>Aucune information trouvée pour cet utilisateur.</p>
    <?php endif; ?>
</body>
</html>
