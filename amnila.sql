drop database  if exists amnila ;
-- Création de la base de données Amnila
CREATE DATABASE amnila;

-- Utilisation de la base de données
USE amnila;

CREATE TABLE `activite` (
  `IDactivite` int(5) NOT NULL,
  `IDStation` int(5) NOT NULL,
  `NomA` varchar(50) NOT NULL,
  `PrixA` char(10) NOT NULL,
  `DescriptionA` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `administrateur`
--

CREATE TABLE `administrateur` (
  `IDadministrateur` int(5) NOT NULL,
  `NomA` varchar(20) NOT NULL,
  `PrenomA` varchar(20) NOT NULL,
  `UsernameA` varchar(20) NOT NULL,
  `PasswordA` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `administrateur`
--

INSERT INTO `administrateur` (`IDadministrateur`, `NomA`, `PrenomA`, `UsernameA`, `PasswordA`) VALUES
(1, 'Pond', 'Amelia', 'Amy', '$2y$10$00vrmD8mx/wdE');

-- --------------------------------------------------------

--
-- Structure de la table `appartement`
--

CREATE TABLE `appartement` (
  `IDappartement` int(5) NOT NULL,
  `Type_d_appartementA` varchar(255) NOT NULL,
  `RueA` varchar(50) NOT NULL,
  `Code_PostalA` char(8) NOT NULL,
  `VilleA` varchar(20) NOT NULL,
  `N__immeubleA` char(5) NOT NULL,
  `SurfaceA` varchar(10) NOT NULL,
  `Prix_journalier` decimal(15,2) NOT NULL,
  `IDproprietaire` int(5) NOT NULL,
  `IDregion` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `appartement`
--

INSERT INTO `appartement` (`IDappartement`, `Type_d_appartementA`, `RueA`, `Code_PostalA`, `VilleA`, `N__immeubleA`, `SurfaceA`, `Prix_journalier`, `IDproprietaire`, `IDregion`) VALUES
(1, 'Charmant appartement en montagne', '12 rue sur la falaise', '75002', 'Paris', '', '45', 12.00, 2, 1),
(2, 'Chalet Rose Quartz', '8 rue sur la falaise', '75001', 'Paris', '', '45', 15.00, 2, 1);

-- --------------------------------------------------------

--
-- Structure de la table `contrat`
--

CREATE TABLE `contrat` (
  `IDcontrat` int(11) NOT NULL,
  `Date_de_signatureC` date NOT NULL,
  `Date_de_debutC` date NOT NULL,
  `Date_de_finC` date NOT NULL,
  `Arrhes_payees` varchar(10) NOT NULL,
  `Solde_payee` varchar(10) NOT NULL,
  `Caution_versee` varchar(10) NOT NULL,
  `IDappartement` int(5) NOT NULL,
  `IDreservation` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `equipement`
--

CREATE TABLE `equipement` (
  `IDequipement` int(5) NOT NULL,
  `Type_d_equipement` varchar(20) NOT NULL,
  `Detail_equipement` varchar(255) DEFAULT NULL,
  `IDproprietaire` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `equipement`
--

INSERT INTO `equipement` (`IDequipement`, `Type_d_equipement`, `Detail_equipement`, `IDproprietaire`) VALUES
(2, 'Ski', 'Bon état', 2);

-- --------------------------------------------------------

--
-- Structure de la table `locataire`
--

CREATE TABLE `locataire` (
  `IDlocataire` int(5) NOT NULL,
  `NomL` varchar(20) NOT NULL,
  `PrenomL` varchar(20) NOT NULL,
  `Adresse_email_L` varchar(50) NOT NULL,
  `PasswordL` varchar(20) NOT NULL,
  `TelephoneL` char(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `locataire`
--

INSERT INTO `locataire` (`IDlocataire`, `NomL`, `PrenomL`, `Adresse_email_L`, `PasswordL`, `TelephoneL`) VALUES
(3, 'Thomas', 'Smith', 'locataire@gmail.com', '$2y$10$Cai8FiydaP.Dl', '0769895711'),
(4, 'Jacob', 'Jones', 'locataire2@gmail.com', '$2y$10$W126wY/hLtSby', '0758966321');

-- --------------------------------------------------------

--
-- Structure de la table `photo`
--

CREATE TABLE `photo` (
  `IDphoto` int(5) NOT NULL,
  `Chemin` varchar(50) NOT NULL,
  `DescriptionP` varchar(50) DEFAULT NULL,
  `IDappartement` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `photo`
--

INSERT INTO `photo` (`IDphoto`, `Chemin`, `DescriptionP`, `IDappartement`) VALUES
(1, 'uploads/1_1740330195_0.jpg', NULL, 1),
(2, 'uploads/2_1740670630_0.jpg', NULL, 2),
(3, 'uploads/2_1740670630_1.jpg', NULL, 2),
(4, 'uploads/2_1740670630_2.jpg', NULL, 2);

-- --------------------------------------------------------

--
-- Structure de la table `proprietaire`
--

CREATE TABLE `proprietaire` (
  `IDproprietaire` int(5) NOT NULL,
  `NomP` varchar(20) NOT NULL,
  `PrenomP` varchar(20) NOT NULL,
  `Adresse_email_P` varchar(50) NOT NULL,
  `PasswordP` varchar(20) NOT NULL,
  `TelephoneP` char(10) NOT NULL,
  `RIB` char(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `proprietaire`
--

INSERT INTO `proprietaire` (`IDproprietaire`, `NomP`, `PrenomP`, `Adresse_email_P`, `PasswordP`, `TelephoneP`, `RIB`) VALUES
(2, 'John', 'Smith', 'proprietaire@gmail.com', '$2y$10$00vrmD8mx/wdE', '0758966321', '01478523654');

-- --------------------------------------------------------

--
-- Structure de la table `region`
--

CREATE TABLE `region` (
  `IDregion` int(5) NOT NULL,
  `NomR` varchar(50) NOT NULL,
  `DepartementR` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `region`
--

INSERT INTO `region` (`IDregion`, `NomR`, `DepartementR`) VALUES
(1, 'Ile-de-france', '');

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

CREATE TABLE `reservation` (
  `IDreservation` int(5) NOT NULL,
  `Date_de_finR` date NOT NULL,
  `StatutR` varchar(20) NOT NULL,
  `Date_de_debutR` date NOT NULL,
  `IDappartement` int(5) NOT NULL,
  `IDlocataire` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reservation`
--

INSERT INTO `reservation` (`IDreservation`, `Date_de_finR`, `StatutR`, `Date_de_debutR`, `IDappartement`, `IDlocataire`) VALUES
(5, '2025-03-31', 'En attente', '2025-03-20', 2, 3);

-- --------------------------------------------------------

--
-- Structure de la table `reserver`
--

CREATE TABLE `reserver` (
  `IDequipement` int(5) NOT NULL,
  `IDreservation` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `station`
--

CREATE TABLE `station` (
  `IDStation` int(5) NOT NULL,
  `NomS` varchar(50) NOT NULL,
  `IDregion` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(5) NOT NULL,
  `name` varchar(50) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `role` enum('admin','proprietaire','locataire') NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `firstname`, `role`, `email`, `password`, `reset_token`, `reset_expires`) VALUES
(1, 'Pond', 'Amelia', 'admin', 'tardis@gmail.com', '$2y$10$00vrmD8mx/wdEdcfUJStfOXrLWVLbZOYdLhSyirKzEmSdSjdWU3UW', NULL, NULL),
(2, '', '', 'proprietaire', 'proprietaire@gmail.com', '$2y$10$00vrmD8mx/wdEdcfUJStfOXrLWVLbZOYdLhSyirKzEmSdSjdWU3UW', 'adcc30edf75e2a9bdeeff82abe5bd75aea6c6e9bba08329f36261553505843c9', '2025-03-05 10:21:41'),
(3, '', '', 'locataire', 'locataire@gmail.com', '$2y$10$Cai8FiydaP.DlSG.ldkIaeTm9kZTqDAB2kdsJqidS7b21QBc5sTnm', '46690a771aea8c334eba1a24daa6ac33e463e3ec72433eaeba0676dc962cc7c2', '2025-03-05 10:20:01'),
(4, '', '', 'locataire', 'locataire2@gmail.com', '$2y$10$W126wY/hLtSby0ccXm4h7.brJT4wnUlqbo4sjEaB5iZ1ugiA48S/C', NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `activite`
--
ALTER TABLE `activite`
  ADD PRIMARY KEY (`IDactivite`),
  ADD KEY `activite_ibfk_1` (`IDStation`);

--
-- Index pour la table `administrateur`
--
ALTER TABLE `administrateur`
  ADD PRIMARY KEY (`IDadministrateur`);

--
-- Index pour la table `appartement`
--
ALTER TABLE `appartement`
  ADD PRIMARY KEY (`IDappartement`),
  ADD KEY `FK_appartement_proprietaire` (`IDproprietaire`),
  ADD KEY `FK_appartement_region` (`IDregion`);

--
-- Index pour la table `contrat`
--
ALTER TABLE `contrat`
  ADD PRIMARY KEY (`IDcontrat`),
  ADD KEY `FK_contrat_appartement` (`IDappartement`),
  ADD KEY `FK_contrat_reservation` (`IDreservation`);

--
-- Index pour la table `equipement`
--
ALTER TABLE `equipement`
  ADD PRIMARY KEY (`IDequipement`),
  ADD KEY `FK_equipement_proprietaire` (`IDproprietaire`);

--
-- Index pour la table `locataire`
--
ALTER TABLE `locataire`
  ADD PRIMARY KEY (`IDlocataire`);

--
-- Index pour la table `photo`
--
ALTER TABLE `photo`
  ADD PRIMARY KEY (`IDphoto`),
  ADD KEY `FK_photo_appartement` (`IDappartement`);

--
-- Index pour la table `proprietaire`
--
ALTER TABLE `proprietaire`
  ADD PRIMARY KEY (`IDproprietaire`);

--
-- Index pour la table `region`
--
ALTER TABLE `region`
  ADD PRIMARY KEY (`IDregion`);

--
-- Index pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`IDreservation`),
  ADD KEY `FK_reservation_appartement` (`IDappartement`),
  ADD KEY `FK_reservation_locataire` (`IDlocataire`);

--
-- Index pour la table `station`
--
ALTER TABLE `station`
  ADD PRIMARY KEY (`IDStation`),
  ADD KEY `station_ibfk_1` (`IDregion`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `activite`
--
ALTER TABLE `activite`
  MODIFY `IDactivite` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `administrateur`
--
ALTER TABLE `administrateur`
  MODIFY `IDadministrateur` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `appartement`
--
ALTER TABLE `appartement`
  MODIFY `IDappartement` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `contrat`
--
ALTER TABLE `contrat`
  MODIFY `IDcontrat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `equipement`
--
ALTER TABLE `equipement`
  MODIFY `IDequipement` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `locataire`
--
ALTER TABLE `locataire`
  MODIFY `IDlocataire` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `photo`
--
ALTER TABLE `photo`
  MODIFY `IDphoto` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `proprietaire`
--
ALTER TABLE `proprietaire`
  MODIFY `IDproprietaire` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `region`
--
ALTER TABLE `region`
  MODIFY `IDregion` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `IDreservation` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `station`
--
ALTER TABLE `station`
  MODIFY `IDStation` int(5) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `activite`
--
ALTER TABLE `activite`
  ADD CONSTRAINT `activite_ibfk_1` FOREIGN KEY (`IDStation`) REFERENCES `station` (`IDStation`);

--
-- Contraintes pour la table `appartement`
--
ALTER TABLE `appartement`
  ADD CONSTRAINT `FK_appartement_proprietaire` FOREIGN KEY (`IDproprietaire`) REFERENCES `proprietaire` (`IDproprietaire`),
  ADD CONSTRAINT `FK_appartement_region` FOREIGN KEY (`IDregion`) REFERENCES `region` (`IDregion`);

--
-- Contraintes pour la table `contrat`
--
ALTER TABLE `contrat`
  ADD CONSTRAINT `FK_contrat_appartement` FOREIGN KEY (`IDappartement`) REFERENCES `appartement` (`IDappartement`),
  ADD CONSTRAINT `FK_contrat_reservation` FOREIGN KEY (`IDreservation`) REFERENCES `reservation` (`IDreservation`);

--
-- Contraintes pour la table `equipement`
--
ALTER TABLE `equipement`
  ADD CONSTRAINT `FK_equipement_proprietaire` FOREIGN KEY (`IDproprietaire`) REFERENCES `proprietaire` (`IDproprietaire`);

--
-- Contraintes pour la table `locataire`
--
ALTER TABLE `locataire`
  ADD CONSTRAINT `FK_locataire_user_id` FOREIGN KEY (`IDlocataire`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `photo`
--
ALTER TABLE `photo`
  ADD CONSTRAINT `FK_photo_appartement` FOREIGN KEY (`IDappartement`) REFERENCES `appartement` (`IDappartement`);

--
-- Contraintes pour la table `proprietaire`
--
ALTER TABLE `proprietaire`
  ADD CONSTRAINT `FK_proprietaire_user_id` FOREIGN KEY (`IDproprietaire`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `FK_reservation_appartement` FOREIGN KEY (`IDappartement`) REFERENCES `appartement` (`IDappartement`),
  ADD CONSTRAINT `FK_reservation_locataire` FOREIGN KEY (`IDlocataire`) REFERENCES `locataire` (`IDlocataire`);

--
-- Contraintes pour la table `station`
--
ALTER TABLE `station`
  ADD CONSTRAINT `station_ibfk_1` FOREIGN KEY (`IDregion`) REFERENCES `region` (`IDregion`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;