<?php

// Conversion timestamp vs date
$date_sql = array (
    "TIMESTAMP" => array (
        "DATE" => "UNIX_TIMESTAMP(%field%)",
        "TIMESTAMP" => "%field%"
    ),
    "DATE" => array (
        "DATE" => "%field%",
        "TIMESTAMP" => "DATE(FROM_UNIXTIME(%field%))"
    )
);

// Conversion timestamp vs date
function date_php ($dateIn, $typeIn, $typeOut) {
    switch ($typeIn) {
        case "DATE":
            switch ($typeOut) {
                case "DATE":
                    // Date vers Date
                    $dateOut = $dateIn;
                    break;
                case "TIMESTAMP":
                default :
                    // Date vers Timestamp
                    $datetime = new DateTime($dateIn);
                    $datetime->setTimezone(new DateTimeZone('Europe/Paris'));
                    $dateOut = $datetime->format("U"); // PHP 5.2
                    //$dateOut = $datetime->getTimestamp();
                    break;
            }
            break;
        case "TIMESTAMP":
            switch ($typeOut) {
                case "TIMESTAMP":
                    // Timestamp vers Timestamp
                    $dateOut = $dateIn;
                    break;
                case "DATE":
                default :
                    // Timestamp vers Date
                    $datetime = new DateTime("@$dateIn"); // PHP 5.2
                    $datetime->setTimezone(new DateTimeZone('Europe/Paris'));
                    //$datetime->setTimestamp($dateIn);
                    $dateOut = $datetime->format("Y-m-d H:i:s");
                    break;
            }
            break;
        default :
            break;
    }

    return $dateOut;
}

