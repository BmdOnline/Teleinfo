--
-- Structure de la table `tbTeleinfo`
--

CREATE TABLE IF NOT EXISTS `tbTeleinfo` (
  `DATE` datetime DEFAULT NULL,
  `ADCO` varchar(12) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `OPTARIF` varchar(4) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `ISOUSC` varchar(2) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `BASE` varchar(9) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `HCHC` varchar(9) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `HCHP` varchar(49) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `EJPHN` varchar(9) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `EJPHPM` varchar(9) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `BBRHCJB` varchar(9) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `BBRHPJB` varchar(9) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `BBRHCJW` varchar(9) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `BBRHPJW` varchar(9) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `BBRHCJR` varchar(9) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `BBRHPJR` varchar(9) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `PEJP` varchar(2) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `PTEC` varchar(4) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `DEMAIN` varchar(4) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `IINST` varchar(3) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `ADPS` varchar(4) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `IMAX` varchar(4) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `IINST1` varchar(3) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `IINST2` varchar(3) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `IINST3` varchar(3) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `IMAX1` varchar(3) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `IMAX2` varchar(3) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `IMAX3` varchar(3) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `PMAX` varchar(5) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `PAPP` varchar(5) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `HHPHC` varchar(5) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `MOTDETAT` varchar(6) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  `PPOT` varchar(2) CHARACTER SET latin1 COLLATE latin1_general_ci DEFAULT NULL,
  KEY `SEARCH_INDEX` (`ADCO`,`DATE`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

