<?php

// Connexion MySql et requête.
$serveur="localhost";
$login="teleinfo";
$base="teleinfo";
$table="teleinfo";
$pass="teleinfo";

$nbPhasesCompteur = 1; // 1 pour monophasé ou 3 pour triphasé

$tarif_type = "BASE"; // vaut soit "BASE", soit "HCHP", soit "EJP.", soit "BBRX" (TEMPO)
$tarif_jour = ""; // blanc ou si type "BBRX" (TEMPO) "JB"=Bleu / "JW"=Jaune / "JR"=Rouge

// prix du kWh :
// prix TTC au 1/01/2012 :
// prix TTC au 23/07/2012 :
if ( $tarif_type == "BASE") {
  // prix tarif Base EDF
  $prixBASE = (0.1249+0.009+0.009)*1.196; // kWh + CSPE + TCFE, TVA 19.6%
  // Abpnnement pour disjoncteur 9 kVA
  $abo_annuel = 12*(7.34+1.92/2)*1.055; // Abonnement + CTA, TVA 5.5%

  $tab_prix["1/01/2012"] = array(
    "timestamp" => mktime(0,0,0,01,01,2012),
    "Base" => (0.0812+0.009+0.009)*1.196, // kWh + CSPE + TCFE, TVA 19.6%
    "AboAnnuel" => 12*(5.36+1.92/2)*1.055 // Abonnement + CTA, TVA 5.5%
  );
  $tab_prix["23/07/2012"] = array(
    "timestamp" => mktime(0,0,0,07,23,2012),
    "Base" => (0.1249+0.009+0.009)*1.196, // kWh + CSPE + TCFE, TVA 19.6%
    "AboAnnuel" => 12*(7.34+1.92/2)*1.055 // Abonnement + CTA, TVA 5.5%
  );
  $tab_prix["01/08/2013"] = array(
    "timestamp" => mktime(0,0,0,08,01,2013),
    "Base" => 0.1329, // kWh + CSPE + TCFE, TVA 19.6%
    "AboAnnuel" => 125.13*1.055 // Abonnement + CTA, TVA 5.5%
  );
}
// Tarif Heures Creuses / Heures pleines
if ( $tarif_type == "HCHP") {

  $tab_prix["1/01/2012"] = array(
    "timestamp" => mktime(0,0,0,01,01,2012),
    "HP" => 0.1312,
    "HC" => 0.0895,
    "AboAnnuel" => 112.87 // Abonnement + CTA, TVA 5.5%
  );
  $tab_prix["23/07/2012"] = array(
    "timestamp" => mktime(0,0,0,07,23,2012),
    "HP" => 0.1353,
    "HC" => 0.0926,
    "AboAnnuel" => 12*(9.07+1.92/2)*1.055 // Abonnement + CTA, TVA 5.5%
  );
  $tab_prix["01/08/2013"] = array(
    "timestamp" => mktime(0,0,0,08,01,2013),
    "HP" => 0.1467,
    "HC" => 0.1002,
    "AboAnnuel" => 137.01*1.055 // Abonnement + CTA, TVA 5.5%
  );
  // prix tarif Base EDF
  $prixBASE = 0;
  $prixHP = 0.1467;
  $prixHC = 0.1002;
  // Abpnnement pour disjoncteur 9 kVA
  $abo_annuel = 137.01*1.055; // Abonnement + CTA, TVA 5.5%
}
// Tarif Tempo
if ( $tarif_type == "BBRX") {
  $tab_prix["23/07/2012"] = array(
    "timestamp" => mktime(0,0,0,07,23,2012),
    "HPJB" => 0.0725,
    "HCJB" => 0.0869,
    "HPJW" => 0.1036,
    "HCJW" => 0.1234,
    "HPJR" => 0.1933,
    "HCJR" => 0.5081,
    "AboAnnuel" => 12*(8.84+1.92/2)*1.055 // Abonnement + CTA, TVA 5.5%
  );
  // prix tarif EDF
  $prixHPJB = 0.0725;
  $prixHCJB = 0.0869;
  $prixHPJW = 0.1036;
  $prixHCJW = 0.1234;
  $prixHPJR = 0.1933;
  $prixHCJR = 0.5081;
  // Abpnnement pour disjoncteur 9 A
  $abo_annuel = 12*(8.84+1.92/2)*1.055; // Abonnement + CTA, TVA 5.5%
}
/*
if ( $tarif_type == "EJP.") {
  // prix tarif Base EDF
  $prixBASE = (0.1249+0.009+0.009)*1.196; // kWh + CSPE + TCFE, TVA 19.6%  // prix tarif Base EDF
  $prixHP = 0.5329;
  $prixHC = 0.1030;
  // Abpnnement pour disjoncteur 9 A
  $abo_annuel = 12*(11.36+1.92/2)*1.055; // Abonnement + CTA, TVA 5.5%
}
*/