// Retourne l'option souscrite pour la période choisie
// En cas de modification d'abonnement EDF durant la période,
//   on peut avoir plusieurs options.
// Pour l'instant, ce n'est pas géré par le programme :
//   on gère alors l'option courante.
function queryOPTARIF() {
    global $db_connect, $config_table, $date_sql;

    $mesures = array ("OPTARIF", "ISOUSC");

    // Select
    $query = "SELECT ";
    // Mesures
    foreach($mesures as $field) {
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
    // $query .= "ORDER BY ".$config_table["table"]["DATE"]." DESC LIMIT 1;";

    // Variante 1
    // SELECT OPTARIF AS OPTARIF, ISOUSC AS ISOUSC
    // FROM tbTeleinfo
    // WHERE DATE=(SELECT MAX(DATE) FROM tbTeleinfo)

    // Variante 2 (spécifique MySQL)
    // SELECT OPTARIF AS OPTARIF, ISOUSC AS ISOUSC
    // FROM tbTeleinfo
    // ORDER BY DATE DESC
    // LIMIT 1

    return $query;
}

// Retourne l'intensité et la puissance maximales pour la période donnée
function queryMaxPeriod ($timestampdebut, $timestampfin, $optarif = null) {
    global $db_connect, $config_table, $date_sql;
    global $teleinfo;

    $mesures = array ("PAPP", "IINST1");

    $tDate = strtoupper($config_table["type_date"]);
    $timestamp = str_replace ("%field%", $config_table["table"]["DATE"], $date_sql["TIMESTAMP"][$tDate]);
    $date = str_replace ("%field%", $config_table["table"]["DATE"], $date_sql["DATE"][$tDate]);

    // Select
    $query = "SELECT ";
    // Mesures
    foreach($mesures as $field) {
        $query .= str_replace(
            array("%field%", "%mesure%"),
            array($config_table["table"][$field], $field),
            "MAX(%field%) as %mesure%, "); //"MAX(`%field%`) as %mesure%, ");
    }
    // Différents index
    if ($optarif) {
        foreach($teleinfo["PERIODES"][$optarif] as $field) {
            $query .= str_replace(
                array("%field%", "%mesure%"),
                array($config_table["table"][$field], $field),
                "MAX(%field%) as %mesure%, "); //"MAX(`%field%`) as %mesure%, ");
        }
    }
    // Suppression de la dernière virgule
    $query = substr($query, 0, -2) . " ";
    // From
    $query .= "FROM " . $db_connect['table'] . " ";
    // Where
    $datetimedebut = date_php($timestampdebut, "TIMESTAMP", $tDate);
    $datetimefin = date_php($timestampfin, "TIMESTAMP", $tDate);
    //$datefield = str_replace ("%field%", $config_table["table"]["DATE"], $date_sql["DATE"][$tDate]);
    $datefield = $config_table["table"]["DATE"];

    $query .= str_replace(
        array("%date%", "%debut%", "%fin%"),
        array($datefield, $datetimedebut, $datetimefin),
        "WHERE %date% BETWEEN '%debut%' and '%fin%' ");
    // Order By
    $query .= "ORDER BY " . $config_table["table"]["DATE"];

    // SELECT MAX(PAPP) as PAPP, MAX(IINST1) as IINST1, MAX(BASE) as BASE
    // FROM tbTeleinfo
    // WHERE DATE BETWEEN 'Y-m-d H:i:s' and 'Y-m-d H:i:s'
    // ORDER BY DATE'

    // SELECT MAX(`PAPP`) as PAPP, MAX(`IINST1`) as IINST1,
    // FROM tbTeleTempo
    // WHERE UNIX_TIMESTAMP(DATE) BETWEEN xxxx and yyyy
    // ORDER BY DATE
    return $query;
}

function queryMaxDate () {
    global $db_connect, $config_table, $date_sql;

    $mesures = array ("PAPP", "IINST1");

    $tDate = strtoupper($config_table["type_date"]);
    $timestamp = str_replace ("%field%", $config_table["table"]["DATE"], $date_sql["TIMESTAMP"][$tDate]);

    // Select
    $query = "SELECT ";
    // Max
    $query .= str_replace(
        "%date%", $timestamp,
        "MAX(%date%) AS DATE ");
    // From
    $query .= "FROM " . $db_connect['table'] . " ";

    // SELECT MAX(UNIX_TIMESTAMP(DATE)) AS DATE
    // FROM tbTeleinfo

    // SELECT MAX(`DATE`) as DATE
    // FROM tbTeleTempo
    return $query;
}

function queryInstantly () {
    global $db_connect, $config_table, $date_sql;

    $mesures = array ("PAPP", "IINST1", "ISOUSC", "OPTARIF", "PTEC", "DEMAIN");

    $tDate = strtoupper($config_table["type_date"]);
    $timestamp = str_replace ("%field%", $config_table["table"]["DATE"], $date_sql["TIMESTAMP"][$tDate]);

    // Select Timestamp
    $query = "SELECT " . $timestamp . " AS TIMESTAMP, ";
    // Mesures
    foreach($mesures as $field) {
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

    // SELECT UNIX_TIMESTAMP(DATE) AS TIMESTAMP,
    //   PAPP AS PAPP, IINST1 AS IINST1, ISOUSC AS ISOUSC,
    //   OPTARIF AS OPTARIF, PTEC AS PTEC, DEMAIN AS DEMAIN
    // FROM tbTeleinfo
    // WHERE DATE=(SELECT MAX(DATE) FROM tbTeleinfo)"

    return $query;
}

function queryDaily ($timestampdebut, $timestampfin, $optarif = null) {
    global $db_connect, $config_table, $date_sql;
    global $teleinfo;

    $mesures = array ("OPTARIF", "PTEC", "IINST1", "PAPP");

    $tDate = strtoupper($config_table["type_date"]);
    $timestamp = str_replace ("%field%", $config_table["table"]["DATE"], $date_sql["TIMESTAMP"][$tDate]);

    // Select Timestamp
    $query = "SELECT " . $timestamp . " AS TIMESTAMP, ";
    // Mesures
    foreach($mesures as $field) {
        $query .= $config_table["table"][$field] . " AS " . $field . ", ";
    }
    // Différents index
    if ($optarif) { // if ($config["recalculPuissance"]) {
        foreach($teleinfo["PERIODES"][$optarif] as $field){
            $query .= $config_table["table"][$field] . " AS " . $field . ", ";
        }
    }
    // Suppression de la dernière virgule
    $query = substr($query, 0, -2) . " ";
    // From
    $query .= "FROM " . $db_connect['table'] . " ";
    // Where
    $datetimedebut = date_php($timestampdebut, "TIMESTAMP", $tDate);
    $datetimefin = date_php($timestampfin, "TIMESTAMP", $tDate);
    //$datefield = str_replace ("%field%", $config_table["table"]["DATE"], $date_sql["DATE"][$tDate]);
    $datefield = $config_table["table"]["DATE"];

    $query .= str_replace(
        array("%date%", "%debut%", "%fin%"),
        array($datefield, $datetimedebut, $datetimefin),
        "WHERE %date% BETWEEN '%debut%' and '%fin%' ");
    // Order By
    $query .= "ORDER BY " . $config_table["table"]["DATE"];

    // SELECT UNIX_TIMESTAMP(DATE) AS TIMESTAMP,
    //   OPTARIF AS OPTARIF, PTEC AS PTEC, IINST1 AS IINST1, PAPP AS PAPP
    // FROM tbTeleinfo
    // WHERE DATE BETWEEN 'Y-m-d H:i:s' and 'Y-m-d H:i:s'
    // ORDER BY DATE'

    // SELECT UNIX_TIMESTAMP(DATE) as TIMESTAMP,
    //   OPTARIF AS OPTARIF, PTEC AS PTEC, IINST1 AS IINST1, PAPP AS PAPP
    // FROM tbTeleinfo
    // WHERE UNIX_TIMESTAMP(DATE) BETWEEN xxxx and yyyy
    // ORDER BY DATE
    return $query;
}

function queryHistory ($timestampdebut, $timestampfin, $dateformatsql, $optarif) {
    global $db_connect, $config_table, $date_sql;
    global $teleinfo;

    $mesures = array ("OPTARIF", "PTEC");

    $tDate = strtoupper($config_table["type_date"]);
    $timestamp = str_replace ("%field%", $config_table["table"]["DATE"], $date_sql["TIMESTAMP"][$tDate]);
    $date = str_replace ("%field%", $config_table["table"]["DATE"], $date_sql["DATE"][$tDate]);

    // Select Timestamp
    $query = "SELECT " . $timestamp . " AS TIMESTAMP, ";
    // Période
    $query .= str_replace(
        array("%field%", "%format%"),
        array($date, $dateformatsql),
        "DATE_FORMAT(%field%, '%format%') AS PERIODE, ");
    // Mesures
    foreach($mesures as $field) {
        $query .= $config_table["table"][$field] . " AS " . $field . ", ";
    }
    // Max-Min
    foreach($teleinfo["PERIODES"][$optarif] as $field) {
        $query .= str_replace(
            array("%field%", "%mesure%"),
            array($config_table["table"][$field], $field),
            "ROUND(((MAX(NULLIF(%field%, 0)) - MIN(NULLIF(%field%, 0))) / 1000), 1) AS %mesure%, "); //"ROUND(((MAX(`%field%`) - MIN(`%field%`)) / 1000), 1) AS %mesure%, ");
    }
    // Suppression de la dernière virgule
    $query = substr($query, 0, -2) . " ";
    // From
    $query .= "FROM " . $db_connect['table'] . " ";
    // Where
    $datetimedebut = date_php($timestampdebut, "TIMESTAMP", $tDate);
    $datetimefin = date_php($timestampfin, "TIMESTAMP", $tDate);
    //$datefield = str_replace ("%field%", $config_table["table"]["DATE"], $date_sql["DATE"][$tDate]);
    $datefield = $config_table["table"]["DATE"];

    $query .= str_replace(
        array("%date%", "%debut%", "%fin%"),
        array($datefield, $datetimedebut, $datetimefin),
        "WHERE %date% BETWEEN '%debut%' and '%fin%' ");
    // Group By
    $query .= "GROUP BY PERIODE ";
    // Order By
    $query .= "ORDER BY " . $config_table["table"]["DATE"];

    // SELECT UNIX_TIMESTAMP(DATE) AS TIMESTAMP, DATE_FORMAT(DATE, '%a %e') AS PERIODE,
    //   OPTARIF AS OPTARIF, PTEC AS PTEC,
    //   ROUND(((MAX(NULLIF(BASE, 0)) - MIN(NULLIF(BASE, 0))) / 1000), 1) AS BASE
    // FROM tbTeleinfo
    // WHERE UNIX_TIMESTAMP(DATE) BETWEEN 1485471600 and 1486854000
    // GROUP BY PERIODE
    // ORDER BY DATE


    // SELECT UNIX_TIMESTAMP(DATE) AS TIMESTAMP, DATE_FORMAT(DATE, '%a %e') AS PERIODE,
    //   OPTARIF AS OPTARIF, PTEC AS PTEC,
    //   ROUND(((MAX(`BBRHPJB`) - MIN(`BBRHPJB`)) / 1000), 1) AS HPJB,
    //   ROUND(((MAX(`BBRHPJW`) - MIN(`BBRHPJW`)) / 1000), 1) AS HPJW,
    //   ROUND(((MAX(`BBRHPJR`) - MIN(`BBRHPJR`)) / 1000), 1) AS HPJR,
    //   ROUND(((MAX(`BBRHCJB`) - MIN(`BBRHCJB`)) / 1000), 1) AS HCJB,
    //   ROUND(((MAX(`BBRHCJW`) - MIN(`BBRHCJW`)) / 1000), 1) AS HCJW,
    //   ROUND(((MAX(`BBRHCJR`) - MIN(`BBRHCJR`)) / 1000), 1) AS HCJR
    // FROM tbTeleTempo
    // WHERE UNIX_TIMESTAMP(DATE) BETWEEN xxxx and yyyy
    // GROUP BY PERIODE
    // ORDER BY DATE
    return $query;
}

?>
