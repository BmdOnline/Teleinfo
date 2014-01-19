<?php


// Retourne l'option souscrite pour la période choisie
// En cas de modification d'abonnement EDF durant la période,
//   on peut avoir plusieurs options.
// Pour l'instant, ce n'est pas géré par le programme :
//   on gère alors l'option courante.
function queryOpTarif() {
    global $db_teleinfo;
    global $db_connect;

    $db_date = $db_teleinfo['date'];
    $db_optarif = $db_teleinfo['optarif'];

    $query = "SELECT $db_optarif as optarif
        FROM `%table%`
        WHERE $db_date=(select max($db_date) FROM `%table%`)";

    $query = str_replace ("%table%", $db_connect['table'], $query);
    return $query;
}

function getOpTarif() {
    $query = queryOpTarif();
    $result = mysql_query($query) or die ("<b>Erreur</b> dans la requète <b>" . $query . "</b> : "  . mysql_error() . " !<br>");
    $nbenreg = mysql_num_rows($result);
    if ($nbenreg > 0) {
        $row = mysql_fetch_array($result);
        $optarif = $row["optarif"];
    }
    else
      $optarif = null;

    mysql_free_result($result);

    return $optarif;
}

function queryInstantly () {
    global $db_teleinfo, $db_select_date, $db_select_mesures;
    global $db_connect;

    $db_date = $db_teleinfo['date'];
    $db_optarif = $db_teleinfo['optarif'];
    $db_iinst = $db_teleinfo['iinst'];
    $db_demain = $db_teleinfo['demain'];

    $query = "SELECT $db_select_date[$db_date], $db_optarif as optarif, $db_select_mesures[$db_iinst], $db_demain as demain
        FROM `%table%`
        WHERE $db_date=(select max($db_date) FROM `%table%`)";

    $query = str_replace ("%table%", $db_connect['table'], $query);
    return $query;
}

function queryDaily ($timestampdebut, $timestampfin) {
    global $db_teleinfo, $db_timestamp, $db_select_date, $db_select_mesures;
    global $db_connect;

    $db_date = $db_teleinfo['date'];
    $db_ptec = $db_teleinfo['ptec'];
    $db_iinst = $db_teleinfo['iinst'];

    $query = "SELECT $db_select_date[$db_date], $db_ptec as ptec, $db_select_mesures[$db_iinst]
        FROM `%table%`
        WHERE $db_timestamp[$db_date] BETWEEN $timestampdebut and $timestampfin
        ORDER BY $db_date";

    $query = str_replace ("%table%", $db_connect['table'], $query);
    return $query;
}

function queryHistory ($timestampdebut, $dateformatsql, $timestampfin) {
    global $db_teleinfo, $db_timestamp, $db_rec_date, $db_select_date;
    global $db_connect;

    $db_date = $db_teleinfo['date'];
    $db_optarif = $db_teleinfo['optarif'];
    $db_iinst = $db_teleinfo['iinst'];

    $optarif = getOpTarif();

    // Selon l'option tarifaire choisie, on requête des champs différents
    // Attention à la casse : les noms retournés doivent être en majuscules
    switch ($optarif) {
        case "BASE" :
            $select_hist =
                "ROUND(((MAX(`base`) - MIN(`base`)) / 1000), 1) AS BASE";
            break;

        case "HC.." :
            $select_hist =
                "ROUND(((MAX(`hchp`) - MIN(`hchp`)) / 1000), 1) AS HP,
                 ROUND(((MAX(`hchc`) - MIN(`hchc`)) / 1000), 1) AS HC";
            break;

        //case "BBRX" : // A priori, la trame téléinfo renvoie BBR.
        case "BBR" :
            $select_hist =
                "ROUND(((MAX(`bbrhpjb`) - MIN(`bbrhpjb`)) / 1000), 1) AS HPJB,
                 ROUND(((MAX(`bbrhcjb`) - MIN(`bbrhcjb`)) / 1000), 1) AS HCJB,
                 ROUND(((MAX(`bbrhpjw`) - MIN(`bbrhpjw`)) / 1000), 1) AS HPJW,
                 ROUND(((MAX(`bbrhcjw`) - MIN(`bbrhcjw`)) / 1000), 1) AS HCJW,
                 ROUND(((MAX(`bbrhpjr`) - MIN(`bbrhpjr`)) / 1000), 1) AS HPJR,
                 ROUND(((MAX(`bbrhcjr`) - MIN(`bbrhcjr`)) / 1000), 1) AS HCJR";
            break;

        case "EJP." :
            $select_hist = "";
                "ROUND(((MAX(`ejphn`) - MIN(`ejphn`)) / 1000), 1) AS HN,
                 ROUND(((MAX(`ejphpm`) - MIN(`ejphpm`)) / 1000), 1) AS HPM";
            break;
        }
      /*$select_hist =
        "ROUND(((MAX(`base`) - MIN(`base`)) / 1000), 1) AS BASE,
         ROUND(((MAX(`hchp`) - MIN(`hchp`)) / 1000), 1) AS HP,
         ROUND(((MAX(`hchc`) - MIN(`hchc`)) / 1000), 1) AS HC,
         ROUND(((MAX(`bbrhpjb`) - MIN(`bbrhpjb`)) / 1000), 1) AS HPJB,
         ROUND(((MAX(`bbrhcjb`) - MIN(`bbrhcjb`)) / 1000), 1) AS HCJB,
         ROUND(((MAX(`bbrhpjw`) - MIN(`bbrhpjw`)) / 1000), 1) AS HPJW,
         ROUND(((MAX(`bbrhcjw`) - MIN(`bbrhcjw`)) / 1000), 1) AS HCJW,
         ROUND(((MAX(`bbrhpjr`) - MIN(`bbrhpjr`)) / 1000), 1) AS HPJR,
         ROUND(((MAX(`bbrhcjr`) - MIN(`bbrhcjr`)) / 1000), 1) AS HCJR,
         ROUND(((MAX(`ejphn`) - MIN(`ejphn`)) / 1000), 1) AS HN,
         ROUND(((MAX(`ejphpm`) - MIN(`ejphpm`)) / 1000), 1) AS HPM"; /* */

    $query = "SELECT $db_select_date[$db_date], DATE_FORMAT($db_rec_date[$db_date], '$dateformatsql') AS 'periode', $db_optarif as optarif,
        $select_hist
        FROM `%table%`
        WHERE $db_timestamp[$db_date] BETWEEN $timestampdebut and $timestampfin
        GROUP BY periode
        ORDER BY rec_date";

    $query = str_replace ("%table%", $db_connect['table'], $query);
    return $query;
}

/* Actuellement, ne sert pas */
function queryMaxPeriod ($timestampdebut, $timestampfin) {
    global $db_teleinfo, $db_timestamp, $db_select_max_mesures;
    global $db_connect;

    $db_date = $db_teleinfo['date'];
    $db_iinst = $db_teleinfo['iinst'];

    $query = "SELECT $db_select_max_mesures[$db_iinst]
        FROM `%table%`
        WHERE $db_timestamp[$db_date] BETWEEN $timestampdebut and $timestampfin
        ORDER BY $db_date";

    $query = str_replace ("%table%", $db_connect['table'], $query);
    return $query;
}

?>