/* Requêtes à adapter en fonction de la structure de votre table
   Base de donnée Téléinfo:

Format de la table BASE/HCP/EJP (hchc --> hn.. et hchp --> pm..):
timestamp   rec_date   rec_time   adco     optarif isousc   hchp     hchc     ptec   inst1   inst2   inst3   imax1   imax2   imax3   pmax   papp   hhphc   motdetat   ppot   adir1   adir2   adir3
1234998004   2009-02-19   00:00:04   700609361116   HC..   09   11008167   10490114   HP   1   0   1   18   23   22   8780   400   E   000000     00   0   0   0
1234998065   2009-02-19   00:05:05   700609361116   HC..   09   11008273   10490214   HP   1   0   1   18   23   22   8780   400   E   000000     00   0   0   0
1234998124   2009-02-19   00:10:04   700609361116   HC..   09   11008379   10490314   HP   1   0   1   18   23   22   8780   390   E   000000     00   0   0   0
1234998185   2009-02-19   00:15:05   700609361116   HC..   09   11008484   10490414   HP   1   0   0   18   23   22   8780   330   E   000000     00   0   0   0
1234998244   2009-02-19   00:20:04   700609361116   HC..   09   11008589   10490514   HP   1   0   0   18   23   22   8780   330   E   000000     00   0   0   0
1234998304   2009-02-19   00:25:04   700609361116   HC..   09   11008693   10490614   HP   1   0   0   18   23   22   8780   330   E   000000     00   0   0   0
1234998365   2009-02-19   00:30:05   700609361116   HC..   09   11008798   10490714   HP   1   0   0   18   23   22   8780   320   E   000000     00   0   0   0

Script de création de la table pour les tarifs BASE/HC/HP/EJP
CREATE TABLE IF NOT EXISTS `teleinfo` (
	`timestamp` BIGINT(10) NOT NULL DEFAULT '0',
	`rec_date` DATE NOT NULL DEFAULT '0000-00-00',
	`rec_time` TIME NOT NULL DEFAULT '00:00:00',
	`adco` VARCHAR(12) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
	`optarif` VARCHAR(4) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
	`isousc` tinyint(2) NOT NULL DEFAULT '0',
	`hchp` BIGINT(9) NOT NULL DEFAULT '0',
	`hchc` BIGINT(9) NOT NULL DEFAULT '0',
	`ptec` VARCHAR(2) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
	`inst1` tinyint(3) NOT NULL DEFAULT '0',
	`inst2` tinyint(3) NOT NULL DEFAULT '0',
	`inst3` tinyint(3) NOT NULL DEFAULT '0',
	`imax1` tinyint(3) NOT NULL DEFAULT '0',
	`imax2` tinyint(3) NOT NULL DEFAULT '0',
	`imax3` tinyint(3) NOT NULL DEFAULT '0',
	`pmax` INT(5) NOT NULL DEFAULT '0',
	`papp` INT(5) NOT NULL DEFAULT '0',
	`hhphc` VARCHAR(1) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
	`motdetat` VARCHAR(6) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
	`ppot` VARCHAR(2) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
	`adir1` tinyint(3) NOT NULL DEFAULT '0',
	`adir2` tinyint(3) NOT NULL DEFAULT '0',
	`adir3` tinyint(3) NOT NULL DEFAULT '0',
	UNIQUE KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

Format de la table TEMPO:
timestamp   rec_date   rec_time   adco     optarif isousc    bbrhcjb    bbrhpjb    bbrhcjw    bbrhpjw    bbrhcjr    bbrhpjr    ptec   inst1   inst2   inst3   imax1   imax2   imax3   pmax   papp   hhphc   motdetat   ppot   adir1   adir2   adir3
1234998004   2009-02-19   17:00:04   700609361116   BBRX   09   11008167   10490114   10490114   10490114   10490114   10490114   HPJB  1   0   1   18   23   22   8780   400   E   000000     00   0   0   0
1234998004   2009-02-19   17:05:04   700609361116   BBRX   09   11008267   10490214   10490214   10490214   10490214   10490214   HPJB  1   0   1   18   23   22   8780   400   E   000000     00   0   0   0
1234998004   2009-02-19   17:10:04   700609361116   BBRX   09   11008367   10490314   10490314   10490314   10490314   10490314   HPJB  1   0   1   18   23   22   8780   400   E   000000     00   0   0   0
1234998004   2009-02-19   17:15:04   700609361116   BBRX   09   11008467   10490414   10490414   10490414   10490414   10490414   HPJB  1   0   1   18   23   22   8780   400   E   000000     00   0   0   0
1234998004   2009-02-19   17:20:04   700609361116   BBRX   09   11008567   10490514   10490514   10490514   10490514   10490514   HPJB  1   0   1   18   23   22   8780   400   E   000000     00   0   0   0
1234998004   2009-02-19   17:25:04   700609361116   BBRX   09   11008667   10490614   10490614   10490614   10490614   10490614   HPJB  1   0   1   18   23   22   8780   400   E   000000     00   0   0   0
1234998004   2009-02-19   17:30:04   700609361116   BBRX   09   11008767   10490714   10490714   10490714   10490714   10490714   HPJB  1   0   1   18   23   22   8780   400   E   000000     00   0   0   0
1234998004   2009-02-19   17:35:04   700609361116   BBRX   09   11008867   10490814   10490814   10490814   10490814   10490814   HPJB  1   0   1   18   23   22   8780   400   E   000000     00   0   0   0

Script de création de la table pour le tarif TEMPO
CREATE TABLE IF NOT EXISTS `teleinfo` (
  `timestamp` bigint(10) NOT NULL default '0',
  `rec_date` date NOT NULL default '0000-00-00',
  `rec_time` time NOT NULL default '00:00:00',
  `adco` varchar(12) character set latin1 collate latin1_general_ci NOT NULL,
  `optarif` varchar(4) character set latin1 collate latin1_general_ci NOT NULL,
  `isousc` tinyint(2) NOT NULL default '0',
  `bbrhcjb` bigint(9) NOT NULL default '0',
  `bbrhpjb` bigint(9) NOT NULL default '0',
  `bbrhcjw` bigint(9) NOT NULL default '0',
  `bbrhpjw` bigint(9) NOT NULL default '0',
  `bbrhcjr` bigint(9) NOT NULL default '0',
  `bbrhpjr` bigint(9) NOT NULL default '0',
  `ptec` varchar(2) character set latin1 collate latin1_general_ci NOT NULL,
  `demain` varchar(5) character set latin1 collate latin1_general_ci NOT NULL,
  `iinst` tinyint(3) NOT NULL default '0',
  `adps` tinyint(3) NOT NULL default '0',
  `imax` tinyint(3) NOT NULL default '0',
  `papp` int(5) NOT NULL default '0',
  `hhphc` varchar(1) character set latin1 collate latin1_general_ci NOT NULL,
  `motdetat` varchar(6) character set latin1 collate latin1_general_ci NOT NULL,
  UNIQUE KEY `timestamp` (`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

*/

