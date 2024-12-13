# Projet-Website

Ce projet est une plateforme web développée dans le cadre du projet site web - GB5 BIMB. Il est conçu pour fonctionner en local à l'aide de l'environnement XAMPP.

## Prérequis

Avant de commencer, assurez-vous que les outils suivants sont installés et configurés sur votre machine :

- [XAMPP](https://www.apachefriends.org/index.html) (serveur Apache, MySQL, PHP)
- Un navigateur web moderne
- PHPMyAdmin (généralement inclus avec XAMPP)

## Installation et initialisation du projet

1. **Téléchargement des fichiers :**
   - Téléchargez ou clonez le dossier zip contenant les fichiers du projet, nommé `projet-website`.

2. **Placement des fichiers dans `htdocs` :**
   - Déplacez ou extrayez le dossier `projet-website` directement dans le dossier `htdocs` de votre installation XAMPP.  
     > Par défaut, ce dossier se trouve généralement dans le répertoire d'installation de XAMPP.

3. **Configuration de la base de données :**
   - Ouvrez PHPMyAdmin à l'adresse suivante : `http://localhost/phpmyadmin`.
   - Créez une nouvelle base de données appelée **`website_db`**.
   - Importez le fichier `website_db.sql` dans cette base de données en utilisant l'interface PHPMyAdmin.

4. **Configuration des paramètres de connexion :**
   - Vérifiez les paramètres de connexion à la base de données dans les fichiers PHP du projet (par exemple, `config.php`, ou autre fichier contenant la configuration). 
   - Les paramètres par défaut sont :
     ```php
     $host = 'localhost';
     $dbname = 'website_db';
     $user = 'root';
     $password = '';
     ```

5. **Lancement du projet :**
   - Démarrez XAMPP et activez le serveur Apache et MySQL.
   - Ouvrez votre navigateur et accédez à l'adresse : `http://localhost/projet-website/Homepage.php`.

## Arborescence des fichiers

opt/lampp/htdocs/projet-website

├── Admin 
│   ├── Admin.css 

│   ├── Confirmer_modif.php 

│   ├── Enregistrer_modif.php

│   ├── Fonctions_admin.php

│   ├── Home_Admin.php

│   ├── Liste_entreprises.php

│   ├── Liste_medecins.php

│   ├── Liste_patients.php

│   ├── Modifier_Entreprises.php

│   ├── Modifier_Medecins.php

│   ├── Modifier_Patients.php

│   ├── Supprimer_utilisateur.php

│   └── Validation_en_attente.php

├── Connexion

│   ├── fonctionConnexion.php

│   ├── Form1_connexion.php

│   └── verification1_connexion.php

├── Deconnexion.php

├── essai_indiv.css

├── Essai_individuel.php

├── Essais.php

├── Fonctions_essai.php

├── Fonctions.php

├── Homepage.php

├── hub.php

├── Inscription
│   ├── finalisation_inscription.php
│   ├── fonctionInscription.php
│   ├── Form1_inscription.php
│   ├── Form2_inscription.php
│   ├── verification1_inscription.php
│   └── verification2_inscription.php
├── navigationBar.css
├── Notifications
│   ├── fonction_notif.php
│   ├── Notifications.php
│   ├── Notifications_style.css
│   └── Redirect_notif.php
├── Page_Essai_Individuel
│   ├── Aff_Statistiques.php
│   ├── Fonction_Modif_Essais.php
│   ├── Infos_Patient.php
│   ├── Liste_Patients_Essai.php
│   ├── Modifier_Essais.php
│   ├── Modifier_Patient_Essai.php
│   └── Page_aff_stat.php
├── Page_Mes_Infos
│   ├── Creer_essai.php
│   ├── Fonction_Mes_infos.php
│   ├── Historique_Essais.php
│   ├── Menu_Mes_Infos.php
│   ├── Mes_infos.php
│   └── Page_Creer_Essai.php
├── Pictures
│   ├── defaultPicture.png
│   ├── eyes_close.png
│   ├── letterPicture.png
│   ├── logo.png
│   ├── minilogo.png
│   ├── open_eye.png
│   ├── pictureProfil.png
│   └── subscription.png
├── README.md
├── test_unitaire.php
├── website.css
└── website_db.sql

7 directories, 60 files

## Test unitaire

Le projet inclut des tests unitaires. 

> Les tests unitaires sont exécutés via le fichier `test_unitaire.php`.

*Si des erreurs sont constatés après avoir exécuter plusieurs fois ce fichier, veuillez réinitialiser la BdD.

## Dépendances

Aucune dépendance externe n'est requise, mais le projet utilise les fonctionnalités intégrées de PHP et MySQL pour le développement backend.

## Auteur

Julie Feriau, 
François Martin, 
Angélique Vella, 
Céline Wu 
