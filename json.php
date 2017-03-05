<?php
setlocale(LC_ALL , "fr_FR" );
date_default_timezone_set("Europe/Paris");
error_reporting(E_ERROR); // E_WARNING

// Adapté du code de Domos, dont il ne doit plus rester grand chose !
// cf . http://vesta.homelinux.net/wiki/teleinfo_papp_jpgraph.html

include_once("config.php");
include_once("queries.php");
include_once("tarifs.php");

function getOPTARIF($full = false) {
    global $teleinfo;
    global $mysqli;

    // full : Identifie le type de résultat attendu
    //   true  :  retourne un tableau avec optarif & isousc
    //   false :  retourne optarif

    $query = queries::queryOPTARIF();

    $result=$mysqli->query($query);
    if (!$result) {
        printf("<b>Erreur</b> dans la requête <b>" . $query . "</b> : "  . $mysqli->error . " !<br>");
        exit();
    }
    $nbenreg = $result->num_rows;
    if ($nbenreg > 0) {
        $row = $result->fetch_array();
        $optarif = $teleinfo["OPTARIF"][$row["OPTARIF"]];
        $isousc = $row["ISOUSC"];
    }
    else {
      $optarif = null;
      $isousc = null;
    }

    $result->free();

    if ($full) {
        return array (
            "OPTARIF" => $optarif,
            "ISOUSC" => $isousc
        );
    } else {
        return $optarif;
    }
}

// Retourne la date du dernier relevé
// Sert pour éviter les zones vides lorsque le relevé ne se fait pas / plus
// Pas utilisé actuellement car ralentit les traitements
// Le gain ne compense pas la perte de temps.
function getMaxDate() {
    $query = queries::queryMaxDate();
    global $mysqli;

    $result=$mysqli->query($query);
    if (!$result) {
        printf("<b>Erreur</b> dans la requête <b>" . $query . "</b> : "  . $mysqli->error . " !<br>");
        exit();
    }
    $nbenreg = $result->num_rows;
    if ($nbenreg > 0) {
        $row = $result->fetch_array();
        $date = $row["DATE"];
    }
    else {
      $date = null;
    }

    $result->free();

    return $date;
}

function Tomorrow($date) {
    //return $date;
    return mktime(0, 0, 0, date("m", $date), date("d", $date)+1, date("Y", $date));
}


