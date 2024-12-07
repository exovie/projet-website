-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 30, 2024 at 05:03 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `website_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `DESTINATAIRE`
--

CREATE TABLE `DESTINATAIRE` (
  `Id_notif` int(11) NOT NULL,
  `Id_destinataire` int(11) NOT NULL,
  `Statut_notification` varchar(50) NOT NULL COMMENT 'ouvert, pas ouvert'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `DESTINATAIRE`
--

INSERT INTO `DESTINATAIRE` (`Id_notif`, `Id_destinataire`, `Statut_notification`) VALUES
(4, 84, 'Ouvert'),
(5, 49, 'Ouvert'),
(6, 57, 'Ouvert'),
(7, 89, 'Ouvert'),
(8, 48, 'Ouvert'),
(9, 33, 'Ouvert'),
(10, 53, 'Ouvert'),
(11, 85, 'Ouvert'),
(12, 41, 'Ouvert'),
(22, 70, 'Ouvert'),
(23, 72, 'Ouvert'),
(24, 73, 'Ouvert'),
(25, 80, 'Ouvert'),
(26, 83, 'Ouvert'),
(27, 26, 'Ouvert'),
(28, 38, 'Ouvert'),
(29, 94, 'Ouvert'),
(30, 31, 'Ouvert'),
(31, 35, 'Ouvert'),
(33, 69, 'Ouvert'),
(35, 77, 'Ouvert'),
(36, 64, 'Ouvert'),
(39, 51, 'Ouvert'),
(40, 78, 'Ouvert'),
(42, 81, 'Ouvert'),
(43, 46, 'Ouvert'),
(44, 33, 'Ouvert'),
(45, 36, 'Ouvert'),
(46, 55, 'Ouvert'),
(47, 53, 'Ouvert'),
(48, 95, 'Ouvert'),
(49, 52, 'Ouvert'),
(50, 66, 'Ouvert'),
(51, 92, 'Ouvert'),
(52, 41, 'Ouvert'),
(53, 65, 'Ouvert'),
(55, 87, 'Ouvert'),
(66, 63, 'Ouvert'),
(67, 100, 'Ouvert'),
(68, 76, 'Ouvert'),
(69, 29, 'Ouvert'),
(70, 75, 'Ouvert'),
(71, 40, 'Ouvert'),
(72, 39, 'Ouvert'),
(73, 85, 'Ouvert'),
(74, 30, 'Ouvert'),
(75, 42, 'Ouvert'),
(87, 7, 'Ouvert'),
(99, 11, 'Ouvert'),
(102, 14, 'Non ouvert'),
(102, 22, 'Non ouvert'),
(104, 14, 'Non ouvert'),
(104, 22, 'Non ouvert'),
(105, 11, 'Ouvert'),
(112, 14, 'Non ouvert'),
(112, 15, 'Non ouvert'),
(112, 23, 'Non ouvert'),
(114, 19, 'Non ouvert'),
(116, 20, 'Non ouvert'),
(117, 21, 'Ouvert'),
(117, 23, 'Non ouvert'),
(118, 21, 'Non ouvert'),
(118, 23, 'Ouvert'),
(120, 10, 'Ouvert'),
(120, 24, 'Non ouvert'),
(121, 10, 'Ouvert'),
(121, 19, 'Non ouvert'),
(122, 5, 'Ouvert'),
(122, 16, 'Non ouvert');

-- --------------------------------------------------------

--
-- Table structure for table `ENTREPRISES`
--

CREATE TABLE `ENTREPRISES` (
  `Id_entreprise` int(11) NOT NULL,
  `Nom_entreprise` varchar(255) NOT NULL,
  `Telephone` varchar(15) NOT NULL,
  `Profile_picture` blob DEFAULT NULL,
  `Siret` bigint(11) NOT NULL,
  `Verif_inscription` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ENTREPRISES`
--