function queryinstantly () {
  global $table, $tarif_type;

  if ( $tarif_type == "BASE") {
      $query="SELECT unix_timestamp(date) as timestamp, date(date) as rec_date, time(date) as rec_time, ptec, papp, iinst1
        FROM `$table`
        WHERE date=(select max(date) FROM `$table`)";
  }

  if ( $tarif_type == "HCHP") {
      $query="SELECT timestamp, rec_date, rec_time, ptec, papp, inst1 AS iinst1
        FROM `$table`
        WHERE date=(select max(timestamp) FROM `$table`)";
  }

  return $query;
}

function querydaily ($timestampdebut, $timestampfin) {
  global $table, $tarif_type;

  if ( $tarif_type == "BASE") {
    $query="SELECT UNIX_TIMESTAMP(date) AS timestamp, date(date) AS rec_date, time(date) AS rec_time, ptec, papp, iinst1
      FROM `$table`
      WHERE UNIX_TIMESTAMP(date) >= $timestampdebut
      AND UNIX_TIMESTAMP(date) < $timestampfin
      ORDER BY UNIX_TIMESTAMP(date)";
  }

  if ( $tarif_type == "HCHP") {
    $query="SELECT timestamp, rec_date, rec_time, ptec, papp, inst1  AS iinst1
      FROM `$table`
      WHERE timestamp >= $timestampdebut
      AND timestamp < $timestampfin
      ORDER BY timestamp";
  }

  return $query;
}