/****************************************/
/*    Graph consommation instantanée    */
/****************************************/
function instantly () {
    global $teleinfo;
    global $config;
    global $mysqli;

    $graphConf = $config["graphiques"]["instantly"];

    $date = isset($_GET['date'])?$_GET['date']:null;
    $optarif = isset($_GET['optarif'])?$_GET['optarif']:null;

    $heurecourante = date('H');              // Heure courante.
    $timestampheure = mktime($heurecourante+1,0,0,date("m"),date("d"),date("Y"));  // Timestamp courant à heure fixe (mn et s à 0).

    // Meilleure date entre celle donnée en paramètre et celle calculée
    $date = ($date)?min($date, $timestampheure):$timestampheure;

    $periodesecondes = 24*3600;                            // 24h.
    $timestampfin = $date;
    $timestampdebut2 = $date - $periodesecondes;           // Recule de 24h.
    $timestampdebut = $timestampdebut2 - $periodesecondes; // Recule de 24h.

    $query = queries::queryInstantly();
    $resultInst=$mysqli->query($query);
    if (!$resultInst) {
        printf("<b>Erreur</b> dans la requête <b>" . $query . "</b> : "  . $mysqli->error . " !<br>");
        exit();
    }
    $nbenreg = $resultInst->num_rows;
    if ($nbenreg > 0) {
        $rowInst = $resultInst->fetch_array();
        $optarif = $teleinfo["OPTARIF"][$rowInst["OPTARIF"]];        $optarifStr = $teleinfo["LIBELLES"]["OPTARIF"][$optarif];
        $ptec = $teleinfo["PTEC"][$rowInst["PTEC"]];
        $ptecStr = $teleinfo["LIBELLES"]["PTEC"][$ptec];
        $demain = $rowInst["DEMAIN"];
        $date_deb = $rowInst["TIMESTAMP"];

        // Recalcul de la période
        $timestampfin = $date_deb;
        $timestampdebut2 = $date_deb - $periodesecondes;       // Recule de 24h.
        $timestampdebut = $timestampdebut2 - $periodesecondes; // Recule de 24h.

        $Isousc = floatval(str_replace(",", ".", $rowInst["ISOUSC"]));
        $val["W"] = floatval(str_replace(",", ".", $rowInst["PAPP"]));
        $series["W"] = "Watts";

        $query = queries::queryMaxPeriod ($timestampdebut2, $timestampfin, $optarif);
        $resultMax=$mysqli->query($query);
        if (!$resultMax) {
            printf("<b>Erreur</b> dans la requête <b>" . $query . "</b> : "  . $mysqli->error . " !<br>");
            exit();
        }
        $nbenreg = $resultMax->num_rows;
        $haveI = false;
        if ($nbenreg > 0) {
            $rowMax = $resultMax->fetch_array();
            $max["W"] = max($val["W"], $rowMax["PAPP"]);
            // Intensités IINST1... IINST3
            if ($graphConf["intensity"]) {
                $numPhase = 1;
                while (isset($rowMax["IINST" . $numPhase])) {
                    $val["I" . $numPhase] = floatval(str_replace(",", ".", $rowInst["IINST" . $numPhase]));
                    $max["I" . $numPhase] = max($val["I" . $numPhase], $rowMax["IINST" . $numPhase]);
                    $haveI = $haveI || ($max["I" . $numPhase]!=0);
                    $numPhase++;
                }
            }
            // Différents index
            if ($config["afficheIndex"]) {
                foreach($teleinfo["PERIODES"][$optarif] as $field) {
                    $index[$field] = array (
                        "title" => $teleinfo["LIBELLES"]["PTEC"][$field],
                        "value" => $rowMax[$field],
                    );
                }
            }
        };
        $resultMax->free();
        $resultInst->free();

        $datetext = date("d/m G:i", $date_deb);

        $seuils["W"] = array (
            'min' => 0,
            'max' => ceil($max["W"] / 500) * 500, // Arrondi à 500 "au dessus"
        );
        $bands["W"] = $graphConf["bands"]["W"];

        // Subtitle pour la période courante
        $subtitle = "Option tarifaire : <b>".$optarifStr." (".$Isousc." A)</b><br />";
        $subtitle .= "Période tarifaire : <b>".$ptecStr."</b><br />";
        switch ($optarif) {
            case "BBR":
                $subtitle .= "Prochaine période : <b>".$demain."</b><br />";
                break;
            default :
                break;
        }
        $subtitle .= "Puissance Max : <b>".intval($max["W"])." W</b><br />";

        // Affiche l'intensité ?
        if (($graphConf["intensity"]) && $haveI) {
            // Intensités IINST1... IINST3
            $subtitle .= "Intensité Max : <b>";
            $numPhase = 1;
            while (isset($rowMax["IINST" . $numPhase])) {
                $val["I" . $numPhase] = floatval(str_replace(",", ".", $rowInst["IINST" . $numPhase]));
                $series["I" . $numPhase] = "Ampères";

                $seuils["I" . $numPhase] = array (
                    'min' => 0,
                    'max' => ceil($max["I" . $numPhase] / 5) * 5, // Arrondi à 5 "au dessus"
                );
                $bands["I" . $numPhase] = $graphConf["bands"]["I"];

                $subtitle .= intval($max["I" . $numPhase])." A / ";
                $numPhase++;
            }
            // Supprime les 3 derniers caractères : " / "
            $subtitle = substr($subtitle, 0, -3);

            $subtitle .= "</b><br />";
        }

        // Différents index
        if ($config["afficheIndex"]) {
            foreach($teleinfo["PERIODES"][$optarif] as $field) {
                $subtitle .= "Index " . $field . " : <b>" . $index[$field]["value"]/1000 . " KWh</b><br />"; // Le compteur affiche la valeur / 1000
            }
        }

        $instantly = array(
            'title' => "Consommation du $datetext",
            'subtitle' => $subtitle,
            'optarif' => array($optarif => $optarifStr),
            'index' => $index,
            'ptec' => array($ptec => $ptecStr),
            'demain' => $demain,
            'debut' => $date_deb*1000, // $date_deb_UTC,
            'series' => $series,
            'data'=> $val,
            'seuils' => $seuils,
            'bands' => $bands,
            'refresh_auto' => $graphConf["refreshAuto"],
            'refresh_delay' => $graphConf["refreshDelay"]
        );

        return $instantly;
    } else {
        $resultInst->free();
        return null;
    }
}

