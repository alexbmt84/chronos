-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 03 oct. 2022 à 09:00
-- Version du serveur : 8.0.29
-- Version de PHP : 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `chronos_taches`
--
CREATE DATABASE IF NOT EXISTS `chronos_taches` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `chronos_taches`;

-- --------------------------------------------------------

--
-- Structure de la table `categories_taches`
--

DROP TABLE IF EXISTS `categories_taches`;
CREATE TABLE IF NOT EXISTS `categories_taches` (
  `id` int NOT NULL AUTO_INCREMENT,
  `label` varchar(25) COLLATE utf8mb4_general_ci NOT NULL,
  `defaut` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories_taches`
--

INSERT INTO `categories_taches` (`id`, `label`, `defaut`) VALUES
(1, 'Pro', 1),
(2, 'Perso', 0);

-- --------------------------------------------------------

--
-- Structure de la table `taches`
--

DROP TABLE IF EXISTS `taches`;
CREATE TABLE IF NOT EXISTS `taches` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  `date_creation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `etat` int NOT NULL DEFAULT '0',
  `categorie` int NOT NULL DEFAULT '0',
  `utilisateur` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_taches_utilisateurs_utilisateur` (`utilisateur`),
  KEY `fk_taches_categories_categorie` (`categorie`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `taches`
--


-- --------------------------------------------------------

--
-- Structure de la table `timers`
--

DROP TABLE IF EXISTS `timers`;
CREATE TABLE IF NOT EXISTS `timers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `start` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `end` datetime DEFAULT NULL,
  `tache` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_chronos_taches_tache` (`tache`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `timers`
--


-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(127) COLLATE utf8mb4_general_ci NOT NULL,
  `hash` varchar(70) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `pseudo` varchar(60) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--


--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `taches`
--
ALTER TABLE `taches`
  ADD CONSTRAINT `fk_taches_categories_categorie` FOREIGN KEY (`categorie`) REFERENCES `categories_taches` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_taches_utilisateurs_utilisateur` FOREIGN KEY (`utilisateur`) REFERENCES `utilisateurs` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Contraintes pour la table `timers`
--
ALTER TABLE `timers`
  ADD CONSTRAINT `fk_chronos_taches_tache` FOREIGN KEY (`tache`) REFERENCES `taches` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
