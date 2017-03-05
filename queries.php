<?php
class queries
{
    // Conversion timestamp vs date
    private static function dateConvSQL ($dateIn, $typeIn, $typeOut) {
        if (($dateIn == '') || ($typeOut == '') || ($typeIn == $typeOut)) {
            $dateOut = $dateIn;
        } else {
            switch ($typeOut) {
                case "DATE":
                    $dateOut = "DATE(FROM_UNIXTIME(" . $dateIn . "))";
                    break;
                case "TIMESTAMP":
                    $dateOut = "UNIX_TIMESTAMP(" . $dateIn . ")";
                    break;
                default :
                    $dateOut = $dateIn;
                    break;
            }
        }
        return $dateOut;
    }

    // Conversion timestamp vs date
    private static function dateConv ($dateIn, $typeIn, $typeOut) {
        if (($dateIn == '') || ($typeOut == '') || ($typeIn == $typeOut)) {
            $dateOut = $dateIn;
        } else {
            switch ($typeIn) {
                case "DATE":
                    try {
                        $datetime = new DateTime($dateIn);
                        $datetime->setTimezone(new DateTimeZone('Europe/Paris'));
                    } catch (Exception $e) {
                        $datetime = $dateIn;
                        $typeOut = '';
                    }
                    break;
                case "TIMESTAMP":
                    try {
                        $datetime = new DateTime("@$dateIn");
                        $datetime->setTimezone(new DateTimeZone('Europe/Paris'));
                    } catch (Exception $e) {
                        $datetime = $dateIn;
                        $typeOut = '';
                    }
                    break;
                default :
                    $datetime = $dateIn;
                    break;
            }
            switch ($typeOut) {
                case "DATE":
                    $dateOut = $datetime->format("Y-m-d H:i:s");
                    break;
                case "TIMESTAMP":
                    $dateOut = $datetime->format("U");
                    break;
                default :
                    $dateOut = $datetime;
                    break;
            }
        }
        return $dateOut;
    }

    // Retourne l'option souscrite pour la période choisie
    // En cas de modification d'abonnement EDF durant la période,
    //   on peut avoir plusieurs options.
    // Pour l'instant, ce n'est pas géré par le programme :
    //   on gère alors l'option courante.
    public static function queryOPTARIF () {
        global $db_connect, $config_table;

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
    public static function queryMaxPeriod ($timestampdebut, $timestampfin, $optarif = null) {
        global $db_connect, $config_table;
        global $teleinfo;

        $mesures = array ("PAPP");
        // Intensités IINST1... IINST3
        $numPhase = 1;
        while (isset($config_table["table"]["IINST" . $numPhase])) {
            $mesures[] = "IINST" . $numPhase;
            $numPhase++;
        }

        $tDate = strtoupper($config_table["type_date"]);
        $timestamp = self::dateConvSQL($config_table["table"]["DATE"], $tDate, "TIMESTAMP");
        $date = self::dateConvSQL($config_table["table"]["DATE"], $tDate, "DATE");

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
        $datetimedebut = self::dateConv($timestampdebut, "TIMESTAMP", $tDate);
        $datetimefin = self::dateConv($timestampfin, "TIMESTAMP", $tDate);
        //$datefield = self::dateConvSQL($config_table["table"]["DATE"], $tDate, "DATE");
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

    public static function queryMaxDate () {
        global $db_connect, $config_table;

        $mesures = array ("PAPP");
        // Intensités IINST1... IINST3
        $numPhase = 1;
        while (isset($config_table["table"]["IINST" . $numPhase])) {
            $mesures[] = "IINST" . $numPhase;
            $numPhase++;
        }

        $tDate = strtoupper($config_table["type_date"]);
        $timestamp = self::dateConvSQL($config_table["table"]["DATE"], $tDate, "TIMESTAMP");

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

    public static function queryInstantly () {
        global $db_connect, $config_table;

        $mesures = array ("PAPP");
        // Intensités IINST1... IINST3
        $numPhase = 1;
        while (isset($config_table["table"]["IINST" . $numPhase])) {
            $mesures[] = "IINST" . $numPhase;
            $numPhase++;
        }
        $mesures = array_merge($mesures, array("ISOUSC", "OPTARIF", "PTEC", "DEMAIN"));

        $tDate = strtoupper($config_table["type_date"]);
        $timestamp = self::dateConvSQL($config_table["table"]["DATE"], $tDate, "TIMESTAMP");

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

    public static function queryDaily ($timestampdebut, $timestampfin, $optarif = null) {
        global $db_connect, $config_table;
        global $teleinfo;

        $mesures = array ("PAPP");
        // Intensités IINST1... IINST3
        $numPhase = 1;
        while (isset($config_table["table"]["IINST" . $numPhase])) {
            $mesures[] = "IINST" . $numPhase;
            $numPhase++;
        }
        $mesures = array_merge($mesures, array("OPTARIF", "PTEC"));

        $tDate = strtoupper($config_table["type_date"]);
        $timestamp = self::dateConvSQL($config_table["table"]["DATE"], $tDate, "TIMESTAMP");

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
        $datetimedebut = self::dateConv($timestampdebut, "TIMESTAMP", $tDate);
        $datetimefin = self::dateConv($timestampfin, "TIMESTAMP", $tDate);
        //$datefield = self::dateConvSQL($config_table["table"]["DATE"], $tDate, "DATE");
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

    public static function queryHistory ($timestampdebut, $timestampfin, $dateformatsql, $optarif) {
        global $db_connect, $config_table;
        global $teleinfo;

        $mesures = array ("OPTARIF", "PTEC");

        $tDate = strtoupper($config_table["type_date"]);
        $timestamp = self::dateConvSQL($config_table["table"]["DATE"], $tDate, "TIMESTAMP");
        $date = self::dateConvSQL($config_table["table"]["DATE"], $tDate, "DATE");

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
        $datetimedebut = self::dateConv($timestampdebut, "TIMESTAMP", $tDate);
        $datetimefin = self::dateConv($timestampfin, "TIMESTAMP", $tDate);
        //$datefield = self::dateConvSQL($config_table["table"]["DATE"], $tDate, "DATE");
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
}

?>
