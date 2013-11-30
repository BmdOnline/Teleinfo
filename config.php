<?php

/***********************/
/*    Données MySQL    */
/***********************/
$db_serveur="localhost";
$db_base="teleinfo";
$db_table="teleinfo";
$db_login="teleinfo";
$db_pass="teleinfo";

$db_date = "date"; // vaut soit "date" soit "timestamp"
$db_iinst = "iinst1"; // vaut soit "iinst1" soit "inst1"

// Ne pas modifier ces quelques lignes
$db_timestamp["date"] = "UNIX_TIMESTAMP(date)";
$db_rec_date["date"] = "DATE(date)";
$db_select_date["date"] = "UNIX_TIMESTAMP(date) as timestamp, DATE(date) as rec_date, TIME(date) as rec_time";
$db_timestamp["timestamp"] = "timestamp";
$db_rec_date["timestamp"] = "rec_date";
$db_select_date["timestamp"] = "timestamp, rec_date, rec_time";
$db_select_mesures["iinst1"]="ptec, papp, iinst1";
$db_select_max_mesures["iinst1"]="MAX(papp) AS maxpapp, MAX(iinst1) AS maxiinst1";
$db_select_mesures["inst1"]="ptec, papp, inst1 as iinst1";
$db_select_max_mesures["inst1"]="MAX(papp) AS maxpapp, MAX(inst1) AS maxiinst1";

/*********************/
/*    Données EDF    */
/*********************/
$nbPhasesCompteur = 1; // 1 pour monophasé ou 3 pour triphasé

// Quelques informations sur Teleinfo et les formules EDF :
//   http://www.yadnet.com/index.php?page=protocole-teleinfo
$tarif_type = "BASE"; // vaut soit "BASE", soit "HCHP", soit "EJP.", soit "BBRX" (TEMPO)
$tarif_jour = ""; // blanc ou si type "BBRX" (TEMPO) "JB"=Bleu / "JW"=Jaune / "JR"=Rouge

function getTarifs($tarif_type) {
  global $abo_annuel;
  global $prixBASE;
  global $prixHP;
  global $prixHC;
  global $prixHPJB;
  global $prixHCJB;
  global $prixHPJW;
  global $prixHCJW;
  global $prixHPJR;
  global $prixHCJR;

  // prix du kWh :
  switch($tarif_type) {
    // Tarif de base
    case "BASE" :
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
      // prix tarif Base EDF
      $prixBASE = (0.1249+0.009+0.009)*1.196; // kWh + CSPE + TCFE, TVA 19.6%
      // Abpnnement pour disjoncteur 9 kVA
      $abo_annuel = 12*(7.34+1.92/2)*1.055; // Abonnement + CTA, TVA 5.5%
      break;

    // Tarif Heures Creuses / Heures pleines
    case "HCHP" :
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
      // prix tarif HP/HC EDF
      $prixBASE = 0;
      $prixHP = 0.1467;
      $prixHC = 0.1002;
      // Abonnement pour disjoncteur 9 kVA
      $abo_annuel = 137.01*1.055; // Abonnement + CTA, TVA 5.5%
      break;

    case "BBRX" :
    // Tarif Tempo
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
      // prix tarif Tempo EDF
      $prixHPJB = 0.0725;
      $prixHCJB = 0.0869;
      $prixHPJW = 0.1036;
      $prixHCJW = 0.1234;
      $prixHPJR = 0.1933;
      $prixHCJR = 0.5081;
      // Abonnement pour disjoncteur 9 A
      $abo_annuel = 12*(8.84+1.92/2)*1.055; // Abonnement + CTA, TVA 5.5%
      break;

    case "EJP." :
    // Tarif EJP (a définir)
      $tab_prix["23/07/2012"] = array(
        "timestamp" => mktime(0,0,0,07,23,2012),
        "HN" => 0,
        "HPM" => 0,
        "AboAnnuel" => 12*(8.84+1.92/2)*1.055 // Abonnement + CTA, TVA 5.5%
      );
      // prix tarif EJP EDF
      $prixBASE = 0;  // prix tarif Base EDF
      $prixHN = 0;
      $prixHPM = 0;
      // Abpnnement pour disjoncteur 9 A
      $abo_annuel = 12*(11.36+1.92/2)*1.055; // Abonnement + CTA, TVA 5.5%
      break;
  };

  return $tab_prix;
}

