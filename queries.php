<?php

// Convertion timestamp vs date
$variantes_sql = array (
    "TIMESTAMP" => array (
        "DATE" => "UNIX_TIMESTAMP(%field%)",
        "TIMESTAMP" => "%field%"
    ),
    "DATE" => array (
        "DATE" => "%field%",
        "TIMESTAMP" => "DATE(FROM_UNIXTIME(%field%))"
    )
);


// Retourne l'option souscrite pour la période choisie
// En cas de modification d'abonnement EDF durant la période,
//   on peut avoir plusieurs options.
// Pour l'instant, ce n'est pas géré par le programme :
//   on gère alors l'option courante.
function queryOPTARIF() {
    global $db_connect, $config_table, $variantes_sql;

    $mesures = array ("OPTARIF", "ISOUSC");

    // Select
    $query = "SELECT ";
    // Mesures
    foreach($mesures as $field){
        $query .= $config_table["table"][$field] . " AS " . $field . ", ";
    }
    // Suppression de la dernière virgule
    $query = substr($query, 0, -2) . " ";
    // From
    $query .= "FROM " . $db_connect['table'] . " ";
    // Where
    $query .= str_replace(
        array("%date%", "%table%"),
        array($config_table["table"]["DATE"], $db_connect['table']),
        "WHERE %date%=(SELECT MAX(%date%) FROM %table%)");

    // SELECT OPTARIF AS OPTARIF, ISOUSC AS ISOUSC
    // FROM tbTeleinfo
    // WHERE DATE=(SELECT MAX(DATE) FROM tbTeleinfo)
    return $query;
}

// Retourne l'intensité et la puissance maximales pour la période donnée
function queryMaxPeriod ($timestampdebut, $timestampfin) {
    global $db_connect, $config_table, $variantes_sql;

    $mesures = array ("PAPP", "IINST1");

    $tDate = strtoupper($config_table["type_date"]);
    $timestamp = str_replace ("%field%", $config_table["table"]["DATE"], $variantes_sql["TIMESTAMP"][$tDate]);
    $date = str_replace ("%field%", $config_table["table"]["DATE"], $variantes_sql["DATE"][$tDate]);

    // Select
    $query = "SELECT ";
    // Mesures
    foreach($mesures as $field){
        $query .= str_replace(
            array("%field%", "%mesure%"),
            array($config_table["table"][$field], $field),
            "MAX(`%field%`) as %mesure%, ");
    }
    // Suppression de la dernière virgule
    $query = substr($query, 0, -2) . " ";
    // From
    $query .= "FROM " . $db_connect['table'] . " ";
    // Where
    $query .= str_replace(
        array("%date%", "%debut%", "%fin%"),
        array($timestamp, $timestampdebut, $timestampfin),
        "WHERE %date% BETWEEN %debut% and %fin%") . " ";
    // Order By
    $query .= "ORDER BY " . $config_table["table"]["DATE"];

    // SELECT MAX(`PAPP`) as PAPP, MAX(`IINST1`) as IINST1,
    // FROM tbTeleTempo
    // WHERE UNIX_TIMESTAMP(DATE) BETWEEN xxxx and yyyy
    // ORDER BY DATE
    return $query;
}

function queryMaxDate () {
    global $db_connect, $config_table, $variantes_sql;

    $mesures = array ("PAPP", "IINST1");

    $tDate = strtoupper($config_table["type_date"]);
    $timestamp = str_replace ("%field%", $config_table["table"]["DATE"], $variantes_sql["TIMESTAMP"][$tDate]);

    // Select
    $query = "SELECT ";
    // Max
    $query .= str_replace(
        "%date%", $timestamp,
        "MAX(%date%) AS DATE ");
    // From
    $query .= "FROM " . $db_connect['table'] . " ";

    // SELECT MAX(`DATE`) as DATE
    // FROM tbTeleTempo
    return $query;
}

function queryInstantly () {
    global $db_connect, $config_table, $variantes_sql;

    $mesures = array ("OPTARIF", "IINST1", "PAPP", "DEMAIN");

    $tDate = strtoupper($config_table["type_date"]);
    $timestamp = str_replace ("%field%", $config_table["table"]["DATE"], $variantes_sql["TIMESTAMP"][$tDate]);

    // Select Timestamp
    $query = "SELECT " . $timestamp . " AS TIMESTAMP, ";
    // Mesures
    foreach($mesures as $field){
        $query .= $config_table["table"][$field] . " AS " . $field . ", ";
    }
    // Suppression de la dernière virgule
    $query = substr($query, 0, -2) . " ";
    // From
    $query .= "FROM " . $db_connect['table'] . " ";
    // Where
    $query .= str_replace(
        array("%date%", "%table%"),
        array($config_table["table"]["DATE"], $db_connect['table']),
        "WHERE %date%=(SELECT MAX(%date%) FROM %table%)");

    // SELECT UNIX_TIMESTAMP(DATE) as TIMESTAMP,
    //   OPTARIF AS OPTARIF, IINST1 AS IINST1, PAPP AS PAPP, DEMAIN AS DEMAIN
    // FROM tbTeleinfo
    // WHERE DATE=(SELECT MAX(DATE) FROM tbTeleinfo)
    return $query;
}

