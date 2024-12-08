<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Modal sans JS</title>
    <style>
        /* Le fond du modal */
        .modal {
            display: none; /* Par défaut, le modal est caché */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4); /* Fond sombre */
            text-align: center;
        }

        /* Contenu du modal */
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
        }

        /* Style pour le bouton de fermeture */
        .close-btn {
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .close-btn:hover,
        .close-btn:focus {
            color: black;
            text-decoration: none;
        }

        /* L'élément checkbox sert de déclencheur */
        .modal-toggle {
            display: none;
        }

        /* Affiche le modal quand le checkbox est coché */
        .modal-toggle:checked + .modal {
            display: block;
        }
        
        /* Style pour l'image de la lettre */
        .dropdown img {
            cursor: pointer;
            width: 50px; /* Ajustez la taille de l'image */
        }
    </style>
</head>
<body>

    <!-- Accès à la messagerie -->
    <div class="dropdown">
        <!-- Lien avec l'image qui agit comme un déclencheur -->
        <label for="modal-toggle">
            <img src="Pictures/letterPicture.png" alt="letterPicture">
        </label>

        <!-- Checkbox pour ouvrir/fermer le modal -->
        <input type="checkbox" id="modal-toggle" class="modal-toggle">

        <!-- Modal -->
        <div id="modal" class="modal">
            <div class="modal-content">
                <h1>Centre de notifications</h1>
                <label for="modal-toggle" class="close-btn">&times;</label>
                <!-- Contenu du modal -->
                <p>Voici le contenu de vos notifications.</p>
            </div>
        </div>
    </div>

</body>
</html>