function queryInstantly () {
  global $db_date, $db_select_date, $db_iinst, $db_select_mesures;
  global $db_table;

  $query="SELECT $db_select_date[$db_date], $db_select_mesures[$db_iinst]
    FROM `$db_table`
    WHERE $db_date=(select max($db_date) FROM `$db_table`)";

  return $query;
}

function queryDaily ($timestampdebut, $timestampfin) {
  global $db_date, $db_timestamp, $db_select_date, $db_iinst, $db_select_mesures;
  global $db_table;

  $query="SELECT $db_select_date[$db_date], $db_select_mesures[$db_iinst]
    FROM `$db_table`
    WHERE $db_timestamp[$db_date] BETWEEN $timestampdebut and $timestampfin
    ORDER BY $db_date";

  return $query;
}

function queryHistory ($timestampdebut, $dateformatsql, $timestampfin) {
  global $db_date, $db_timestamp, $db_rec_date, $db_select_date;
  global $db_table, $tarif_type;

  switch ($tarif_type) {
    case "BASE" :
      $select_hist =
        "ROUND(((MAX(`base`) - MIN(`base`)) / 1000), 1) AS base";
      break;

    case "HCHP" :
      $select_hist =
        "ROUND(((MAX(`hchp`) - MIN(`hchp`)) / 1000), 1) AS hp,
         ROUND(((MAX(`hchc`) - MIN(`hchc`)) / 1000), 1) AS hc";
      break;

    case "BBRX" :
      $select_hist =
        "ROUND(((MAX(`bbrhpjb`) - MIN(`bbrhpjb`)) / 1000), 1) AS hpjb,
         ROUND(((MAX(`bbrhcjb`) - MIN(`bbrhcjb`)) / 1000), 1) AS hcjb,
         ROUND(((MAX(`bbrhpjw`) - MIN(`bbrhpjw`)) / 1000), 1) AS hpjw,
         ROUND(((MAX(`bbrhcjw`) - MIN(`bbrhcjw`)) / 1000), 1) AS hcjw,
         ROUND(((MAX(`bbrhpjr`) - MIN(`bbrhpjr`)) / 1000), 1) AS hpjr,
         ROUND(((MAX(`bbrhcjr`) - MIN(`bbrhcjr`)) / 1000), 1) AS hcjr";
      break;

    case "EJP." :
      $select_hist = "";
        "ROUND(((MAX(`ejphn`) - MIN(`ejphn`)) / 1000), 1) AS hn,
         ROUND(((MAX(`ejphpm`) - MIN(`ejphpm`)) / 1000), 1) AS hpm";
      break;
  }
  $query="SELECT $db_select_date[$db_date], DATE_FORMAT($db_rec_date[$db_date], '$dateformatsql') AS 'periode',
    $select_hist
    FROM `$db_table`
    WHERE $db_timestamp[$db_date] BETWEEN $timestampdebut and $timestampfin
    GROUP BY periode
    ORDER BY rec_date" ;

  return $query;
}

function queryMaxPeriod ($timestampdebut, $timestampfin) {
  global $db_date, $db_timestamp, $db_iinst, $db_select_max_mesures;
  global $db_table;

  $query="SELECT $db_select_max_mesures[$db_iinst]
    FROM `$db_table`
    WHERE $db_timestamp[$db_date] BETWEEN $timestampdebut and $timestampfin
    ORDER BY $db_date";

  return $query;
}

?>