function queryDaily ($timestampdebut, $timestampfin) {
    global $db_connect, $config_table, $variantes_sql;

    $mesures = array ("PTEC", "IINST1", "PAPP");

    $tDate = strtoupper($config_table["type_date"]);
    $timestamp = str_replace ("%field%", $config_table["table"]["DATE"], $variantes_sql["TIMESTAMP"][$tDate]);

    // Select Timestamp
    $query = "SELECT " . $timestamp . " AS TIMESTAMP, ";
    // Mesures
    foreach($mesures as $field){
        $query .= $config_table["table"][$field] . " AS " . $field . ", ";
    }
    // Suppression de la dernière virgule
    $query = substr($query, 0, -2) . " ";
    // From
    $query .= "FROM " . $db_connect['table'] . " ";
    // Where
    $query .= str_replace(
        array("%date%", "%debut%", "%fin%"),
        array($timestamp, $timestampdebut, $timestampfin),
        "WHERE %date% BETWEEN %debut% and %fin% ");
    // Order
    $query .= "ORDER BY " . $config_table["table"]["DATE"];

    // SELECT UNIX_TIMESTAMP(DATE) as TIMESTAMP,
    //   IINST1 AS IINST1, PAPP AS PAPP
    // FROM tbTeleinfo
    // WHERE UNIX_TIMESTAMP(DATE) BETWEEN xxxx and yyyy
    // ORDER BY DATE
    return $query;
}

function queryHistory ($optarif, $timestampdebut, $dateformatsql, $timestampfin) {
    global $db_connect, $config_table, $variantes_sql;
    global $teleinfo;

    // $optarif = getOpTarif(); // Passé en paramètre

    $mesures = array ("OPTARIF");

    $tDate = strtoupper($config_table["type_date"]);
    $timestamp = str_replace ("%field%", $config_table["table"]["DATE"], $variantes_sql["TIMESTAMP"][$tDate]);
    $date = str_replace ("%field%", $config_table["table"]["DATE"], $variantes_sql["DATE"][$tDate]);

    // Select Timestamp
    $query = "SELECT " . $timestamp . " AS TIMESTAMP, ";
    // Période
    $query .= str_replace(
        array("%field%", "%format%"),
        array($date, $dateformatsql),
        "DATE_FORMAT(%field%, '%format%') AS PERIODE, ");
    // Mesures
    foreach($mesures as $field){
        $query .= $config_table["table"][$field] . " AS " . $field . ", ";
    }
    // Max-Min
    foreach($teleinfo["PERIODES"][$optarif] as $field){
        $query .= str_replace(
            array("%field%", "%mesure%"),
            array($config_table["table"][$field], $field),
            "ROUND(((MAX(`%field%`) - MIN(`%field%`)) / 1000), 1) AS %mesure%, ");
    }
    // Suppression de la dernière virgule
    $query = substr($query, 0, -2) . " ";
    // From
    $query .= "FROM " . $db_connect['table'] . " ";
    // Where
    $query .= str_replace(
        array("%date%", "%debut%", "%fin%"),
        array($timestamp, $timestampdebut, $timestampfin),
        "WHERE %date% BETWEEN %debut% and %fin%") . " ";
    // Group By
    $query .= "GROUP BY PERIODE ";
    // Order By
    $query .= "ORDER BY " . $config_table["table"]["DATE"];

    // SELECT UNIX_TIMESTAMP(DATE) AS TIMESTAMP, DATE_FORMAT(DATE, '%a %e') AS PERIODE,
    //   OPTARIF AS OPTARIF,
    //   ROUND(((MAX(`BBRHPJB`) - MIN(`BBRHPJB`)) / 1000), 1) AS HPJB,
    //   ROUND(((MAX(`BBRHPJW`) - MIN(`BBRHPJW`)) / 1000), 1) AS HPJW,
    //   ROUND(((MAX(`BBRHPJR`) - MIN(`BBRHPJR`)) / 1000), 1) AS HPJR,
    //   ROUND(((MAX(`BBRHCJB`) - MIN(`BBRHCJB`)) / 1000), 1) AS HCJB,
    //   ROUND(((MAX(`BBRHCJW`) - MIN(`BBRHCJW`)) / 1000), 1) AS HCJW,
    //   ROUND(((MAX(`BBRHCJR`) - MIN(`BBRHCJR`)) / 1000), 1) AS HCJR
    // FROM tbTeleTempo
    // WHERE UNIX_TIMESTAMP(DATE) BETWEEN xxxx and yyyy
    // ORDER BY PERIODE, DATE
    return $query;
}

?>