function queryhistory ($timestampdebut, $dateformatsql, $timestampfin) {
  global $table, $tarif_type;

  if ( $tarif_type == "BASE") {
    $query="SELECT unix_timestamp(date) as timestamp, date(date) as rec_date, DATE_FORMAT(date(date), '$dateformatsql') AS 'periode' ,
      ROUND( ((MAX(`base`) - MIN(`base`)) / 1000) ,1 ) AS base
      FROM `$table`
      WHERE unix_timestamp(date) >= '$timestampdebut'
      AND unix_timestamp(date) < '$timestampfin'
      GROUP BY periode
      ORDER BY rec_date" ;
  }

  if ( $tarif_type == "BBRX") {
    $query="SELECT rec_date, DATE_FORMAT(rec_date, '$dateformatsql') AS 'periode' ,
      ROUND( ((MAX(`bbrhpjb`) - MIN(`bbrhpjb`)) / 1000) ,1 ) AS hpjb,
      ROUND( ((MAX(`bbrhcjb`) - MIN(`bbrhcjb`)) / 1000) ,1 ) AS hcjb,
      ROUND( ((MAX(`bbrhpjw`) - MIN(`bbrhpjw`)) / 1000) ,1 ) AS hpjw,
      ROUND( ((MAX(`bbrhcjw`) - MIN(`bbrhcjw`)) / 1000) ,1 ) AS hcjw,
      ROUND( ((MAX(`bbrhpjr`) - MIN(`bbrhpjr`)) / 1000) ,1 ) AS hpjr,
      ROUND( ((MAX(`bbrhcjr`) - MIN(`bbrhcjr`)) / 1000) ,1 ) AS hcjr

      FROM `$table`
      WHERE timestamp > '$timestampdebut'
      GROUP BY periode
      ORDER BY rec_date" ;
  }

  if ( $tarif_type == "HCHP") {
    $query="SELECT timestamp, rec_date, DATE_FORMAT(rec_date, '$dateformatsql') AS 'periode' ,
      ROUND( ((MAX(`hchp`) - MIN(`hchp`)) / 1000) ,1 ) AS hp,
      ROUND( ((MAX(`hchc`) - MIN(`hchc`)) / 1000) ,1 ) AS hc
      FROM `$table`
      WHERE timestamp >= '$timestampdebut'
      AND timestamp < '$timestampfin'
      GROUP BY periode
      ORDER BY rec_date" ;
  }

  return $query;
}

function queryMaxPeriod ($timestampdebut, $timestampfin) {
  global $table, $tarif_type;

  if ( $tarif_type == "BASE") {
    $query="SELECT UNIX_TIMESTAMP(date) AS timestamp, date(date) AS rec_date, time(date) AS rec_time, ptec, papp, iinst1
      FROM `$table`
      WHERE UNIX_TIMESTAMP(date) >= $timestampdebut
      AND UNIX_TIMESTAMP(date) < $timestampfin
      ORDER BY UNIX_TIMESTAMP(date)";
  }

  if ( $tarif_type == "HCHP") {
    $query="SELECT MAX(papp) AS maxpapp, MAX(inst1) AS maxiinst1
      FROM `$table`
      WHERE timestamp >= $timestampdebut
      AND timestamp < $timestampfin
      ORDER BY timestamp";
  }

  return $query;
}

?>
