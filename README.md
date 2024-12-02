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

SCHÉMA tree à venir 

## Fonctionnalités principales

1. **Gestion des utilisateurs :**
   - Inscription avec validation des rôles (patient, médecin, entreprise).
   - Authentification sécurisée (hachage des mots de passe).

2. **Interface utilisateur dynamique :**
   - [Décrivez brièvement les fonctionnalités ou modules clés, ex. Tableau de bord, etc.]

3. **Validation des formulaires :**
   - Système de validation pour garantir l'intégrité des données utilisateur.

## Test unitaire

Le projet inclut des tests unitaires pour valider les principales fonctions, notamment :
- Vérification des emails existants.
- Vérification de l'âge des utilisateurs.
- Validation des champs selon les rôles.

> Les tests unitaires sont exécutés via le fichier `test_unitaire.php`.

## Dépendances

Aucune dépendance externe n'est requise, mais le projet utilise les fonctionnalités intégrées de PHP et MySQL pour le développement backend.

## Auteur

Julie Feriau, 
François Martin, 
Angélique Vella, 
Céline Wu 