/******************************************************************************************/
/*    Graph consommation w des 24 dernières heures + en parrallèle consommation d'Hier    */
/******************************************************************************************/
function daily () {
    global $teleinfo;
    global $config;
    global $mysqli;

    $graphConf = $config["graphiques"]["daily"];

    //$date = isset($_GET['date'])?$_GET['date']:null;
    $date = isset($_GET['date'])?min($_GET['date'],getMaxDate()):getMaxDate();
    $heurecourante = date('H');              // Heure courante.
    $timestampheure = mktime($heurecourante+1,0,0,date("m"),date("d"),date("Y"));  // Timestamp courant à heure fixe (mn et s à 0)

    // Meilleure date entre celle donnée en paramètre, celle calculée et la dernière date en base
    $date = ($date)?min($date, $timestampheure):$timestampheure;

    $periodesecondes = 24*3600;                            // 24h.
    $timestampfin = $date;
    $timestampdebut2 = $date - $periodesecondes;           // Recule d'une période
    $timestampdebut = $timestampdebut2 - $periodesecondes; // Recule d'une période

    if ($config["recalculPuissance"]) {
        $tab_optarif = getOPTARIF(true);
        $optarif = $tab_optarif["OPTARIF"];
    } else {
        $optarif = null;
    }

    $query = queries::queryDaily($timestampdebut, $timestampfin, $optarif);
    $result=$mysqli->query($query);
    if (!$result) {
        printf("<b>Erreur</b> dans la requête <b>" . $query . "</b> : "  . $mysqli->error . " !<br>");
        exit();
    }
    $nbenreg = $result->num_rows;
    if ($nbenreg > 0) {
        $date_deb=0; // date du 1er enregistrement
        $date_fin=time();

        $row = $result->fetch_array();
        $optarif = $teleinfo["OPTARIF"][$row["OPTARIF"]];
        //$optarifStr = $teleinfo["LIBELLES"]["OPTARIF"][$teleinfo["OPTARIF"][$optarif]];
        $optarifStr = $teleinfo["LIBELLES"]["OPTARIF"][$optarif];
        $ptec = $teleinfo["PTEC"][$row["PTEC"]];
        $ptecStr = $teleinfo["LIBELLES"]["PTEC"][$ptec];

        // Initialisation des courbes qui seront affichées
        foreach($teleinfo["PERIODES"][$optarif] as $ptec) {
            $courbe_titre[$ptec]=$teleinfo["LIBELLES"]["PTEC"][$ptec];
            $courbe_color[$ptec]=$teleinfo["COULEURS"][$ptec];
            $courbe_min[$ptec]=5000;
            $courbe_max[$ptec]=0;
            $courbe_mindate[$ptec]=null;
            $courbe_maxdate[$ptec]=null;
            $array[$ptec]=array();
        }

        // Intensités IINST1... IINST3
        if ($graphConf["intensity"]) {
            $numPhase = 1;
            while (isset($row["IINST" . $numPhase])) {
                // Ajout des courbes intensités
                $courbe_titre["I" . $numPhase]="Intensité " . $numPhase;
                $courbe_color["I" . $numPhase]=$teleinfo["COULEURS"]["I" . $numPhase] ? $teleinfo["COULEURS"]["I" . $numPhase] : $teleinfo["COULEURS"]["I"];
                $courbe_min["I" . $numPhase]=5000;
                $courbe_max["I" . $numPhase]=0;
                $courbe_mindate["I" . $numPhase]=null;
                $courbe_maxdate["I" . $numPhase]=null;
                $array["I" . $numPhase] = array();
                $numPhase++;
            }
        }

        // Ajout de la courbe PREC
        $courbe_titre["PREC"]="Période précédente";
        $courbe_color["PREC"]=$teleinfo["COULEURS"]["PREC"];
        $courbe_min["PREC"]=5000;
        $courbe_max["PREC"]=0;
        $courbe_mindate["PTEC"]=null;
        $courbe_maxdate["PTEC"]=null;
        $array["PREC"] = array();

        $navigator = array();

        $result->data_seek(0); // Revient au début (car on a déjà lu un enreg)
        $prevptec = null;
        $prevts = null;
        $previdx = array();
        while ($row = $result->fetch_array()) {
            $ts = intval($row["TIMESTAMP"]);
            $curptec = $teleinfo["PTEC"][$row["PTEC"]];

            if ($ts < $timestampdebut2) {
                // Période précédente
                $ts = ( $ts + $periodesecondes ) * 1000; // Avance d'une période
                $deltats = ($ts - $prevts) / 1000 / 60; // en minutes

                // On utilise la puissance apparente
                $val = floatval(str_replace(",", ".", $row["PAPP"]));

                if ($config["recalculPuissance"]) {
                    // On recalcule la puissance active, basée sur les relevés d'index
                    $curidx = floatval(str_replace(",", ".", $row[$curptec]));
                    $deltaidx = $curidx-$previdx[$curptec];
                    // On utilise la puisse recalculée (sauf pour le premier index, car on n'a pas de "delta")
                    if ($previdx[$curptec]!==null) {
                        $val =  $deltaidx / $deltats * 60;
                    }
                    $previdx[$curptec] = $curidx;
                }

                $array["PREC"][] = array($ts, $val); // php recommande cette syntaxe plutôt que array_push
                if ($courbe_max["PREC"] < $val) {$courbe_max["PREC"] = $val; $courbe_maxdate["PREC"] = $ts;};
                if ($courbe_min["PREC"] > $val) {$courbe_min["PREC"] = $val; $courbe_mindate["PREC"] = $ts;};

                $prevts = $ts;
            }
            else {
                // Période courante
                if ($date_deb==0) {
                    $date_deb = $row["TIMESTAMP"];
                }
                $ts = $ts * 1000;
                $deltats = ($ts - $prevts) / 1000 / 60; // en minutes

                // On utilise la puissance apparente
                $val = floatval(str_replace(",", ".", $row["PAPP"]));

                if ($config["recalculPuissance"]) {
                    // On recalcule la puissance active, basée sur les relevés d'index
                    $curidx = floatval(str_replace(",", ".", $row[$curptec]));
                    $deltaidx = $curidx-$previdx[$curptec];
                    if ($previdx[$curptec]!==null) {
                        $val =  $deltaidx / $deltats * 60;
                    }
                    $previdx[$curptec] = $curidx;
                }

                // Affecte la consommation selon la période tarifaire
                foreach($teleinfo["PERIODES"][$optarif] as $ptec) {

                    if ($curptec == $ptec) {
                        // Période tarifaire courante
                        $array[$ptec][] = array($ts, $val); // php recommande cette syntaxe plutôt que array_push
                    } elseif ($prevptec == $ptec) {
                        // Changement de periode tarifaire
                        $array[$ptec][] = array($ts, $val); // On reporte la valeur pour lisser le graphique
                    } else {
                        // Toutes les autres périodes tarifaires
                        $array[$ptec][] = array($ts, null);
                    }
                }

                // Ajuste les seuils min/max le cas échéant
                if ($courbe_max[$curptec] < $val) {$courbe_max[$curptec] = $val; $courbe_maxdate[$curptec] = $ts;};
                if ($courbe_min[$curptec] > $val) {$courbe_min[$curptec] = $val; $courbe_mindate[$curptec] = $ts;};

                // Highstock permet un navigateur chronologique
                $navigator[] = array($ts, $val);

                // Intensités IINST1... IINST3
                if ($graphConf["intensity"]) {
                    $numPhase = 1;
                    while (isset($row["IINST" . $numPhase])) {
                        $val = floatval(str_replace(",", ".", $row["IINST" . $numPhase]));
                        $array["I" . $numPhase][] = array($ts, $val); // php recommande cette syntaxe plutôt que array_push
                        if ($courbe_max["I" . $numPhase] < $val) {$courbe_max["I" . $numPhase] = $val; $courbe_maxdate["I" . $numPhase] = $ts;};
                        if ($courbe_min["I" . $numPhase] > $val) {$courbe_min["I" . $numPhase] = $val; $courbe_mindate["I" . $numPhase] = $ts;};
                        $numPhase++;
                    }
                }
            }
            $prevts = $ts;
            $prevptec = $curptec;
        }
        $result->free();

        $date_fin = $ts/1000;

        $plotlines_max = max(array_diff_key($courbe_max, array("I1"=>null, "I2"=>null, "I3"=>null, "PREC"=>null)));
        $plotlines_min = min(array_diff_key($courbe_min, array("I1"=>null, "I2"=>null, "I3"=>null, "PREC"=>null)));

        $date_deb_UTC=$date_deb*1000;
        $datetext = date("d/m G:i", $date_deb) . " au " . date("d/m G:i", $date_fin);

        $seuils = array (
            'min' => $plotlines_min,
            'max' => $plotlines_max,
        );

        // Conserve les séries nécessaires
        $series = array_intersect_key($teleinfo["LIBELLES"]["PTEC"], array_flip($teleinfo["PERIODES"][$optarif]));

        $daily = array(
            'title' => "Graph du $datetext",
            'subtitle' => "",
            'optarif' => array($optarif => $optarifStr),
            'ptec' => array($ptec => $ptecStr),
            'debut' => $timestampfin*1000, // $date_deb_UTC,
            'series' => $series,
            'MAX_color' => $teleinfo["COULEURS"]["MAX"],
            'MIN_color' => $teleinfo["COULEURS"]["MIN"],
            'navigator' => $navigator,
            'seuils' => $seuils
        );

        // Ajoute les séries
        foreach(array_keys($array) as $ptec) {
            $daily[$ptec."_name"] = $courbe_titre[$ptec]." [".$courbe_min[$ptec]." ~ ".$courbe_max[$ptec]."]";
            $daily[$ptec."_color"] = $courbe_color[$ptec]; // $teleinfo["COULEURS"][$ptec];
            $daily[$ptec."_data"] = $array[$ptec];
        }

        return $daily;
    } else {
        $result->free();
        return null;
    }
}

