-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- Client :  localhost:8889
-- Généré le :  Dim 12 Novembre 2017 à 22:41
-- Version du serveur :  5.5.42
-- Version de PHP :  5.6.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données :  `webkot5-test1`
--

-- --------------------------------------------------------

--
-- Structure de la table `ir_config_parameter`
--

CREATE TABLE `ir_config_parameter` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  `value` text NOT NULL,
  `category` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `ir_external_identifier`
--

CREATE TABLE `ir_external_identifier` (
  `id` int(10) unsigned NOT NULL,
  `xml_id` varchar(128) NOT NULL,
  `module` varchar(128) NOT NULL,
  `res_id` int(10) unsigned NOT NULL,
  `res_model` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `ir_module`
--

CREATE TABLE `ir_module` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  `directory` varchar(128) NOT NULL,
  `description` text,
  `active` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `ir_view`
--

CREATE TABLE `ir_view` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(128) NOT NULL,
  `type` varchar(64) NOT NULL,
  `arch` text NOT NULL,
  `sequence` int(11) NOT NULL DEFAULT '10',
  `active` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `ir_config_parameter`
--
ALTER TABLE `ir_config_parameter`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `ir_external_identifier`
--
ALTER TABLE `ir_external_identifier`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `ir_module`
--
ALTER TABLE `ir_module`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `ir_view`
--
ALTER TABLE `ir_view`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `ir_config_parameter`
--
ALTER TABLE `ir_config_parameter`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ir_external_identifier`
--
ALTER TABLE `ir_external_identifier`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ir_module`
--
ALTER TABLE `ir_module`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `ir_view`
--
ALTER TABLE `ir_view`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