INSERT INTO `ENTREPRISES` (`Id_entreprise`, `Nom_entreprise`, `Telephone`, `Profile_picture`, `Siret`, `Verif_inscription`) VALUES
(5, 'BioHealth Labs', '7782326054', '', 30294292554645, 1),
(6, 'NextGen Robotics', '2286060683', '', 60866228756114, 1),
(7, 'AquaPure Systems', '3612582695', '', 90094511572613, 1),
(8, 'UrbanPulse Technologies', '7867526605', '', 21107436387254, 1),
(9, 'Quantum Dynamics', '5751376468', '', 41834918436514, 1),
(10, 'BlueSky Innovations', '9038931626', '', 40735153784638, 1),
(11, 'GreenWave Industries', '3014606655', '', 12750055895775, 1),
(12, 'TechNova Solutions', '6446476253', '', 50990608789862, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ESSAIS_CLINIQUES`
--

CREATE TABLE `ESSAIS_CLINIQUES` (
  `Id_essai` int(11) NOT NULL,
  `Titre` varchar(255) NOT NULL,
  `Contexte` text DEFAULT NULL,
  `Objectif_essai` text DEFAULT NULL,
  `Design_etude` text DEFAULT NULL,
  `Critere_evaluation` text DEFAULT NULL,
  `Resultats_attendus` text DEFAULT NULL,
  `Date_lancement` date DEFAULT NULL,
  `Date_fin` date DEFAULT NULL,
  `Date_creation` date NOT NULL,
  `Id_essai_precedent` int(11) DEFAULT NULL,
  `Statut` varchar(50) NOT NULL COMMENT 'En attente de medcin, recrutement, En cours, suspendu, fini',
  `Id_entreprise` int(11) NOT NULL,
  `Nb_medecins` int(11) NOT NULL,
  `Nb_patients` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ESSAIS_CLINIQUES`
--

INSERT INTO `ESSAIS_CLINIQUES` (`Id_essai`, `Titre`, `Contexte`, `Objectif_essai`, `Design_etude`, `Critere_evaluation`, `Resultats_attendus`, `Date_lancement`, `Date_fin`, `Date_creation`, `Id_essai_precedent`, `Statut`, `Id_entreprise`, `Nb_medecins`, `Nb_patients`) VALUES
(0, 'Évaluation de la sécurité de la crème MIM\'OSAU dans le traitement des champignons podaux - Phase I', 'Les infections fongiques des pieds, ou mycoses podales, représentent un problème de santé commun mais souvent difficile à traiter efficacement. La crème MIM\'OSAU a été formulée pour offrir une solution innovante, avec des propriétés antifongiques puissantes visant à éradiquer ces infections tout en respectant l\'équilibre cutané.', 'Évaluer la sécurité et la tolérance de la crème MIM\'OSAU chez des volontaires sains.', 'Étude ouverte, non randomisée.\nPopulation cible : Adultes de 18 à 65 ans en bonne santé.\nDurée de l\'étude : 4 semaines d\'application quotidienne de la crème sur une petite zone de la peau.\nVisites de suivi hebdomadaires pour évaluer la tolérance et les effets secondaires.', 'Critère principal : Absence d\'effets secondaires graves.\nCritères secondaires : Irritation cutanée, rougeurs, et autres réactions locales.', 'Bonne tolérance de la crème MIM\'OSAU sans effets secondaires graves, permettant de passer à la phase II.', '2019-09-01', '2020-12-30', '2019-02-01', NULL, 'Termine', 7, 2, 10),
(1, 'Évaluation de l\'efficacité de la crème MIM\'OSAU dans le traitement des champignons podaux - Phase II', 'Les infections fongiques des pieds, ou mycoses podales, représentent un problème de santé commun mais souvent difficile à traiter efficacement. La crème MIM\'OSAU a été formulée pour offrir une solution innovante, avec des propriétés antifongiques puissantes visant à éradiquer ces infections tout en respectant l\'équilibre cutané.', 'Évaluer l\'efficacité du traitement et continuer à surveiller sa sécurité.', 'Étude randomisée, en double aveugle, contrôlée par placebo.\nPopulation cible : Adultes de 18 à 65 ans présentant des signes cliniques de mycoses podales modérées à sévères.\nDurée de l\'étude : 30 semaines d\'application quotidienne de la crème sur la zone affectée.\nVisites de suivi hebdomadaires pour évaluer l\'évolution des symptômes et la tolérance au traitement.', 'Critère principal : Réduction de la surface et de la sévérité des lésions fongiques à la fin de l\'essai.\nCritères secondaires : Amélioration de la qualité de vie des patients (évaluée par un questionnaire validé) et absence d\'effets secondaires significatifs.', 'Efficacité supérieure au placebo : Une diminution notable des symptômes chez les participants utilisant la crème MIM\'OSAU, démontrant une efficacité supérieure au placebo et une bonne tolérance.', '2021-07-01', '2023-05-01', '2021-03-01', 0, 'Termine', 7, 3, 10),
(2, 'Evaluation de l\'efficacité de la crème MIM\'OSAU dans le traitement des champignons podaux - Phase III', 'Les infections fongiques des pieds, ou mycoses podales, représentent un problème de santé commun mais souvent difficile à traiter efficacement. La crème MIM\'OSAU a été formulée pour offrir une solution innovante, avec des propriétés antifongiques puissantes visant à éradiquer ces infections tout en respectant l\'équilibre cutané.', 'Evaluer l\'efficacité et la tolérance de la crème MIM\'OSAU par rapport à un placebo dans le traitement des mycoses podales.', '    Type d\'étude : Étude randomisée, en double aveugle, contrôlée par placebo.\r\n    Population cible : Adultes de 18 à 65 ans présentant des signes cliniques de mycoses podales modérées à sévères.\r\n    Durée de l\'étude : 3 semaines d\'application quotidienne.\r\n    Procédure : Les participants appliqueront la crème MIM\'OSAU ou une crème placebo une fois par jour sur la zone affectée. Des visites de suivi seront effectuées chaque semaine pour évaluer l\'évolution des symptômes et la tolérance au traitement.', 'Critère principal : Réduction de la surface et de la sévérité des lésions fongiques à la fin de l\'essai.\r\n\r\nCritères secondaires : Amélioration de la qualité de vie des patients (évaluée par un questionnaire validé) et absence d\'effets secondaires significatifs.', 'Une diminution notable des symptômes chez les participants utilisant la crème MIM\'OSAU, démontrant une efficacité supérieure au placebo et une bonne tolérance.\r\n\r\nCet essai clinique permettra de valider l\'utilisation de la crème MIM\'OSAU comme traitement fiable pour les mycoses podales, contribuant à élargir les options thérapeutiques disponibles pour les patients.', NULL, NULL, '2024-08-01', 1, 'Recrutement', 7, 3, 10),
(3, 'Étude clinique sur l\'efficacité de la méthode pyramidale dans la gestion de l\'anxiété - Phase II', 'L\'anxiété touche une part importante de la population mondiale, avec un impact significatif sur la qualité de vie. La méthode pyramidale, une approche innovante mêlant techniques de respiration contrôlée, visualisation guidée, et méditation structurée, le tout, à l\'intérieur d\'une pyramide, a montré des résultats prometteurs dans des études préliminaires.\r\n\r\n', 'Evaluer l\'efficacité et la faisabilité de la méthode pyramidale en comparaison à une intervention standard (éducation à la gestion de l\'anxiété) pour réduire les symptômes d\'anxiété modérée à sévère.', '\"Type d\'étude : Étude randomisée, en simple aveugle, avec groupe contrôle actif.\r\nPopulation cible : Adultes âgés de 18 à 65 ans présentant un diagnostic d\'anxiété généralisée ou sociale (selon les critères du DSM-5). \r\nDurée de l\'étude : 58 semaines, avec des évaluations hebdomadaires. \r\nProcédure : Les participants seront répartis aléatoirement en deux groupes : Groupe 1 : Suivi de la méthode pyramidale (sessions bihebdomadaires de 60 minutes à l\'intérieur d\'une pyramide). Groupe 2 : Programme d\'éducation standard sur la gestion de l\'anxiété (sessions équivalentes). Les participants seront évalués avant, pendant, et après l\'intervention pour mesurer l\'évolution de leurs symptômes.\"', '\"Critère principal : Réduction du score global d\'anxiété (échelle GAD-7) après 8 semaines.\r\n\r\nCritères secondaires : Amélioration de la qualité de vie (évaluée par l\'échelle SF-36). Niveau de bien-être perçu (mesuré par une échelle visuelle analogique). Absence d\'effets indésirables.\"', 'Une diminution significative des scores d\'anxiété dans le groupe méthode pyramidale, ainsi qu\'une amélioration notable de la qualité de vie, démontrant son efficacité en tant qu\'intervention non pharmacologique. Cet essai permettra d\'élargir les options thérapeutiques pour les patients souffrant d\'anxiété, en proposant une alternative accessible et durable.', NULL, NULL, '2024-09-15', NULL, 'En attente', 5, 2, 10),
(4, 'Étude des bienfaits de la danse contre l\'alopécie androgénétique- Phase I', 'La calvitie, fréquente chez les hommes, a des impacts physiques et psychologiques. Des hypothèses suggèrent que l\'exercice physique, en particulier la danse, pourrait stimuler la circulation sanguine du cuir chevelu et aider à prévenir la perte de cheveux.', 'Évaluer les effets de la danse sur la croissance des cheveux et la prévention de la calvitie chez les adultes souffrant d\'alopécie modérée', 'Type : Étude randomisée, contrôlée, avec groupe contrôle inactif. \r\nPopulation : Adultes de 18 à 50 ans avec alopécie modérée.\r\nProcédure : Groupe 1 : Séances de danse aérobique, trois fois par semaine, pendant 6 mois. Groupe 2 : Aucun exercice physique, conseils généraux pour l\'hygiène capillaire.', 'Critère Principal : Augmentation de la densité capillaire mesurée par phototrichogramme. \r\n\r\nCritères Secondaires : Satisfaction personnelle et bien-être général des participants, ainsi que la circulation sanguine du cuir chevelu.', 'On s\'attend à ce que le groupe danse montre une amélioration de la densité capillaire et une meilleure satisfaction par rapport à leur image corporelle.', '2019-10-10', '2021-11-08', '2019-06-01', NULL, 'Termine', 6, 2, 10),
(5, 'Vider un poppers dans une gourde de boisson pétillante: une fausse mauvaise idée? - Phase I', 'La recherche de nouvelles expériences sensorielles et récréatives pousse certains à explorer des combinaisons audacieuses. Mélanger des poppers avec une boisson pétillante, puis inhaler les vapeurs, est une pratique émergente qui suscite curiosité et controverse. Ce phénomène intrigue par son potentiel à offrir des effets amplifiés, tout en soulevant des questions de sécurité.', 'Évaluer les sensations perçues et les éventuels risques associés à l’inhalation de vapeurs issues d’un mélange de poppers et de boisson gazeuse.', 'Type d’étude : Étude exploratoire, en ouvert, de phase I. \r\nPopulation cible : Adultes entre 18 et 40 ans, familiers avec l’usage récréatif des poppers. \r\nProcédure : Les participants prépareront un mélange de poppers et de boisson gazeuse selon une méthode standardisée, puis inhaleront les vapeurs sous supervision. Les réactions seront mesurées en temps réel, incluant des descriptions subjectives des sensations (euphorie, intensité des effets, etc.) et des données physiologiques (rythme cardiaque, saturation en oxygène).', 'Critère principal : Intensité perçue des effets (mesurée via des échelles visuelles analogiques).\r\n\r\nCritères secondaires : Apparition de symptômes secondaires (étourdissements, nausées) et réactions physiologiques anormales.', 'Une documentation claire des sensations positives potentielles, tout en identifiant les limites de sécurité. L’étude vise à fournir un cadre scientifique pour évaluer cette pratique et, potentiellement, guider ses adeptes vers un usage informé et modéré.', '2024-02-01', NULL, '2023-06-08', NULL, 'En cours', 8, 2, 10),
(6, 'Étude sur l\'efficacité des traitements combinés en oncologie prostatique pour les patients atteints de cancer de la prostate avancé - Phase II', 'Le cancer de la prostate est le cancer le plus fréquent chez les hommes, avec plus de 50 000 nouveaux cas par an en France. Bien que de nombreux traitements soient disponibles, le cancer de la prostate avancé reste un défi majeur en oncologie. Les traitements combinés, incluant la chimiothérapie, la radiothérapie et l\'hormonothérapie, pourraient offrir de nouvelles perspectives pour améliorer les résultats cliniques et la qualité de vie des patients.', 'Evaluer l\'efficacité des traitements combinés en oncologie prostatique pour les patients atteints de cancer de la prostate avancé. L\'étude vise à déterminer si l\'association de plusieurs modalités thérapeutiques peut améliorer la survie globale et réduire les symptômes liés à la maladie.', 'Type d\'étude : Étude randomisée, en double aveugle, contrôlée par placebo.\r\nPopulation cible : Hommes de 50 à 75 ans atteints de cancer de la prostate avancé.\r\nDurée de l\'étude : 24 mois.\r\nProcédure : Les participants seront répartis en deux groupes. Le groupe expérimental recevra un traitement combiné de chimiothérapie, radiothérapie et hormonothérapie, tandis que le groupe témoin recevra un traitement standard. Les traitements seront administrés selon un protocole défini sur une période de 24 mois.', 'Critère principal : Survie globale des patients, mesurée à 24 mois.\r\n\r\nCritères secondaires : Réduction des symptômes (douleurs, troubles urinaires), qualité de vie évaluée par le questionnaire SF-36, et taux de PSA (Antigène Prostatique Spécifique) dans le sang.', 'Il est attendu que les patients du groupe expérimental montrent une amélioration significative de la survie globale par rapport au groupe témoin. Une réduction des symptômes et une amélioration de la qualité de vie sont également attendues. Ces résultats pourraient valider l\'utilisation des traitements combinés comme approche efficace pour le cancer de la prostate avancé.', '2023-01-15', NULL, '2022-05-18', NULL, 'En cours', 9, 1, 10),
(7, 'Évaluation de la sécurité du médicament XEN-101 dans le traitement de l\'hypertension artérielle- Phase I', 'L\'hypertension artérielle est une condition médicale courante qui peut entraîner des complications graves si elle n\'est pas traitée. Le médicament XEN-101 a été développé pour offrir une nouvelle option thérapeutique.', 'Évaluer la sécurité et la tolérance du médicament XEN-101 chez des volontaires sains.', 'Étude ouverte, non randomisée.\r\nPopulation cible : Adultes de 18 à 65 ans en bonne santé.\r\nDurée de l\'étude : 4 semaines de traitement quotidien.\r\nVisites de suivi hebdomadaires pour évaluer la tolérance et les effets secondaires.', 'Critère principal : Absence d\'effets secondaires graves.\r\nCritères secondaires : Mesure de la pression artérielle et autres paramètres vitaux.', 'Bonne tolérance du médicament XEN-101 sans effets secondaires graves, permettant de passer à la phase II', NULL, NULL, '2024-06-20', NULL, 'En attente', 10, 2, 10),
(8, 'Évaluation de l\'efficacité du vaccin VAX-200 dans la prévention de la grippe saisonnière- Phase II', 'La grippe saisonnière cause des millions de cas de maladie chaque année. Le vaccin VAX-200 a été développé pour offrir une protection efficace contre les souches courantes du virus de la grippe.', 'Évaluer l\'efficacité du vaccin et continuer à surveiller sa sécurité.', 'Étude randomisée, en double aveugle, contrôlée par placebo.\r\nPopulation cible : Adultes de 18 à 65 ans.\r\nDurée de l\'étude : 6 mois de suivi après la vaccination.\r\nVisites de suivi mensuelles pour évaluer l\'incidence de la grippe et les effets secondaires.', 'Critère principal : Réduction de l\'incidence de la grippe chez les participants vaccinés.\r\nCritères secondaires : Tolérance du vaccin et réponse immunitaire.', 'Efficacité supérieure au placebo : Réduction significative de l\'incidence de la grippe et bonne tolérance du vaccin.', '2023-04-01', NULL, '2022-09-16', NULL, 'En cours', 11, 3, 8),
(9, 'Évaluation de l\'efficacité du médicament ANT-300 dans le traitement de la dépression majeure - Phase III', 'La dépression majeure est une maladie mentale courante qui affecte des millions de personnes dans le monde. Le médicament ANT-300 a été développé pour offrir une nouvelle option thérapeutique.', 'Confirmer l\'efficacité du traitement, surveiller les effets secondaires, comparer avec les traitements courants et collecter des informations qui permettront l\'utilisation sûre du traitement.', 'Étude randomisée, en double aveugle, contrôlée par placebo.\r\nPopulation cible : Adultes de 18 à 65 ans.\r\nDurée de l\'étude : 6 mois de suivi pendant la prise.', 'Critère principal : Réduction des scores de dépression sur une échelle validée.\r\nCritères secondaires : Amélioration de la qualité de vie et absence d\'effets secondaires significatifs.', 'Une diminution notable des symptômes de dépression chez les participants utilisant le médicament ANT-300, démontrant une efficacité supérieure au placebo et une bonne tolérance.', '2017-02-01', '2020-12-01', '2016-07-05', NULL, 'Termine', 12, 2, 9),
(10, 'Évaluation de l\'efficacité préliminaire du traitement génique GEN-500 dans la dystrophie musculaire - Phase II', 'La dystrophie musculaire est une maladie génétique qui cause une dégénérescence progressive des muscles. Le traitement génique GEN-500 a été développé pour offrir une nouvelle approche thérapeutique.', 'Évaluer l\'efficacité préliminaire du traitement et continuer à surveiller sa sécurité.', 'Étude randomisée, en double aveugle, contrôlée par placebo.\r\nPopulation cible : Adultes de 18 à 65 ans diagnostiqués avec une dystrophie musculaire.\r\nDurée de l\'étude : 12 semaines de traitement.\r\nVisites de suivi hebdomadaires pour évaluer l\'évolution des symptômes et la tolérance au traitement.', 'Critère principal : Réduction de la dégénérescence musculaire.\r\nCritères secondaires : Amélioration de la force musculaire et qualité de vie des patients.', 'Indications préliminaires d\'efficacité avec une bonne tolérance, permettant de passer à la phase III', '2019-07-01', '2020-06-01', '2019-03-03', NULL, 'Termine', 11, 2, 8),
(11, 'Évaluation de l\'efficacité du traitement génique GEN-500 dans la dystrophie musculaire - Phase III', 'La dystrophie musculaire est une maladie génétique qui cause une dégénérescence progressive des muscles. Le traitement génique GEN-500 a été développé pour offrir une nouvelle approche thérapeutique.', 'Confirmer l\'efficacité du traitement, surveiller les effets secondaires, comparer avec les traitements courants et collecter des informations qui permettront l\'utilisation sûre du traitement.', 'Étude randomisée, en double aveugle, contrôlée par placebo.\r\nPopulation cible : Adultes de 18 à 65 ans diagnostiqués avec une dystrophie musculaire.\r\nDurée de l\'étude : 24 semaines de traitement.\r\nVisites de suivi hebdomadaires pour évaluer l\'évolution des symptômes et la tolérance au traitement.', 'Critère principal : Réduction significative de la dégénérescence musculaire.\r\nCritères secondaires : Amélioration de la force musculaire, qualité de vie des patients et absence d\'effets secondaires significatifs.', 'Confirmation de l\'efficacité et de la sécurité du traitement génique GEN-500, démontrant une amélioration notable par rapport au placebo et aux traitements courants.', NULL, NULL, '2024-04-01', 10, 'Recrutement', 11, 3, 10),
(12, 'Évaluation de l\'efficacité du médicament NEURO-200 dans le traitement de la maladie d\'Alzheimer - Phase II', 'La maladie d\'Alzheimer est une maladie neurodégénérative progressive qui affecte des millions de personnes dans le monde. Le médicament NEURO-200 a été développé pour ralentir la progression de la maladie et améliorer la qualité de vie des patients.', 'Évaluer l\'efficacité préliminaire du médicament NEURO-200 et continuer à surveiller sa sécurité.', 'Étude randomisée, en double aveugle, contrôlée par placebo.\r\nPopulation cible : Adultes de 50 à 85 ans diagnostiqués avec une maladie d\'Alzheimer légère à modérée.\r\nDurée de l\'étude : 24 semaines de traitement quotidien.\r\nVisites de suivi mensuelles pour évaluer l\'évolution des symptômes et la tolérance au traitement.', 'Critère principal : Amélioration des scores cognitifs sur une échelle validée.\r\nCritères secondaires : Amélioration de la qualité de vie des patients et absence d\'effets secondaires significatifs.', 'Indications préliminaires d\'efficacité avec une bonne tolérance, permettant de passer à la phase III.', NULL, NULL, '2024-11-10', NULL, 'En attente', 10, 2, 10);

-- --------------------------------------------------------

--
-- Table structure for table `MEDECINS`
--

CREATE TABLE `MEDECINS` (
  `Id_medecin` int(11) NOT NULL,
  `Nom` varchar(255) NOT NULL,
  `Prenom` varchar(255) NOT NULL,
  `Specialite` varchar(255) NOT NULL,
  `Telephone` varchar(15) NOT NULL,
  `Matricule` bigint(11) NOT NULL,
  `Profile_picture` blob DEFAULT NULL,
  `Statut_inscription` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `MEDECINS`
--

INSERT INTO `MEDECINS` (`Id_medecin`, `Nom`, `Prenom`, `Specialite`, `Telephone`, `Matricule`, `Profile_picture`, `Statut_inscription`) VALUES
(13, 'Simpson', 'Daniel', 'Oncologie prostatique', '5419219709', 93732213325, '', 1),
(14, 'Duncan', 'Christopher', 'Hématologie', '8646647372', 37370229073, '', 1),
(15, 'Schultz', 'Jennifer', 'Génétique médicale', '6679008914', 44583386929, '', 1),
(16, 'Mcgrath', 'Theodore', 'Phamacologie clinique', '2477551563', 36934957567, '', 1),
(17, 'Watson', 'Sandra', 'Endocrinologie', '7290469575', 93777509616, '', 1),
(18, 'Crawford', 'Mary', 'Data solver', '7349606574', 37753040566, '', 1),
(19, 'Garcia', 'Angel', 'Immunologie ', '2531450995', 30148664127, '', 1),
(20, 'Hauze', 'Mike', 'Mycologie', '3628293731', 76200587194, '', 1),
(21, 'Raoult', 'Didier', 'Virologie de Wuhan', '8441427696', 10003343455, '', 1),
(22, 'Cive', 'Jean', 'Dentiste', '1970984413', 10003445839, '', 1),
(23, 'Vigeant', 'Valentin', 'Bio-informatique gynécologique', '9784558709', 10003373748, '', 1),
(24, 'Grataloup', 'Guillaume', 'Chimiste en herbes médicinales', '4775597092', 10003405667, '', 1),
(25, 'Topedik', 'Aure', 'Modélisateur en podologie', '9466897264', 10009373221, '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `MEDECIN_ESSAIS`
--

CREATE TABLE `MEDECIN_ESSAIS` (
  `Id_medecin` int(11) NOT NULL,
  `Id_essai` int(11) NOT NULL,
  `Statut_medecin` varchar(50) NOT NULL COMMENT 'sollicite, en attente, actif, termine, retire '
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `MEDECIN_ESSAIS`
--

INSERT INTO `MEDECIN_ESSAIS` (`Id_medecin`, `Id_essai`, `Statut_medecin`) VALUES
(13, 0, 'Termine'),
(13, 8, 'Actif'),
(14, 2, 'Actif'),
(14, 6, 'Actif'),
(14, 11, 'Actif'),
(15, 2, 'Actif'),
(15, 8, 'Actif'),
(16, 1, 'Termine'),
(16, 3, 'En attente'),
(17, 1, 'Abandon'),
(17, 11, 'Abandon'),
(18, 1, 'Termine'),
(18, 9, 'Termine'),
(19, 1, 'Termine'),
(19, 7, 'Sollicite'),
(19, 12, 'En attente'),
(20, 4, 'Termine'),
(20, 9, 'Termine'),
(20, 11, 'Sollicite'),
(21, 0, 'Termine'),
(21, 3, 'Sollicite'),
(21, 10, 'Termine'),
(22, 5, 'Actif'),
(22, 10, 'Termine'),
(22, 11, 'Actif'),
(23, 2, 'Actif'),
(23, 8, 'Abandon'),
(23, 12, 'Sollicite'),
(24, 4, 'Termine'),
(24, 7, 'En attente'),
(25, 5, 'Actif');

-- --------------------------------------------------------

--
-- Table structure for table `NOTIFICATION`
--

CREATE TABLE `NOTIFICATION` (
  `Id_notif` int(11) NOT NULL,
  `CodeNotif` varchar(255) NOT NULL,
  `Id_Essai` int(11) DEFAULT NULL,
  `Date_Notif` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `NOTIFICATION`
--

INSERT INTO `NOTIFICATION` (`Id_notif`, `CodeNotif`, `Id_Essai`, `Date_Notif`) VALUES
(0, '2', 9, '2016-07-04 22:00:00'),
(1, '5', 9, '2016-07-09 22:00:00'),
(2, '5', 9, '2016-07-30 22:00:00'),
(3, '7', 9, '2016-07-30 22:00:00'),
(4, '20', 9, '2016-08-31 22:00:00'),
(5, '20', 9, '2016-09-16 22:00:00'),
(6, '20', 9, '2016-10-02 22:00:00'),
(7, '20', 9, '2016-10-18 22:00:00'),
(8, '20', 9, '2016-11-03 23:00:00'),
(9, '20', 9, '2016-11-19 23:00:00'),
(10, '20', 9, '2016-12-05 23:00:00'),
(11, '20', 9, '2016-12-21 23:00:00'),
(12, '20', 9, '2017-01-06 23:00:00'),
(13, '15', 9, '2017-01-31 23:00:00'),
(14, '2', 0, '2019-01-31 23:00:00'),
(15, '5', 0, '2019-02-28 23:00:00'),
(16, '2', 10, '2019-03-02 23:00:00'),
(17, '5', 10, '2019-03-25 23:00:00'),
(18, '5', 10, '2019-03-29 23:00:00'),
(19, '7', 10, '2019-03-29 23:00:00'),
(20, '5', 0, '2019-03-31 22:00:00'),
(21, '7', 0, '2019-03-31 22:00:00'),
(22, '20', 0, '2019-04-14 22:00:00'),
(23, '20', 10, '2019-04-14 22:00:00'),
(24, '20', 10, '2019-04-23 22:00:00'),
(25, '20', 0, '2019-04-29 22:00:00'),
(26, '20', 10, '2019-05-02 22:00:00'),
(27, '20', 10, '2019-05-11 22:00:00'),
(28, '20', 0, '2019-05-14 22:00:00'),
(29, '20', 10, '2019-05-20 22:00:00'),
(30, '20', 0, '2019-05-29 22:00:00'),
(31, '20', 10, '2019-05-29 22:00:00'),
(32, '2', 4, '2019-05-31 22:00:00'),
(33, '20', 10, '2019-06-07 22:00:00'),
(34, '5', 4, '2019-06-09 22:00:00'),
(35, '20', 0, '2019-06-13 22:00:00'),
(36, '20', 10, '2019-06-16 22:00:00'),
(37, '5', 4, '2019-06-23 22:00:00'),
(38, '7', 4, '2019-06-23 22:00:00'),
(39, '20', 0, '2019-06-28 22:00:00'),
(40, '20', 4, '2019-06-29 22:00:00'),
(41, '15', 10, '2019-06-30 22:00:00'),
(42, '20', 4, '2019-07-06 22:00:00'),
(43, '20', 4, '2019-07-13 22:00:00'),
(44, '20', 0, '2019-07-13 22:00:00'),
(45, '20', 4, '2019-07-20 22:00:00'),
(46, '20', 4, '2019-07-27 22:00:00'),
(47, '20', 0, '2019-07-28 22:00:00'),
(48, '20', 4, '2019-08-03 22:00:00'),
(49, '20', 4, '2019-08-10 22:00:00'),
(50, '20', 0, '2019-08-12 22:00:00'),
(51, '20', 4, '2019-08-17 22:00:00'),
(52, '20', 0, '2019-08-19 22:00:00'),
(53, '20', 4, '2019-08-24 22:00:00'),
(54, '15', 0, '2019-08-31 22:00:00'),
(55, '20', 4, '2019-08-31 22:00:00'),
(56, '15', 4, '2019-10-09 22:00:00'),
(57, '14', 10, '2020-05-31 22:00:00'),
(58, '14', 9, '2020-11-30 23:00:00'),
(59, '14', 0, '2020-12-29 23:00:00'),
(60, '2', 1, '2021-02-28 23:00:00'),
(61, '5', 1, '2021-03-03 23:00:00'),
(62, '5', 1, '2021-03-08 23:00:00'),
(63, '5', 1, '2021-03-09 23:00:00'),
(64, '5', 1, '2021-03-17 23:00:00'),
(65, '7', 1, '2021-03-17 23:00:00'),
(66, '20', 1, '2021-03-19 23:00:00'),
(67, '20', 1, '2021-03-28 22:00:00'),
(68, '20', 1, '2021-04-09 22:00:00'),
(69, '20', 1, '2021-04-11 22:00:00'),
(70, '20', 1, '2021-04-16 22:00:00'),
(71, '20', 1, '2021-04-25 22:00:00'),
(72, '20', 1, '2021-05-09 22:00:00'),
(73, '20', 1, '2021-05-23 22:00:00'),
(74, '20', 1, '2021-05-31 22:00:00'),
(75, '20', 1, '2021-06-06 22:00:00'),
(76, '15', 1, '2021-06-30 22:00:00'),
(77, '6', 1, '2021-07-31 22:00:00'),
(78, '14', 4, '2021-11-07 23:00:00'),
(79, '2', 6, '2022-05-17 22:00:00'),
(80, '5', 6, '2022-06-15 22:00:00'),
(81, '7', 6, '2022-06-15 22:00:00'),
(82, '2', 8, '2022-09-15 22:00:00'),
(83, '5', 8, '2022-09-19 22:00:00'),
(84, '5', 8, '2022-10-29 22:00:00'),
(85, '5', 8, '2022-12-09 23:00:00'),
(86, '7', 8, '2022-12-09 23:00:00'),
(87, '6', 1, '2022-12-31 23:00:00'),
(88, '15', 6, '2023-01-14 23:00:00'),
(89, '15', 8, '2023-03-31 22:00:00'),
(90, '14', 1, '2023-04-30 22:00:00'),
(91, '2', 5, '2023-06-07 22:00:00'),
(92, '5', 5, '2023-06-30 22:00:00'),
(93, '5', 5, '2023-07-13 22:00:00'),
(94, '7', 5, '2023-07-13 22:00:00'),
(95, '15', 5, '2024-01-31 23:00:00'),
(96, '2', 11, '2024-03-31 22:00:00'),
(97, '5', 11, '2024-04-19 22:00:00'),
(98, '5', 11, '2024-05-11 22:00:00'),
(99, '6', 11, '2024-05-14 22:00:00'),
(100, '5', 11, '2024-05-18 22:00:00'),
(101, '7', 11, '2024-05-18 22:00:00'),
(102, '10', 11, '2024-06-14 22:00:00'),
(103, '2', 7, '2024-06-19 22:00:00'),
(104, '10', 11, '2024-06-28 22:00:00'),
(105, '6', 8, '2024-07-07 22:00:00'),
(106, '2', 2, '2024-07-31 22:00:00'),
(107, '5', 2, '2024-08-09 22:00:00'),
(108, '5', 7, '2024-08-16 22:00:00'),
(109, '5', 2, '2024-08-17 22:00:00'),
(110, '5', 2, '2024-08-19 22:00:00'),
(111, '7', 2, '2024-08-19 22:00:00'),
(112, '10', 2, '2024-08-29 22:00:00'),
(113, '2', 3, '2024-09-14 22:00:00'),
(114, '3', 7, '2024-09-14 22:00:00'),
(115, '5', 3, '2024-09-17 22:00:00'),
(116, '3', 11, '2024-09-18 22:00:00'),
(117, '3', 12, '2024-09-22 22:00:00'),
(118, '3', 3, '2024-09-26 22:00:00'),
(119, '5', 7, '2024-09-29 22:00:00'),
(120, '19', 7, '2024-09-30 22:00:00'),
(121, '19', 12, '2024-10-04 22:00:00'),
(122, '19', 3, '2024-10-08 22:00:00'),
(123, '5', 3, '2024-10-19 22:00:00'),
(124, '2', 12, '2024-11-09 23:00:00'),
(125, '5', 12, '2024-11-14 23:00:00'),
(126, '5', 12, '2024-11-19 23:00:00'),
(127, '5', 11, '2024-11-26 23:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `PATIENTS`
--

CREATE TABLE `PATIENTS` (
  `Id_patient` int(11) NOT NULL,
  `Nom` varchar(255) NOT NULL,
  `Prenom` varchar(255) NOT NULL,
  `Date_naissance` date NOT NULL,
  `Sexe` varchar(1) NOT NULL COMMENT 'M ou F',
  `Telephone` varchar(15) NOT NULL,
  `Profile_picture` blob DEFAULT NULL,
  `Taille` int(11) DEFAULT NULL,
  `Poids` int(11) DEFAULT NULL,
  `Traitements` varchar(255) DEFAULT NULL,
  `Allergies` varchar(255) DEFAULT NULL,
  `Cni` blob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `PATIENTS`
--

INSERT INTO `PATIENTS` (`Id_patient`, `Nom`, `Prenom`, `Date_naissance`, `Sexe`, `Telephone`, `Profile_picture`, `Taille`, `Poids`, `Traitements`, `Allergies`, `Cni`) VALUES
(26, 'Gomez', 'Amanda', '1999-06-01', 'F', '1758191769', '', 196, 76, 'Non', 'Non', ''),
(27, 'Malone', 'Shirley', '1962-03-10', 'F', '1687907332', '', 159, 93, 'Non', 'Non', ''),
(28, 'Bates', 'Vincent', '1977-08-26', 'M', '9238323160', '', 207, 89, 'Non', 'Non', ''),
(29, 'Perez', 'Jill', '1959-01-16', 'F', '8383655117', '', 192, 51, 'Non', 'Non', ''),
(30, 'Taylor', 'Monique', '1958-12-17', 'F', '3749910295', '', 202, 74, 'Traitement de l\'arthrite', 'Non', ''),
(31, 'Morris', 'Teresa', '1940-02-15', 'F', '9677323168', '', 170, 105, 'Non', 'Non', ''),
(32, 'Gallegos', 'Jonathan', '1950-05-21', 'M', '6065850911', '', 151, 63, 'Non', 'Non', ''),
(33, 'Lee', 'Christopher', '1935-08-14', 'M', '8732671744', '', 175, 64, 'Non', 'Non', ''),
(34, 'Moore', 'Jennifer', '1988-06-30', 'F', '1818708689', '', 188, 110, 'Traitement de la dépression', 'Non', ''),
(35, 'Reed', 'Hannah', '1951-01-27', 'F', '4655438481', '', 200, 103, 'Non', 'Non', ''),
(36, 'Sanchez', 'Lisa', '1996-12-17', 'F', '8701564376', '', 157, 97, 'Non', 'Non', ''),
(37, 'Stevens', 'Roy', '1995-09-10', 'M', '4751825562', '', 196, 58, 'Non', 'Non', ''),
(38, 'Murray', 'Wendy', '1952-05-21', 'F', '6432947701', '', 203, 92, 'Non', 'Non', ''),
(39, 'Vasquez', 'Justin', '1935-04-10', 'M', '5313443288', '', 161, 92, 'Non', 'Non', ''),
(40, 'King', 'Lisa', '1976-03-09', 'F', '7422287590', '', 168, 58, 'Non', 'Allergie aux parfums et produits chimiques', ''),
(41, 'Ponce', 'Maria', '1940-11-11', 'F', '4046954260', '', 152, 86, 'Traitement de l\'asthme', 'Non', ''),
(42, 'Silva', 'Mario', '1994-05-18', 'M', '8930112318', '', 169, 105, 'Non', 'Non', ''),
(43, 'Thornton', 'Jennifer', '2001-05-20', 'F', '8498140578', '', 198, 62, 'Non', 'Non', ''),
(44, 'Wise', 'Shawn', '1974-02-03', 'M', '4433413821', '', 194, 109, 'Non', 'Non', ''),
(45, 'Rangel', 'Matthew', '1983-07-18', 'M', '8086588769', '', 160, 102, 'Non', 'Non', ''),
(46, 'Buck', 'Keith', '2000-09-04', 'M', '9599128694', '', 154, 103, 'Non', 'Non', ''),
(47, 'Black', 'Nancy', '1965-03-13', 'F', '4165690001', '', 191, 98, 'Non', 'Non', ''),
(48, 'Nichols', 'Edward', '1950-10-19', 'M', '6266600542', '', 160, 76, 'Non', 'Non', ''),
(49, 'Cox', 'Mary', '2006-03-29', 'F', '7478938214', '', 180, 59, 'Non', 'Non', ''),
(50, 'Gibson', 'Katelyn', '1968-02-21', 'F', '7148087311', '', 194, 71, 'Non', 'Non', ''),
(51, 'Swanson', 'Christopher', '1995-11-08', 'M', '2242807483', '', 205, 57, 'Non', 'Non', ''),
(52, 'Bailey', 'Kirk', '1996-12-01', 'M', '8141877600', '', 150, 92, 'Non', 'Non', ''),
(53, 'Marshall', 'Beverly', '2006-05-08', 'F', '6695660695', '', 180, 46, 'Traitement de l\'asthme', 'Non', ''),
(54, 'Martinez', 'Jennifer', '1993-12-31', 'F', '2100835607', '', 185, 51, 'Non', 'Allergie au latex', ''),
(55, 'Roth', 'James', '1948-04-11', 'M', '1463715553', '', 147, 55, 'Non', 'Non', ''),
(56, 'White', 'Kim', '1999-08-15', 'F', '2505234357', '', 210, 89, 'Non', 'Non', ''),
(57, 'Johnson', 'Richard', '1961-12-25', 'M', '8476947936', '', 206, 65, 'Non', 'Non', ''),
(58, 'Jones', 'Lisa', '1936-03-04', 'F', '6264806280', '', 206, 46, 'Non', 'Non', ''),
(59, 'Ramos', 'Christopher', '1992-11-05', 'M', '4359351618', '', 186, 95, 'Traitement de la dépression', 'Non', ''),
(60, 'Schneider', 'Christopher', '1985-09-20', 'M', '2768491336', '', 202, 107, 'Non', 'Non', ''),
(61, 'James', 'Jennifer', '1983-01-22', 'F', '4986679503', '', 151, 90, 'Traitement de l\'asthme', 'Non', ''),
(62, 'Cox', 'Charles', '1959-08-11', 'M', '4381931183', '', 205, 98, 'Non', 'Non', ''),
(63, 'Arnold', 'Roy', '1969-10-20', 'M', '8068902803', '', 195, 99, 'Non', 'Non', ''),
(64, 'Mitchell', 'David', '1945-05-17', 'M', '5900127298', '', 147, 48, 'Traitement de l\'hypercholestérolémie', 'Non', ''),
(65, 'Oconnor', 'Jacqueline', '2001-01-03', 'F', '7484711607', '', 151, 90, 'Traitement de la maladie de Crohn', 'Non', ''),
(66, 'Houston', 'Richard', '1981-07-02', 'M', '5653960270', '', 173, 101, 'Traitement de l\'hypertension', 'Non', ''),
(67, 'Thomas', 'Lindsay', '2002-06-16', 'F', '8715014737', '', 155, 73, 'Traitement de l\'ostéoporose', 'Non', ''),
(68, 'Kaiser', 'Tim', '1960-04-03', 'M', '5747146415', '', 205, 86, 'Non', 'Non', ''),
(69, 'Rogers', 'Dennis', '1968-12-29', 'M', '1106263037', '', 183, 108, 'Non', 'Non', ''),
(70, 'Williams', 'Mary', '1974-12-08', 'F', '4991275937', '', 205, 107, 'Non', 'Non', ''),
(71, 'Petersen', 'Amy', '1945-04-25', 'F', '7003386039', '', 192, 72, 'Non', 'Non', ''),
(72, 'Goodwin', 'Jeffrey', '1981-07-24', 'M', '1752605810', '', 168, 80, 'Non', 'Non', ''),
(73, 'Hebert', 'Paul', '1970-09-20', 'M', '9178555365', '', 168, 55, 'Non', 'Non', ''),
(74, 'Hill', 'Patrick', '1970-10-22', 'M', '2253710292', '', 161, 105, 'Non', 'Non', ''),
(75, 'Phillips', 'Sharon', '2001-01-14', 'F', '1752520238', '', 148, 99, 'Non', 'Allergie au latex', ''),
(76, 'Watson', 'Katie', '1950-04-09', 'F', '4458127526', '', 210, 64, 'Non', 'Non', ''),
(77, 'Davidson', 'Christina', '1990-04-26', 'F', '3076674002', '', 191, 47, 'Non', 'Non', ''),
(78, 'Gordon', 'Joseph', '1965-04-28', 'M', '4159827712', '', 187, 93, 'Non', 'Non', ''),
(79, 'Kelley', 'Kristin', '1967-09-23', 'F', '1106534433', '', 156, 63, 'Non', 'Non', ''),
(80, 'Strickland', 'Jocelyn', '1969-10-14', 'F', '9362008996', '', 162, 70, 'Non', 'Non', ''),
(81, 'Castro', 'Colton', '1997-11-20', 'M', '2351141394', '', 194, 72, 'Non', 'Non', ''),
(82, 'Monroe', 'Eric', '1960-07-05', 'M', '2956995772', '', 207, 82, 'Non', 'Non', ''),
(83, 'Thomas', 'William', '1980-08-11', 'M', '9087323640', '', 163, 109, 'Traitement de l\'épilepsie', 'Non', ''),
(84, 'Anthony', 'Isaac', '1977-07-13', 'M', '9911944915', '', 167, 107, 'Non', 'Non', ''),
(85, 'Deportes', 'Gaëlle', '2002-04-06', 'F', '7632477242', '', 159, 55, 'Non', 'Non', ''),
(86, 'Powell', 'Jessica', '1987-10-28', 'F', '7633572992', '', 181, 93, 'Non', 'Non', ''),
(87, 'Huynh', 'Douglas', '1970-08-20', 'M', '4729269782', '', 151, 104, 'Non', 'Non', ''),
(88, 'Gilbert', 'Allison', '1961-12-07', 'F', '2543150703', '', 182, 63, 'Non', 'Allergie au latex', ''),
(89, 'Deleon', 'Danny', '1975-02-25', 'M', '3779770938', '', 186, 88, 'Non', 'Non', ''),
(90, 'Moore', 'Toni', '1982-04-30', 'F', '1745158000', '', 208, 57, 'Non', 'Non', ''),
(91, 'Wilson', 'Jamie', '1981-02-14', 'F', '8865754474', '', 145, 60, 'Non', 'Non', ''),
(92, 'Faulkner', 'Gregory', '1937-07-29', 'M', '8593007761', '', 162, 101, 'Non', 'Non', ''),
(93, 'Brewer', 'Jorge', '1940-12-24', 'M', '4640620433', '', 191, 46, 'Traitement de l\'hypercholestérolémie', 'Non', ''),
(94, 'Robertson', 'Alexa', '1949-04-07', 'F', '8361043844', '', 178, 99, 'Traitement de l\'épilepsie', 'Non', ''),
(95, 'Baird', 'Adam', '1959-07-20', 'M', '7894152379', '', 187, 99, 'Non', 'Non', ''),
(96, 'Bender', 'Lauren', '1970-11-24', 'F', '8158670694', '', 204, 89, 'Non', 'Allergie aux moisissures', ''),
(97, 'Snyder', 'Joshua', '2003-07-23', 'M', '2348718407', '', 156, 70, 'Non', 'Non', ''),
(98, 'Williamson', 'Daryl', '1941-09-15', 'M', '6266886634', '', 204, 45, 'Non', 'Non', ''),
(99, 'Russell', 'Rachel', '1990-09-19', 'F', '8981250505', '', 151, 46, 'Non', 'Non', ''),
(100, 'Steele', 'Michael', '1940-03-07', 'M', '9086141583', '', 208, 89, 'Non', 'Non', '');

-- --------------------------------------------------------

--
-- Table structure for table `PATIENTS_ESSAIS`
--

CREATE TABLE `PATIENTS_ESSAIS` (
  `Id_patient` int(11) NOT NULL,
  `Id_essai` int(11) NOT NULL,
  `Date_participation` date NOT NULL,
  `Statut_participation` varchar(50) NOT NULL COMMENT 'Actif, fini, Abandon, En attente, Refus'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `PATIENTS_ESSAIS`
--

INSERT INTO `PATIENTS_ESSAIS` (`Id_patient`, `Id_essai`, `Date_participation`, `Statut_participation`) VALUES
(26, 10, '2019-05-12', 'Termine'),
(27, 5, '2023-09-15', 'Actif'),
(28, 5, '2024-01-07', 'Actif'),
(29, 1, '2021-04-12', 'Termine'),
(30, 1, '2021-06-01', 'Termine'),
(31, 0, '2019-05-30', 'Termine'),
(31, 11, '2024-07-13', 'Actif'),
(32, 5, '2023-10-04', 'Actif'),
(33, 0, '2019-07-14', 'Termine'),
(33, 9, '2016-11-20', 'Termine'),
(34, 5, '2023-07-20', 'Actif'),
(35, 10, '2019-05-30', 'Termine'),
(36, 4, '2019-07-21', 'Termine'),
(37, 5, '2023-11-30', 'Actif'),
(38, 0, '2019-05-15', 'Termine'),
(38, 11, '2024-06-29', 'En attente'),
(39, 1, '2021-05-10', 'Termine'),
(40, 1, '2021-04-26', 'Termine'),
(41, 0, '2019-08-20', 'Termine'),
(41, 9, '2017-01-07', 'Termine'),
(42, 1, '2021-06-07', 'Termine'),
(43, 2, '2024-09-30', 'Actif'),
(44, 2, '2024-09-16', 'Actif'),
(45, 8, '2023-01-21', 'Actif'),
(46, 4, '2019-07-14', 'Termine'),
(47, 8, '2023-01-10', 'Actif'),
(48, 9, '2016-11-04', 'Termine'),
(49, 9, '2016-09-17', 'Termine'),
(50, 6, '2022-12-03', 'Actif'),
(51, 0, '2019-06-29', 'Termine'),
(51, 11, '2024-08-10', 'Actif'),
(52, 4, '2019-08-11', 'Termine'),
(53, 0, '2019-07-29', 'Termine'),
(53, 9, '2016-12-06', 'Termine'),
(54, 5, '2023-12-19', 'Actif'),
(55, 4, '2019-07-28', 'Termine'),
(56, 2, '2024-08-30', 'En attente'),
(57, 9, '2016-10-03', 'Termine'),
(58, 6, '2022-07-23', 'Actif'),
(59, 6, '2022-08-30', 'Actif'),
(60, 6, '2022-12-22', 'Actif'),
(61, 6, '2022-10-07', 'Actif'),
(62, 8, '2023-03-02', 'Actif'),
(63, 1, '2021-03-20', 'Termine'),
(64, 10, '2019-06-17', 'Termine'),
(65, 4, '2019-08-25', 'Termine'),
(66, 0, '2019-08-13', 'Termine'),
(67, 8, '2023-02-14', 'Actif'),
(68, 5, '2023-11-11', 'Actif'),
(69, 10, '2019-06-08', 'Termine'),
(70, 0, '2019-04-15', 'Termine'),
(70, 11, '2024-06-01', 'Actif'),
(71, 8, '2023-02-22', 'Actif'),
(72, 10, '2019-04-15', 'Termine'),
(73, 10, '2019-04-24', 'Termine'),
(74, 5, '2023-08-27', 'Actif'),
(75, 1, '2021-04-17', 'Termine'),
(76, 1, '2021-04-10', 'Termine'),
(77, 0, '2019-06-14', 'Termine'),
(77, 11, '2024-07-27', 'Actif'),
(78, 4, '2019-06-30', 'Termine'),
(79, 5, '2023-10-23', 'Actif'),
(80, 0, '2019-04-30', 'Termine'),
(80, 11, '2024-06-15', 'En attente'),
(81, 4, '2019-07-07', 'Termine'),
(82, 5, '2023-08-08', 'Actif'),
(83, 10, '2019-05-03', 'Termine'),
(84, 9, '2016-09-01', 'Termine'),
(85, 1, '2021-05-24', 'Termine'),
(85, 9, '2016-12-22', 'Termine'),
(86, 6, '2022-11-14', 'Actif'),
(87, 4, '2019-09-01', 'Termine'),
(88, 8, '2023-02-06', 'Actif'),
(89, 9, '2016-10-19', 'Termine'),
(90, 6, '2022-07-04', 'Actif'),
(91, 6, '2022-08-11', 'Actif'),
(92, 4, '2019-08-18', 'Termine'),
(93, 8, '2023-01-13', 'Actif'),
(94, 10, '2019-05-21', 'Termine'),
(95, 4, '2019-08-04', 'Termine'),
(96, 6, '2022-09-18', 'Actif'),
(97, 2, '2024-08-23', 'Refus'),
(98, 6, '2022-10-26', 'Actif'),
(99, 8, '2023-01-29', 'Actif'),
(100, 1, '2021-03-29', 'Termine');

-- --------------------------------------------------------

--
-- Table structure for table `USERS`
--

CREATE TABLE `USERS` (
  `Id_user` int(11) NOT NULL,
  `Passwd` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `Role` varchar(50) NOT NULL COMMENT 'Patient, Medecin, Entreprise, Admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `USERS`
--

INSERT INTO `USERS` (`Id_user`, `Passwd`, `Email`, `Role`) VALUES
(1, '$2y$10$guzxWsBMiRGe/NwoM.GdtOFBofQTQZ5KXM/7HSoVKIdlQC1DiuALa', 'angie@admin.com', 'Admin'),
(2, '$2y$10$Ga8vWD.3DfKxpMwxPWpEweV7zr7ZfY4KWKdmJaViRGXmLutOrmjVm', 'francis@admin.com', 'Admin'),
(3, '$2y$10$OxUlTAiR1tHcCbwmYd2HMuUBzCYI4VlFAIny32AuQvmt8IFGw3LFS', 'julie@admin.com', 'Admin'),
(4, '$2y$10$oauvvGSCXJkmpqsjHTwZbuOhhTXpzMryEPmfSdAwZIhas1rj9aZYm', 'celine@admin.com', 'Admin'),
(5, '$2y$10$dMcAIprOl5LT7IqDvpvXY.moYrHYBYReMSEPMeWu72gvAb9LbkkAO', 'biohealthlabs@pro.com', 'Entreprise'),
(6, '$2y$10$urizhJc7.Bf1fYpdgkNJkeOLY8FrzmyIQQyepazhgOMDAC.zyKdLG', 'nextgenrobotics@pro.com', 'Entreprise'),
(7, '$2y$10$ewoMGT74MphBlVVtGt7Qhul8ITRXRDP.ZWZjBvd/NKBfumuNbcjwS', 'aquapuresystems@pro.com', 'Entreprise'),
(8, '$2y$10$6YGq2ntg2e7dWMnmTrPb3e3l5I03AlVvZJ8mr3WsE.vY3M.e/z6wm', 'urbanpulsetechnologies@pro.com', 'Entreprise'),
(9, '$2y$10$op69pXOkAIaUqD1Z8jQiPe7FGxlHiThhMM8vhrNo5kAGXO3hBrZYq', 'quantumdynamics@pro.com', 'Entreprise'),
(10, '$2y$10$gDh59EC1UHsD1X8SsyI.q.bdQBeQmmRIrYvAEnBvX28TnUcHLmm.K', 'blueskyinnovations@pro.com', 'Entreprise'),
(11, '$2y$10$RYxF67Oo8pmtjY4yZBjlo.bqcEW3MIr1NoxoZjPpADXtruPDUpjmu', 'greenwaveindustries@pro.com', 'Entreprise'),
(12, '$2y$10$51w/WXUrf209dBQHcNB5m.QEARzrAyMovO74wCaEUtk8K9b6lsKci', 'technovasolutions@pro.com', 'Entreprise'),
(13, '$2y$10$XIqfpErSSMcLaq4zOFz2OOgnhnc/f7bPXSp59dNPRq3dt.c.0E2qy', 'daniel.simpson@doc.com', 'Medecin'),
(14, '$2y$10$FcMSYWh6SYS/R4n9xb1lD.A86j8rTt61AvxZJea2TEdRO9J6dGk0W', 'christopher.duncan@doc.com', 'Medecin'),
(15, '$2y$10$.ZuCsaGreQyv5OvKAbX4uuhhcHo2Ke/oUrVohG/xOutXurNnU7U42', 'jennifer.schultz@doc.com', 'Medecin'),
(16, '$2y$10$OjFH1MpzmGIN4I/Q51VD1uVhlFIVIY5V9mOSdOqWcZWms1NFUsGeO', 'theodore.mcgrath@doc.com', 'Medecin'),
(17, '$2y$10$p2.PnVvbDwE.UkOtIlVIF.po0vBgkc0NHPpV.lQtwWzHqsBonHV5W', 'sandra.watson@doc.com', 'Medecin'),
(18, '$2y$10$AyUioMGf2ev6drtNNgU53O13qLl5aKfKFANAQyLEyJU.2Ob.p5jbO', 'mary.crawford@doc.com', 'Medecin'),
(19, '$2y$10$2l1ByYZV3cKIwarI5lCh.OY7FsIvxLFq7/la.4D8o1iDUgbpbhLZ2', 'angel.garcia@doc.com', 'Medecin'),
(20, '$2y$10$DU0KZ5sYw1wPOLHsKR8PIuTg0TSZ.owh/62h.YM2QuZ64dE.37qE6', 'mike.hauze@doc.com', 'Medecin'),
(21, '$2y$10$aPDoVv673q9qOsgdK.Kvw.jLnqjKMXsMbVwptSzeFfjrQ6PfumTmK', 'didier.raoult@doc.com', 'Medecin'),
(22, '$2y$10$J.vB8vaf3qZzLLo2GjnIU..YUxMpHSRNeS4t/Ah/EKAuE3qwwYO2K', 'jean.cive@doc.com', 'Medecin'),
(23, '$2y$10$uOyinY9.ieuOmwM1/.mrEeYlXWEWfZ4Up1Ckz.cBFwV/jy8YX8Vk.', 'valentin.vigeant@doc.com', 'Medecin'),
(24, '$2y$10$03lgHHxEuSqRStE3.mGghemcjxHOsq.LzVcNQ7wb6u8Brq6vvM3pq', 'guillaume.grataloup@doc.com', 'Medecin'),
(25, '$2y$10$pZqsHJyp0WUtgPiEDk67zOPIzk3NectojzOLNXkCqmaJ0m6wRNpfy', 'aure.topedik@doc.com', 'Medecin'),
(26, '$2y$10$7gELPg2a8Q8lrApxMr8Oy.uprzWN2CMouNvqfmhcFBpnuV.5I9Y1u', 'amanda.gomez@people.com', 'Patient'),
(27, '$2y$10$PnCVL5Njskbw4V7lAum/Tugg9CSRKYuSS2vYBKH8MQ3jpmL01FmKS', 'shirley.malone@people.com', 'Patient'),
(28, '$2y$10$0nCH85b.hKD5SuQCU5PTiuCo76A8xwmM.spFNcZCdTJiMTNrgQvju', 'vincent.bates@people.com', 'Patient'),
(29, '$2y$10$/QZjtRhHKdEwcVwTcLHOu.8Bixab19pkpfhi8ywjOjSNwx2NVRyBO', 'jill.perez@people.com', 'Patient'),
(30, '$2y$10$V.mWIytXFpxIWF8Yr2lRXuOsEhetcrl3NagpSa/FUg./1t6/6mr7e', 'monique.taylor@people.com', 'Patient'),
(31, '$2y$10$f6aM3Ds4ONOdamX4/eHEPeuwzr69CWIK4X9JM2Q8A2a3.EQZobvla', 'teresa.morris@people.com', 'Patient'),
(32, '$2y$10$nsL8nZTld4hGrXMB64wXbu/PMdZKPLxOD6.E2VXoSq5AjyKUH03EC', 'jonathan.gallegos@people.com', 'Patient'),
(33, '$2y$10$VZVL05QicIM2JHNIIzMgruve0mFZuBErVMrHRwa5rxJ/me5kTaUC2', 'christopher.lee@people.com', 'Patient'),
(34, '$2y$10$nuDUUsKuYcjiPxJ./DEfwuiVXdU3yyJupPKhTuqDjTTa6mWLhfiPS', 'jennifer.moore@people.com', 'Patient'),
(35, '$2y$10$nnwlOBfVgwKk1stXozOHoeXKR4Ac9PZsukRbN05fr6aLQnnEaJSM.', 'hannah.reed@people.com', 'Patient'),
(36, '$2y$10$jOX64MFYLb6aD2Kol6kOB.HDJZwRP8UfeenCPTTvcZJk6uhGITEp2', 'lisa.sanchez@people.com', 'Patient'),
(37, '$2y$10$CSGqZjmirpOkMCiZXeLuAuqfgyVOsXJsCcYOqqlKZog2KDBBYjWeO', 'roy.stevens@people.com', 'Patient'),
(38, '$2y$10$IJCQZyVTnTCdJ8VfpShKw.6AoFb1R5CVM3wQmO4E25Xc1PJxSPb.G', 'wendy.murray@people.com', 'Patient'),
(39, '$2y$10$M0PPI5fyjRhD05bNVRRJrebD0ElpTOWLb0y820/Yx/88Uloy1myqK', 'justin.vasquez@people.com', 'Patient'),
(40, '$2y$10$bwPK8wo6hQ2v4mF5bUTtLOcx1UDRlTW8VcwOURR5MVGwpO5CLPAvS', 'lisa.king@people.com', 'Patient'),
(41, '$2y$10$QEwXVINFrxY/rj.WFg1saekWRNbxttjZLA8fkNozB9zKFEotWIe8O', 'maria.ponce@people.com', 'Patient'),
(42, '$2y$10$jvOfU0cLjdp.hwiBEQpqEOBAA/AaMbe40DoO.9/TBxfEbIznDVPiS', 'mario.silva@people.com', 'Patient'),
(43, '$2y$10$Y98ctVfGqcOXdHYBOwB.uerQGwbkdxwJxq0g.RE9Q3alommUTj6XC', 'jennifer.thornton@people.com', 'Patient'),
(44, '$2y$10$zb2EBRuVwyO8jkXjbbOWiucyf6Xvs17hSo0j//JhGS2eYUv1uolQW', 'shawn.wise@people.com', 'Patient'),
(45, '$2y$10$KBUXJjAr/ltajxlGNq03j.S1nQGB0LayK9Xsbu8Lb7Y8.larHlKG2', 'matthew.rangel@people.com', 'Patient'),
(46, '$2y$10$d/96isrKpSoNPTqT1/Wuk.SUZSdW/X.LOEX9/tm/4ilV75aRLGFMu', 'keith.buck@people.com', 'Patient'),
(47, '$2y$10$skN943Q.Z1iwUMqla4t4X.O1WTPZP0w5JurLLJKO9h4ORVHWP5Ly.', 'nancy.black@people.com', 'Patient'),
(48, '$2y$10$x5rcvGzyDaE95hnL0Ni3V.v7TFXc4tJ3zoSEwOMSgZDW2Pu2mcg0e', 'edward.nichols@people.com', 'Patient'),
(49, '$2y$10$0XhfFu/2MIQ/sKxDTm8J0eSRAP5wf/4tdaIDoahUtrJNbFI/p7/KG', 'mary.cox@people.com', 'Patient'),
(50, '$2y$10$Bh/uCGDYTqKAu71Va35DY.dWd2Aiyig9Tv4yC9G3YiX6KxUJGDlmG', 'katelyn.gibson@people.com', 'Patient'),
(51, '$2y$10$qIWud20L9zXLMd09bY5wD.7txSTq5MraxM.uM9SLLOmwLXZyawsty', 'christopher.swanson@people.com', 'Patient'),
(52, '$2y$10$t9oJCNz2FUYdH5RHPQTB5.MJiqmTm8TPbYuKpG5R8Y6j7K215NeyW', 'kirk.bailey@people.com', 'Patient'),
(53, '$2y$10$pfkmcAd7xjNQA.CvUfle3OFhV9//1i1dKzio1bxrlgurV.S3tbOzi', 'beverly.marshall@people.com', 'Patient'),
(54, '$2y$10$gk4WTCQGirvjD0suhK9a8ejHMhUVkvV5Dqh4S8P6lDzPpphhxINUK', 'jennifer.martinez@people.com', 'Patient'),
(55, '$2y$10$m.Iws9TsV5i6VJxiwst/y.q03twZ.X1DX5ctermRe8v11stv6h9Sm', 'james.roth@people.com', 'Patient'),
(56, '$2y$10$aXcJFnHSqzEcPQ.I0HpQk.FoKtheTUEeUZnU/fHqc1yQFtRMU0bN.', 'kim.white@people.com', 'Patient'),
(57, '$2y$10$7gM9D9DOjpgD5PXeMdko2u.0Wd8p4dyYRJEhBqe9zlDIAoF1PIlLC', 'richard.johnson@people.com', 'Patient'),
(58, '$2y$10$ql0/S5wlIdFH2mN7ejwVEeT0tqCCVTXI2.9X.cQMYBsLDVMWX4kum', 'lisa.jones@people.com', 'Patient'),
(59, '$2y$10$6ua3keCdcfe9a1kP.fmOE.vyTyoJwJLAtZKZM8CMY/FKdpmpXaEaa', 'christopher.ramos@people.com', 'Patient'),
(60, '$2y$10$wHfpdP9G.AUcEZcOI4soVOvy0WJpa1cE9UiW1A93bRPrkk1WPv0Pq', 'christopher.schneider@people.com', 'Patient'),
(61, '$2y$10$rlOhxZHr2nstTmRazFWGYuHr2JiOpGOlv7jSECMm6Bh4apLncn0bO', 'jennifer.james@people.com', 'Patient'),
(62, '$2y$10$4UnKf7cjNu4jx9udSVO0zu545k9rlv.EhxLnMUVJ/zdSw2/IvFAbS', 'charles.cox@people.com', 'Patient'),
(63, '$2y$10$WqK4MsxyTbos3kHq8lhCJ.8yvZhA9G03rvK6hXmzg.crvPZYRh3Jm', 'roy.arnold@people.com', 'Patient'),
(64, '$2y$10$flhqO7FDivZyFYKvq14YzOI1h4S9Kgn7zlhA6T9l6r/QCPMS3eqIu', 'david.mitchell@people.com', 'Patient'),
(65, '$2y$10$8DkJyMMhCgy5cjCcuY6vSuLDZK6Dhp4.Iwy3Ew5sNx76cvYUwgpsa', 'jacqueline.oconnor@people.com', 'Patient'),
(66, '$2y$10$EdySxeecKiAWxY2GCZnFdee2Fx7cIX3uHB8w5URZ/8pppGI4NOru6', 'richard.houston@people.com', 'Patient'),
(67, '$2y$10$ELsrBXLPWGuaJRgQmMuEI.iCzNLzfzYQRPueVJ5GjCOno4fvIcqZi', 'lindsay.thomas@people.com', 'Patient'),
(68, '$2y$10$kt/utE6RX7Ts6aKNqCX.3u/jYF4MxjNMTAct8/Dd0/BZdud1qGKWC', 'tim.kaiser@people.com', 'Patient'),
(69, '$2y$10$52m5vEBcXgCPUxNUQveaT.t3rT.0C5Myp1a/Y4h.oHZpz7SF6AQg2', 'dennis.rogers@people.com', 'Patient'),
(70, '$2y$10$8lC0uiRNaELfgLYqpWIBHOrx8OLzNrm5cAiEcvUD8aBXNuU2Ojj6m', 'mary.williams@people.com', 'Patient'),
(71, '$2y$10$nhuGm1BVfXIFIMaB2n.TVOHJitfItpmAfLyQWq07LkhY4OVx0q0a.', 'amy.petersen@people.com', 'Patient'),
(72, '$2y$10$CBJrteq9wZmQ/MJzkVrO0OPE2dvb4Pa7Rmngyjb99z77yAy8cJw2q', 'jeffrey.goodwin@people.com', 'Patient'),
(73, '$2y$10$54d2KeaUu1MznVk.rMxAp.bTV5Ir0PZpGpf4gAyM.wxkXDfBoYQ1G', 'paul.hebert@people.com', 'Patient'),
(74, '$2y$10$nLraXNBeMcyUunTNsbscJuAYLpCjwVLueJPBCUwcZ8sOByRu//RwC', 'patrick.hill@people.com', 'Patient'),
(75, '$2y$10$BterURa3PA8JGJd99egEYeXLjEzLP.ddTUSAad6S.Uk.mwe/jJIR2', 'sharon.phillips@people.com', 'Patient'),
(76, '$2y$10$ij3yz1u9rzys7oe5A4WBZumCBp7nsX4U7iUcU7EjADyfiuj5dKvOy', 'katie.watson@people.com', 'Patient'),
(77, '$2y$10$xi2Pq/RiqQrP4.LC2LtMSexyuD2n5y.OoxivH1rSi4TSa1uPQQAJC', 'christina.davidson@people.com', 'Patient'),
(78, '$2y$10$bfQZp6x8muWruziMp8A7HuxJKAPW03zsNyP8TZAVmFLsnl38eJA46', 'joseph.gordon@people.com', 'Patient'),
(79, '$2y$10$bq3S5lv03OsCgkDlT9O6WuvZg9IBI4MD0QZlwelJJ4yDc7efUOFpm', 'kristin.kelley@people.com', 'Patient'),
(80, '$2y$10$b3eyocYWYM3C.IBQLYX3aeSI8V0XVl3y077zyu9Sk1vtXH3MQc1RC', 'jocelyn.strickland@people.com', 'Patient'),
(81, '$2y$10$cBnuANnzl/ntCuBSqqJ9qOiyA.Ltq3y5LcCtooo8gydTLip3jFkUy', 'colton.castro@people.com', 'Patient'),
(82, '$2y$10$phwmj28ChqHUlPlTJOJbkOWrWSTgxdk.FgK1d3Z/RlZdjCn9XA8ka', 'eric.monroe@people.com', 'Patient'),
(83, '$2y$10$bGkW2lK439lvKyae4.MiAelnzD1nMRL3btkSVex4P8v8gzN07BEWy', 'william.thomas@people.com', 'Patient'),
(84, '$2y$10$RfPJ.rIqFrJO/Ef1u8j9NulYyHNGyJbOPcONBB7/homRl81FpHntW', 'isaac.anthony@people.com', 'Patient'),
(85, '$2y$10$AX5GM4JoXAg3Rs9WfyPWBeaI43Uoq9UxCmdzfUYTrPEHdJKGfrJHy', 'gaelle.deportes@people.com', 'Patient'),
(86, '$2y$10$AZXvZuhEQhbCLStroT.7q.hcTKFLj/8Mm2mvwZ.F4FgJLDSoQvM9S', 'jessica.powell@people.com', 'Patient'),
(87, '$2y$10$DliHyLiL0wBgOtU63gb0w.h9FdQXuZgJFRH.Q3YdgtEw5F7KihYGC', 'douglas.huynh@people.com', 'Patient'),
(88, '$2y$10$GEMYG8bqn5N5jEdyagEJA.CpNmKuVYx544N2R.6wJbapTBC55ctBq', 'allison.gilbert@people.com', 'Patient'),
(89, '$2y$10$Xk1L6BMH1C07DqZ5HzfEoObfkyZa/oHKuLWDvLCiYX62swnFdKYNK', 'danny.deleon@people.com', 'Patient'),
(90, '$2y$10$eGreDTNSFkyaid7GnEc8seVVZKiO0IgW4LI1BsjhwA9SrofYYzX46', 'toni.moore@people.com', 'Patient'),
(91, '$2y$10$LdSwyyQKtCFWzOVpYDGfpuIeb9Q4Ntvy3H9uEF/98C7JE41maa.D2', 'jamie.wilson@people.com', 'Patient'),
(92, '$2y$10$KLEaKGaj5bBCRCExFreMueMj1qHOkOjowVAWYiQbJEnFxGLgY7ezm', 'gregory.faulkner@people.com', 'Patient'),
(93, '$2y$10$lWqXVHEajnN9.mhCQmxzA.9BkvSX.T3AdBeHzkizrMJXKmI7htT0.', 'jorge.brewer@people.com', 'Patient'),
(94, '$2y$10$0fcUwe98.q0p.7/peVv2au/THyZu5H40vVIBPewTI/veHd5BWNCm2', 'alexa.robertson@people.com', 'Patient'),
(95, '$2y$10$Rp5297OD.Q4SPDqaItis8.gMyVG6ei.CJEIy8540W1QfdeaGRVxpu', 'adam.baird@people.com', 'Patient'),
(96, '$2y$10$Bp10Rw5dmc6SgUuopyhzHe1R2EXawR.jH/pfl5JGbO0eyrZLvBDk6', 'lauren.bender@people.com', 'Patient'),
(97, '$2y$10$FCtKCD6d72WO6FbkM/reAuM1sGUHwNvBa55KPnOn2WhLEBtUkSiIe', 'joshua.snyder@people.com', 'Patient'),
(98, '$2y$10$WeDWcy78mHtTDUdH4HWt7.MhfbUgHQJWsn8PZtfiU0O0TyrsmbWWi', 'daryl.williamson@people.com', 'Patient'),
(99, '$2y$10$9bj7DOoiOixBhe0y3BZPROXIl1y9p/i1AhWtFxCeLJSWtuGVUkt2O', 'rachel.russell@people.com', 'Patient'),
(100, '$2y$10$y.WBRHB0gkb3czR9beROfOpqxsiT14k0vDrYsNoRiwZz1EOLzBEpW', 'michael.steele@people.com', 'Patient');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `DESTINATAIRE`
--
ALTER TABLE `DESTINATAIRE`
  ADD PRIMARY KEY (`Id_notif`, `Id_destinataire`),
  ADD KEY `FK_DESTINATAIRE_USERS` (`Id_destinataire`),
  DROP FOREIGN KEY `FK_DESTINATAIRE_NOTIFICATION`,
  ADD CONSTRAINT `FK_DESTINATAIRE_NOTIFICATION` FOREIGN KEY (`Id_notif`) REFERENCES `NOTIFICATION` (`Id_notif`) ON DELETE SET NULL;

--
-- Indexes for table `ENTREPRISES`
--
ALTER TABLE `ENTREPRISES`
  ADD PRIMARY KEY (`Id_entreprise`);

--
-- Indexes for table `ESSAIS_CLINIQUES`
--
ALTER TABLE `ESSAIS_CLINIQUES`
  ADD PRIMARY KEY (`Id_essai`),
  ADD KEY `FK_ESSAIS_ENTREPRISES` (`Id_entreprise`);

--
-- Indexes for table `MEDECINS`
--
ALTER TABLE `MEDECINS`
  ADD PRIMARY KEY (`Id_medecin`);

--
-- Indexes for table `MEDECIN_ESSAIS`
--
ALTER TABLE `MEDECIN_ESSAIS`
  ADD PRIMARY KEY (`Id_medecin`,`Id_essai`),
  ADD KEY `FK_MEDECIN_ESSAIS_ESSAIS` (`Id_essai`);

--
-- Indexes for table `NOTIFICATION`
--
ALTER TABLE `NOTIFICATION`
  ADD PRIMARY KEY (`Id_notif`),
  ADD KEY `FK_NOTIFICATION_ESSAIS` (`Id_Essai`);

--
-- Indexes for table `PATIENTS`
--
ALTER TABLE `PATIENTS`
  ADD PRIMARY KEY (`Id_patient`);

--
-- Indexes for table `PATIENTS_ESSAIS`
--
ALTER TABLE `PATIENTS_ESSAIS`
  ADD PRIMARY KEY (`Id_patient`,`Id_essai`),
  ADD KEY `FK_PATIENTS_ESSAIS_ESSAIS` (`Id_essai`);

--
-- Indexes for table `USERS`
--
ALTER TABLE `USERS`
  ADD PRIMARY KEY (`Id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ESSAIS_CLINIQUES`
--
ALTER TABLE `ESSAIS_CLINIQUES`
  MODIFY `Id_essai` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `NOTIFICATION`
--
ALTER TABLE `NOTIFICATION`
  MODIFY `Id_notif` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT for table `USERS`
--
ALTER TABLE `USERS`
  MODIFY `Id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `DESTINATAIRE`
--
ALTER TABLE `DESTINATAIRE`
  ADD CONSTRAINT `FK_DESTINATAIRE_NOTIFICATION` FOREIGN KEY (`Id_notif`) REFERENCES `NOTIFICATION` (`Id_notif`),
  ADD CONSTRAINT `FK_DESTINATAIRE_USERS` FOREIGN KEY (`Id_destinataire`) REFERENCES `USERS` (`Id_user`);

--
-- Constraints for table `ENTREPRISES`
--
ALTER TABLE `ENTREPRISES`
  ADD CONSTRAINT `FK_ENTREPRISES_USERS` FOREIGN KEY (`Id_entreprise`) REFERENCES `USERS` (`Id_user`) ON DELETE CASCADE;

--
-- Constraints for table `ESSAIS_CLINIQUES`
--
ALTER TABLE `ESSAIS_CLINIQUES`
  ADD CONSTRAINT `FK_ESSAIS_ENTREPRISES` FOREIGN KEY (`Id_entreprise`) REFERENCES `ENTREPRISES` (`Id_entreprise`) ON DELETE CASCADE;

--
-- Constraints for table `MEDECINS`
--
ALTER TABLE `MEDECINS`
  ADD CONSTRAINT `FK_MEDECINS_USERS` FOREIGN KEY (`Id_medecin`) REFERENCES `USERS` (`Id_user`) ON DELETE CASCADE;

--
-- Constraints for table `MEDECIN_ESSAIS`
--
ALTER TABLE `MEDECIN_ESSAIS`
  ADD CONSTRAINT `FK_MEDECIN_ESSAIS_ESSAIS` FOREIGN KEY (`Id_essai`) REFERENCES `ESSAIS_CLINIQUES` (`Id_essai`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_MEDECIN_ESSAIS_MEDECINS` FOREIGN KEY (`Id_medecin`) REFERENCES `MEDECINS` (`Id_medecin`) ON DELETE CASCADE;

--
-- Constraints for table `NOTIFICATION`
--
ALTER TABLE `NOTIFICATION`
  ADD CONSTRAINT `FK_NOTIFICATION_ESSAIS` FOREIGN KEY (`Id_Essai`) REFERENCES `ESSAIS_CLINIQUES` (`Id_essai`);

--
-- Constraints for table `PATIENTS`
--
ALTER TABLE `PATIENTS`
  ADD CONSTRAINT `FK_PATIENTS_USERS` FOREIGN KEY (`Id_patient`) REFERENCES `USERS` (`Id_user`) ON DELETE CASCADE;

--
-- Constraints for table `PATIENTS_ESSAIS`
--
ALTER TABLE `PATIENTS_ESSAIS`
  ADD CONSTRAINT `FK_PATIENTS_ESSAIS_ESSAIS` FOREIGN KEY (`Id_essai`) REFERENCES `ESSAIS_CLINIQUES` (`Id_essai`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_PATIENTS_ESSAIS_PATIENTS` FOREIGN KEY (`Id_patient`) REFERENCES `PATIENTS` (`Id_patient`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