/*************************************************************/
/*    Graph cout sur période [8jours|8semaines|8mois|1an]    */
/*************************************************************/
function history() {
    global $teleinfo;
    global $config;
    global $mysqli;

    $graphConf = $config["graphiques"]["history"];

    $duree = isset($_GET['duree'])?$_GET['duree']:8;
    $periode = isset($_GET['periode'])?$_GET['periode']:"jours";
    //$date = isset($_GET['date'])?$_GET['date']:null;
    $date = isset($_GET['date'])?min($_GET['date'],Tomorrow(getMaxDate())):Tomorrow(getMaxDate());

    switch ($periode) {
        case "jours":
            // Calcul de la fin de période courante
            $timestampheure = mktime(0,0,0,date("m"),date("d"),date("Y"));   // Timestamp courant, 0h
            $timestampheure += 24*3600;                                      // Timestamp courant +24h

            // Meilleure date entre celle donnée en paramètre, celle calculée et la dernière date en base
            $date = ($date)?min($date, $timestampheure):$timestampheure;

            // Périodes
            $decalage = '-' . $duree . ' day';                              // Période, en texte
            $timestampfin = $date;                                           // Fin de la période
            $timestampdebut2 = strtotime($decalage, $timestampfin);          // Début de période active
            $timestampdebut = strtotime($decalage, $timestampdebut2);        // Début de période précédente

            $xlabel = $duree . ($duree==1 ? " jour" : " jours");
            $dateformatsql = "%a %e";
            $divabonnement = 365;
            break;
        case "semaines":
            // Calcul de la fin de période courante
            $timestampheure = mktime(0,0,0,date("m"),date("d"),date("Y"));   // Timestamp courant, 0h
            $timestampheure += 24*3600;                                      // Timestamp courant +24h

            // Meilleure date entre celle donnée en paramètre, celle calculée et la dernière date en base
            $date = ($date)?min($date, $timestampheure):$timestampheure;

            // Avance d'un jour tant que celui-ci n'est pas un lundi
            while ( date("w", $date) != 1 ) {
                $date += 24*3600;
            }

            // Périodes
            $decalage = '-' . $duree . ' week';                              // Période, en texte
            $timestampfin = $date;                                           // Fin de la période
            $timestampdebut2 = strtotime($decalage, $timestampfin);          // Début de période active
            $timestampdebut = strtotime($decalage, $timestampdebut2);        // Début de période précédente

            $xlabel = $duree . ($duree==1 ? " semaine" : " semaines");
            $dateformatsql = "sem %v (%x)";
            $divabonnement = 52;
            break;
        case "mois":
            // Calcul de la fin de période courante
            $timestampheure = mktime(0,0,0,date("m"),date("d"),date("Y")); // Timestamp courant, 0h

            // Meilleure date entre celle donnée en paramètre, celle calculée et la dernière date en base
            $date = ($date)?min($date, $timestampheure):$timestampheure;

            // Avance d'un jour tant qu'on n'est pas le premier du mois
            while ( date("d", $date) != 1 ) {
                $date += 24*3600;
            }

            // Périodes
            $decalage = '-' . $duree . ' month';                             // Période, en texte
            $timestampfin = $date;                                           // Fin de la période
            $timestampdebut2 = strtotime($decalage, $timestampfin);          // Début de période active
            $timestampdebut = strtotime($decalage, $timestampdebut2);        // Début de période précédente

            $xlabel = $duree . ($duree==1 ? " mois" : " mois"); // Prévision pour intl
            $dateformatsql = "%b (%Y)";
            if ($duree > 6) $dateformatsql = "%b %Y";
            $divabonnement = 12;
            break;
        case "ans":
            // Calcul de la fin de période courante
            $timestampheure = mktime(0,0,0,date("m"),date("d"),date("Y"));         // Timestamp courant, 0h

            // Meilleure date entre celle donnée en paramètre, celle calculée et la dernière date en base
            $date = ($date)?min($date, $timestampheure):$timestampheure;

            $date = mktime(0,0,0,1,1,date("Y", $date)+1);                          // Année suivante, 0h

            // Périodes
            $decalage = '-' . $duree . ' year';                              // Période, en texte
            $timestampfin = $date;                                           // Fin de la période
            $timestampdebut2 = strtotime($decalage, $timestampfin);          // Début de période active
            $timestampdebut = strtotime($decalage, $timestampdebut2);        // Début de période précédente

            $xlabel = $duree . ($duree==1 ? " an" : " ans");
            $dateformatsql = "%b %Y";
            $divabonnement = 12;
            break;
        default:
            die("Periode erronée, valeurs possibles: [8jours|8semaines|8mois|1an] !");
            break;
    }

    $tab_optarif = getOPTARIF(true);
    $optarif = $tab_optarif["OPTARIF"];
    $isousc = $tab_optarif["ISOUSC"];

    $query = queries::queryHistory($timestampdebut, $timestampfin, $dateformatsql, $optarif);

    $result=$mysqli->query($query);
    if (!$result) {
        printf("<b>Erreur</b> dans la requête <b>" . $query . "</b> : "  . $mysqli->error . " !<br>");
        exit();
    }
    $nbenreg = $result->num_rows;

    if ($nbenreg > 0) {
        $kwhprec = array();
        $kwhprec_detail = array();
        $date_deb=0; // date du 1er enregistrement
        $date_fin=time();

        $row = $result->fetch_array();
        $optarif = $teleinfo["OPTARIF"][$row["OPTARIF"]];
        //$optarifStr = $teleinfo["LIBELLES"]["OPTARIF"][$teleinfo["OPTARIF"][$optarif]];
        $optarifStr = $teleinfo["LIBELLES"]["OPTARIF"][$optarif];
        $ptec = $teleinfo["PTEC"][$row["PTEC"]];
        $ptecStr = $teleinfo["LIBELLES"]["PTEC"][$ptec];

        // On initialise à vide
        // Cas si les périodes sont "nulles", on n'aura pas d'initialisation des tableaux
        foreach($teleinfo["PERIODES"][$optarif] as $ptec) {
            $kwh[$ptec] = array();
            $kwhp[$ptec] = array();
        }
        $categories = array();
        $timestp = array();
        $timestpp = array();

        // Calcul des consommations
        $result->data_seek(0); // Revient au début (car on a déjà lu un enreg)
        while ($row = $result->fetch_array()) {
            $ts = intval($row["TIMESTAMP"]);
            if ($ts < $timestampdebut2) {
                // Période précédente
                $cumul = null; // reset (sinon on cumule à chaque étape de la boucle)
                $timestpp[] = $row["TIMESTAMP"];
                foreach($teleinfo["PERIODES"][$optarif] as $ptec) {
                    // On conserve le détail (qui sera affiché en infobulle)
                    $kwhp[$ptec][] = floatval(isset($row[$ptec]) ? $row[$ptec] : 0);
                    // On calcule le total consommé (qui sera affiché en courbe)
                    $cumul[] = isset($row[$ptec]) ? $row[$ptec] : 0;
                }
                $kwhprec[] = array($row["PERIODE"], array_sum($cumul)); // php recommande cette syntaxe plutôt que array_push
            }
            else {
                // Période courante
                if ($date_deb==0) {
                    $date_deb = $row["TIMESTAMP"];
                    //$date_deb = strtotime($row["REC_DATE"]);
                }
                // Ajout les éléments actuels à chaque tableau
                $categories[] = $row["PERIODE"];
                $timestp[] = $row["TIMESTAMP"];
                foreach($teleinfo["PERIODES"][$optarif] as $ptec) {
                    $kwh[$ptec][] = floatval(isset($row[$ptec]) ? $row[$ptec] : 0);
                }
            }
        }

        // On vérifie la durée de la période actuelle
        if (count($kwh) < $duree) {
            // pad avec une valeur négative, pour ajouter en début de tableau
            $timestp = array_pad ($timestp, -$duree, null);
            $categories = array_pad ($categories, -$duree, null);
            foreach($kwh as &$current) {
                $current = array_pad ($current, -$duree, null);
            }
        }

        // On vérifie la durée de la période précédente
        if (count($kwhprec) < count(reset($kwh))) {
            // pad avec une valeur négative, pour ajouter en début de tableau
            $timestpp = array_pad ($timestpp, -count(reset($kwh)), null);
            $kwhprec = array_pad ($kwhprec, -count(reset($kwh)), null);
            foreach($kwhp as &$current) {
                $current = array_pad ($current, -count(reset($kwh)), null);
            }
        }
        $result->free();

        $date_deb_UTC=$date_deb*1000;
        $datetext = date("d/m G:i", $date_deb);

        // Calcul des coûts
        $tab_prix = getTarifsEDF($optarif, $isousc);

        foreach($teleinfo["PERIODES"][$optarif] as $ptec) {
            $mnt["KWH"][$ptec] = 0;
            $mntp["KWH"][$ptec] = 0;
        }
        $i = 0;
        $rounds = max(count(reset($kwh)), count(reset($kwhp)));
        while ($i < $rounds) {
            // Puriste, on sépare les traitements des périodes courante et précédente.
            // - Si l'option tarifaire évolue (pas encore pris en charge)
            // - Si les taxes évoluent


            // Période courante
            // On recherche la base tarifaire pour cette période (date)
            $cur_prix = getTarifs($tab_prix, $timestp[$i]);

            foreach($cur_prix["TAXES_C"] as $tkey => $tval) {
                $mnt["TAXES"][$tkey][$i] = 0; // Init à zéro
            }
            $mnt["TOTAL"][$i] = 0;
            $conso = false;

            foreach($teleinfo["PERIODES"][$optarif] as $ptec) {
                // TaxesC
                foreach($cur_prix["TAXES_C"] as $tkey => $tval) {
                    $mnt["TAXES"][$tkey][$i] += $kwh[$ptec][$i] * $cur_prix["TAXES_C"][$tkey];
                    $mnt["TOTAL"][$i] += $kwh[$ptec][$i] * $cur_prix["TAXES_C"][$tkey];
                }
                // Consommation
                $mnt["TARIFS"][$ptec][$i] = $kwh[$ptec][$i] * $cur_prix["TARIFS"][$ptec];
                $mnt["TOTAL"][$i] += $kwh[$ptec][$i] * $cur_prix["TARIFS"][$ptec];
                $mnt["KWH"][$ptec] += $kwh[$ptec][$i];
                $conso = ($conso or ($kwh[$ptec][$i]!=0));
            }
            // TaxesA
            foreach($cur_prix["TAXES_A"] as $tkey => $tval) {
                $mnt["TAXES"][$tkey][$i] = $cur_prix["TAXES_A"][$tkey] / $divabonnement;
                $mnt["TOTAL"][$i] += $cur_prix["TAXES_A"][$tkey] / $divabonnement;
            }
            // Abonnement
            $mnt["ABONNEMENTS"][$i] = $conso * $cur_prix["ABONNEMENTS"][$optarif] / $divabonnement;
            $mnt["TOTAL"][$i] += $conso * $cur_prix["ABONNEMENTS"][$optarif] / $divabonnement;

            // Période précédente
            // On recherche la base tarifaire pour la période précédente
            $prec_prix = getTarifs($tab_prix, $timestpp[$i]);

            foreach($prec_prix["TAXES_C"] as $tkey => $tval) {
                $mntp["TAXES"][$tkey][$i] = 0; // Init à zéro
            }
            $mntp["TOTAL"][$i] = 0;
            $conso = false;

            foreach($teleinfo["PERIODES"][$optarif] as $ptec) {
                // TaxesC
                foreach($prec_prix["TAXES_C"] as $tkey => $tval) {
                    $mntp["TAXES"][$tkey][$i] += $kwhp[$ptec][$i] * $prec_prix["TAXES_C"][$tkey];
                    $mntp["TOTAL"][$i] += $kwhp[$ptec][$i] * $prec_prix["TAXES_C"][$tkey];
                }
                // Consommation
                $mntp["TARIFS"][$ptec][$i] = $kwhp[$ptec][$i] * $prec_prix["TARIFS"][$ptec];
                $mntp["TOTAL"][$i] += $kwhp[$ptec][$i] * $prec_prix["TARIFS"][$ptec];
                $mntp["KWH"][$ptec] += $kwhp[$ptec][$i];
                $conso = ($conso or ($kwhp[$ptec][$i]!=0));
            }
            // TaxesA
            foreach($prec_prix["TAXES_A"] as $tkey => $tval) {
                $mntp["TAXES"][$tkey][$i] = $prec_prix["TAXES_A"][$tkey] / $divabonnement;
                $mntp["TOTAL"][$i] += $prec_prix["TAXES_A"][$tkey] / $divabonnement;
            }
            // Abonnement
            $mntp["ABONNEMENTS"][$i] = $conso * $prec_prix["ABONNEMENTS"][$optarif] / $divabonnement;
            $mntp["TOTAL"][$i] += $conso * $prec_prix["ABONNEMENTS"][$optarif] / $divabonnement;

            $i++;
        }

        // Totaux période courante
        foreach($mnt["TAXES"] as $tkey => $tval) {
            $total_mnt["TAXES"][$tkey] = array_sum($mnt["TAXES"][$tkey]);
        }
        foreach($mnt["TARIFS"] as $tkey => $tval) {
            $total_mnt["TARIFS"][$tkey] = array_sum($mnt["TARIFS"][$tkey]);
        }
        $total_mnt["ABONNEMENTS"] = array_sum($mnt["ABONNEMENTS"]);
        $total_mnt["TOTAL"] = array_sum($mnt["TOTAL"]);
        $total_mnt["KWH"] = array_sum($mnt["KWH"]);

        // Totaux période précédente
        foreach($mntp["TAXES"] as $tkey => $tval) {
            $total_mntp["TAXES"][$tkey] = array_sum($mntp["TAXES"][$tkey]);
        }
        foreach($mntp["TARIFS"] as $tkey => $tval) {
            $total_mntp["TARIFS"][$tkey] = array_sum($mntp["TARIFS"][$tkey]);
        }
        $total_mntp["ABONNEMENTS"] = array_sum($mntp["ABONNEMENTS"]);
        $total_mntp["TOTAL"] = array_sum($mntp["TOTAL"]);
        $total_mntp["KWH"] = array_sum($mntp["KWH"]);

        // Subtitle pour la période courante
        $subtitle = "";
        if ($total_mnt["TOTAL"] != 0) { // A priori, toujours vrai !
            $subtitle = $subtitle."<b>Coût sur la période</b> ".round($total_mnt["TOTAL"],2)." Euro (".$total_mnt["KWH"]." KWh)<br />";
            $subtitle = $subtitle."(Abonnement : ".round($total_mnt["ABONNEMENTS"],2);
            $subtitle = $subtitle." + Taxes (" . implode(", ", array_keys($total_mnt["TAXES"])) . ") : ".round(array_sum($total_mnt["TAXES"]),2);
            foreach($total_mnt["TARIFS"] as $ptec => $val) {
                if ($total_mnt["TARIFS"][$ptec] != 0) {
                    $subtitle = $subtitle." + ".$ptec." : ".round($total_mnt["TARIFS"][$ptec],2);
                }
            }
            $subtitle = $subtitle.")";
            if ((count($total_mnt["TARIFS"]) > 1) && ($total_mnt["KWH"] > 0)) {
                $subtitle = $subtitle."<br /><b>Total KWh</b> ";
                $prefix = "";
                foreach($mnt["KWH"] as $ptec => $val) {
                    if ($mnt["KWH"][$ptec] != 0) {
                        $subtitle = $subtitle.$prefix.$ptec." : ".$mnt["KWH"][$ptec];
                        if ($prefix=="") {
                            $prefix = " + ";
                        }
                    }
                }
            }
        }

        // Subtitle pour la période précédente
        if ($total_mntp["TOTAL"] != 0) {
            $subtitle = $subtitle."<br /><b>Coût sur la période précédente</b> ".round($total_mntp["TOTAL"],2)." Euro (".$total_mntp["KWH"]." KWh)<br />";
            $subtitle = $subtitle."(Abonnement : ".round($total_mntp["ABONNEMENTS"],2);
            $subtitle = $subtitle." + Taxes (" . implode(", ", array_keys($total_mntp["TAXES"])) . ") : ".round(array_sum($total_mntp["TAXES"]),2);
            foreach($total_mntp["TARIFS"] as $ptec => $val) {
                if ($total_mntp["TARIFS"][$ptec] != 0) {
                    $subtitle = $subtitle." + ".$ptec." : ".round($total_mntp["TARIFS"][$ptec],2);
                }
            }
            $subtitle = $subtitle.")";
            if ((count($total_mntp["TARIFS"]) > 1) && ($total_mntp["KWH"] > 0)) {
                $subtitle = $subtitle."<br /><b>Total KWh</b> ";
                $prefix = "";
                foreach($mntp["KWH"] as $ptec => $val) {
                    if ($mntp["KWH"][$ptec] != 0) {
                        $subtitle = $subtitle.$prefix.$ptec." : ".$mntp["KWH"][$ptec];
                        if ($prefix=="") {
                            $prefix = " + ";
                        }
                    }
                }
            }
        }

        // Conserve les séries nécessaires
        $series = array_intersect_key($teleinfo["LIBELLES"]["PTEC"], array_flip($teleinfo["PERIODES"][$optarif]));

        $history = array(
            'show3D' => $graphConf["show3D"],
            'title' => "Consommation sur $xlabel",
            'subtitle' => $subtitle,
            'optarif' => array($optarif => $optarifStr),
            'ptec' => array($ptec => $ptecStr),
            'duree' => $duree,
            'periode' => $periode,
            'debut' => $timestampfin*1000,
            'series' => $series,
            'prix' => $mnt,
            'prix_tot' => $total_mnt,
            'PREC_prix' => $mntp,
            'PREC_prix_tot' => $total_mntp,
            'categories' => $categories,
            'PREC_color' => $teleinfo["COULEURS"]["PREC"],
            'PREC_name' => 'Période Précédente',
            'PREC_data' => $kwhprec,
            //'PREC_data_detail' => $kwhp,
            'PREC_type' => $graphConf["typePrec"]
        );

        // Ajoute les séries
        foreach($teleinfo["PERIODES"][$optarif] as $ptec) {
            $history[$ptec."_name"] = $series[$ptec];
            $history[$ptec."_color"] = $teleinfo["COULEURS"][$ptec];
            $history[$ptec."_data"] = $kwh[$ptec];
            $history[$ptec."_type"] = $graphConf["typeSerie"];
            $history["PREC_detail"][$ptec."_data"] = $kwhp[$ptec];
            //'PREC_data_detail' => $kwhp,
        }

        return $history;
    } else {
        $result->free();
        return null;
    }
}

function main() {
    global $db_connect;
    global $mysqli;

    $query = isset($_GET['query'])?$_GET['query']:"daily";

    if (isset($query)) {
        $mysqli = new mysqli($db_connect['serveur'], $db_connect['login'], $db_connect['pass'], $db_connect['base']);
        if (mysqli_connect_errno()) {
            printf("Erreur de connexion au serveur MySql : %s\n", mysqli_connect_error());
            exit();
        }
        if (!$mysqli->set_charset("utf8")) {
            printf("Erreur lors du chargement du jeu de caractères utf8 : %s\n", $mysqli->error);
            exit();
        }
        $mysqli->query("SET lc_time_names = 'fr_FR'");  // Pour afficher date en français dans MySql.

        switch ($query) {
            case "instantly":
                $data=instantly();
                break;
            case "daily":
                $data=daily();
                break;
            case "history":
                $data=history();
                break;
            default:
                break;
        };
        $mysqli->close();

        if ($data !== null) {
            echo json_encode($data);
        }
    }
}

main();
?>
