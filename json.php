<?php
setlocale(LC_ALL , "fr_FR" );
date_default_timezone_set("Europe/Paris");
error_reporting(0);

// Adapté du code de Domos, dont il ne doit plus rester grand chose !
// cf . http://vesta.homelinux.net/wiki/teleinfo_papp_jpgraph.html

include_once("config.php");
include_once("queries.php");
include_once("tarifs.php");

function getOPTARIF($full = false) {
    global $teleinfo;
    // full : Identifie le type de résultat attendu
    //   true  :  retourne un tableau avec optarif & isousc
    //   false :  retourne optarif

    $query = queryOPTARIF();

    $result = mysql_query($query) or die ("<b>Erreur</b> dans la requète <b>" . $query . "</b> : "  . mysql_error() . " !<br>");
    $nbenreg = mysql_num_rows($result);
    if ($nbenreg > 0) {
        $row = mysql_fetch_array($result);
        $optarif = $teleinfo["OPTARIF"][$row["OPTARIF"]];
        $isousc = $row["ISOUSC"];
    }
    else {
      $optarif = null;
      $isousc = null;
    }

    mysql_free_result($result);

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
    $query = queryMaxDate();

    $result = mysql_query($query) or die ("<b>Erreur</b> dans la requète <b>" . $query . "</b> : "  . mysql_error() . " !<br>");
    $nbenreg = mysql_num_rows($result);
    if ($nbenreg > 0) {
        $row = mysql_fetch_array($result);
        $date = $row["DATE"];
    }
    else {
      $date = null;
    }

    mysql_free_result($result);

    return $date;
}

/****************************************/
/*    Graph consommation instantanée    */
/****************************************/
function instantly () {
    global $config;

    $date = isset($_GET['date'])?$_GET['date']:null;

    $heurecourante = date('H') ;              // Heure courante.
    $timestampheure = mktime($heurecourante+1,0,0,date("m"),date("d"),date("Y"));  // Timestamp courant à heure fixe (mn et s à 0).

    // Meilleure date entre celle donnée en paramètre et celle calculée
    $date = ($date)?min($date, $timestampheure):$timestampheure;

    $periodesecondes = 24*3600 ;                            // 24h.
    $timestampfin = $date;
    $timestampdebut2 = $date - $periodesecondes ;           // Recule de 24h.
    $timestampdebut = $timestampdebut2 - $periodesecondes ; // Recule de 24h.

    $query = queryInstantly();

    $result=mysql_query($query) or die ("<b>Erreur</b> dans la requète <b>" . $query . "</b> : "  . mysql_error() . " !<br>");

    $nbenreg = mysql_num_rows($result);
    if ($nbenreg > 0) {
        $row = mysql_fetch_array($result);
        $optarif = $row["OPTARIF"];
        $demain = $row["DEMAIN"];
        $date_deb = $row["TIMESTAMP"];
        $valP = floatval(str_replace(",", ".", $row["PAPP"]));
        $valI = floatval(str_replace(",", ".", $row["IINST1"]));
    };
    mysql_free_result($result);

    $query = queryMaxPeriod ($timestampdebut2, $timestampfin);

    $result=mysql_query($query) or die ("<b>Erreur</b> dans la requète <b>" . $query . "</b> : "  . mysql_error() . " !<br>");
    $nbenreg = mysql_num_rows($result);
    if ($nbenreg > 0) {
        $row = mysql_fetch_array($result);
        $maxP = max($valP, $row["PAPP"]);
        $maxI = max($valI, $row["IINST1"]);
    };
    mysql_free_result($result);

    $datetext = strftime("%c",$date_deb);

    $seuilsP = array (
        'min' => 0,
        'max' => ceil($maxP / 500) * 500, // Arrondi à 500 "au dessus"
    );

    // Subtitle pour la période courante
    switch ($optarif) {
        case "BBR":
            $subtitle = "Prochaine période : <b>".$demain."</b>";
            break;
        default :
            $subtitle = "";
            break;
    }

    $instantly = array(
        'title' => "Consommation du $datetext",
        'subtitle' => $subtitle,
        'debut' => $date_deb*1000, // $date_deb_UTC,
        'W_name' => "Watts",
        'W_data'=> $valP,
        'W_seuils' => $seuilsP,
        'optarif' => $optarif,
        'demain' => $demain,
        'refresh_auto' => $config["refreshAuto"],
        'refresh_delay' => $config["refreshDelay"]
    );

    return $instantly;
}

/****************************************************************************************/
/*    Graph consomation w des 24 dernières heures + en parrallèle consomation d'Hier    */
/****************************************************************************************/
function daily () {
    global $teleinfo;

    $optarif = getOPTARIF();

    $date = isset($_GET['date'])?$_GET['date']:null;

    $heurecourante = date('H') ;              // Heure courante.
    $timestampheure = mktime($heurecourante+1,0,0,date("m"),date("d"),date("Y"));  // Timestamp courant à heure fixe (mn et s à 0).

    // Meilleure date entre celle donnée en paramètre, celle calculée et la dernière date en base
    $date = ($date)?min($date, $timestampheure):$timestampheure;
    //$date = min($date, getMaxDate());

    $periodesecondes = 24*3600 ;                            // 24h.
    $timestampfin = $date;
    $timestampdebut2 = $date - $periodesecondes ;           // Recule de 24h.
    $timestampdebut = $timestampdebut2 - $periodesecondes ; // Recule de 24h.

    $query = queryDaily($timestampdebut, $timestampfin);

    $result=mysql_query($query) or die ("<b>Erreur</b> dans la requète <b>" . $query . "</b> : "  . mysql_error() . " !<br>");

    $nbdata=0;
    $nbenreg = mysql_num_rows($result);
    $nbenreg--;
    $date_deb=0; // date du 1er enregistrement
    $date_fin=time();

    // Initialisation des courbes qui seront affichées
    foreach($teleinfo["PERIODES"][$optarif] as $ptec){
        $courbe_titre[$ptec]=$teleinfo["LIBELLES"]["PTEC"][$ptec];
        $courbe_min[$ptec]=5000;
        $courbe_max[$ptec]=0;
        $courbe_mindate[$ptec]=null;
        $courbe_maxdate[$ptec]=null;
        $array[$ptec]=array();
    }

    // Ajout des courbes intensité et PREC
    $courbe_titre["I"]="Intensité";
    $courbe_min["I"]=5000;
    $courbe_max["I"]=0;
    $courbe_mindate["I"]=null;
    $courbe_maxdate["I"]=null;
    $array["I"] = array();
    $courbe_titre["PREC"]="Période précédente";
    $courbe_min["PREC"]=5000;
    $courbe_max["PREC"]=0;
    $courbe_mindate["PTEC"]=null;
    $courbe_maxdate["PTEC"]=null;
    $array["PREC"] = array();

    $navigator = array();

    $row = mysql_fetch_array($result);
    $ts = intval($row["TIMESTAMP"]);

    // Période précédente
    while (($ts < $timestampdebut2) && ($nbenreg>0) ){
        $ts = ( $ts + 24*3600 ) * 1000;
        $val = floatval(str_replace(",", ".", $row["PAPP"]));
        $array["PREC"][] = array($ts, $val); // php recommande cette syntaxe plutôt que array_push
        if ($courbe_max["PREC"] < $val) {$courbe_max["PREC"] = $val; $courbe_maxdate["PREC"] = $ts;};
        if ($courbe_min["PREC"] > $val) {$courbe_min["PREC"] = $val; $courbe_mindate["PREC"] = $ts;};
        $row = mysql_fetch_array($result);
        $ts = intval($row["TIMESTAMP"]);
        $nbenreg--;
    }

    // Période courante
    while ($nbenreg > 0 ){
        if ($date_deb==0) {
            $date_deb = $row["TIMESTAMP"];
        }
        $ts = intval($row["TIMESTAMP"]) * 1000;

        $val = floatval(str_replace(",", ".", $row["PAPP"]));

        $curptec = $teleinfo["PTEC"][$row["PTEC"]];
        // Affecte la consommation selon la période tarifaire
        foreach($teleinfo["PERIODES"][$optarif] as $ptec){
            if ($curptec == $ptec) {
                $array[$ptec][] = array($ts, $val); // php recommande cette syntaxe plutôt que array_push
            } else {
                $array[$ptec][] = array($ts, null);
            }
        }

        // Ajuste les seuils min/max le cas échéant
        if ($courbe_max[$curptec] < $val) {$courbe_max[$curptec] = $val; $courbe_maxdate[$curptec] = $ts;};
        if ($courbe_min[$curptec] > $val) {$courbe_min[$curptec] = $val; $courbe_mindate[$curptec] = $ts;};

        // Highstock permet un navigateur chronologique
        $navigator[] = array($ts, $val);

        // Intensité
        $val = floatval(str_replace(",", ".", $row["IINST1"])) ;
        $array["I"][] = array($ts, $val); // php recommande cette syntaxe plutôt que array_push
        if ($courbe_max["I"] < $val) {$courbe_max["I"] = $val; $courbe_maxdate["I"] = $ts;};
        if ($courbe_min["I"] > $val) {$courbe_min["I"] = $val; $courbe_mindate["I"] = $ts;};

        // récupérer prochaine occurence de la table
        $row = mysql_fetch_array($result);
        $nbenreg--;
        $nbdata++;
    }
    mysql_free_result($result);

    $date_fin = $ts/1000;

    $plotlines_max = max(array_diff_key($courbe_max, array("I"=>null, "PREC"=>null)));
    $plotlines_min = min(array_diff_key($courbe_min, array("I"=>null, "PREC"=>null)));

    $ddannee = date("Y",$date_deb);
    $ddmois = date("m",$date_deb);
    $ddjour = date("d",$date_deb);
    $ddheure = date("G",$date_deb); //Heure, au format 24h, sans les zéros initiaux
    $ddminute = date("i",$date_deb);

    $ddannee_fin = date("Y",$date_fin);
    $ddmois_fin = date("m",$date_fin);
    $ddjour_fin = date("d",$date_fin);
    $ddheure_fin = date("G",$date_fin); //Heure, au format 24h, sans les zéros initiaux
    $ddminute_fin = date("i",$date_fin);

    $date_deb_UTC=$date_deb*1000;

    $datetext = "$ddjour/$ddmois  $ddheure:$ddminute au $ddjour_fin/$ddmois_fin  $ddheure_fin:$ddminute_fin";

    $seuils = array (
        'min' => $plotlines_min,
        'max' => $plotlines_max,
    );

    // Conserve les séries nécessaires
    $series = array_intersect_key($teleinfo["LIBELLES"]["PTEC"], array_flip($teleinfo["PERIODES"][$optarif]));

    $daily = array(
        'title' => "Graph du $datetext",
        'subtitle' => "",
        'debut' => $timestampfin*1000, // $date_deb_UTC,
        'series' => $series,
        'MAX_color' => $teleinfo["COULEURS"]["MAX"],
        'MIN_color' => $teleinfo["COULEURS"]["MIN"],
        'navigator' => $navigator,
        'seuils' => $seuils,
        'optarif' => $optarif
    );

    // Ajoute les séries
    foreach(array_keys($array) as $ptec) {
        $daily[$ptec."_name"] = $courbe_titre[$ptec]." [".$courbe_min[$ptec]." ~ ".$courbe_max[$ptec]."]";
        $daily[$ptec."_color"] = $teleinfo["COULEURS"][$ptec];
        $daily[$ptec."_data"] = $array[$ptec];
    }

    return $daily;
}

/*************************************************************/
/*    Graph cout sur période [8jours|8semaines|8mois|1an]    */
/*************************************************************/
function history() {
    global $teleinfo;

    $tab_optarif = getOPTARIF(true);
    $optarif = $tab_optarif["OPTARIF"];
    $isousc = $tab_optarif["ISOUSC"];

    $duree = isset($_GET['duree'])?$_GET['duree']:8;
    $periode = isset($_GET['periode'])?$_GET['periode']:"jours";
    $date = isset($_GET['date'])?$_GET['date']:null;

    switch ($periode) {
        case "jours":
            // Calcul de la fin de période courante
            $timestampheure = mktime(0,0,0,date("m"),date("d"),date("Y"));   // Timestamp courant, 0h
            $timestampheure += 24*3600;                                      // Timestamp courant +24h

            // Meilleure date entre celle donnée en paramètre, celle calculée et la dernière date en base
            $date = ($date)?min($date, $timestampheure):$timestampheure;
            //$date = min($date, getMaxDate()); // Attention, il faudra prendre maxDate à 0h

            // Périodes
            $periodesecondes = $duree*24*3600;                               // Periode en secondes
            $timestampfin = $date;                                           // Fin de la période
            $timestampdebut2 = $timestampfin - $periodesecondes;             // Début de période active
            $timestampdebut = $timestampdebut2 - $periodesecondes;           // Début de période précédente

            $xlabel = $duree  . " jours";
            $dateformatsql = "%a %e";
            $divabonnement = 365;
            break;
        case "semaines":
            // Calcul de la fin de période courante
            $timestampheure = mktime(0,0,0,date("m"),date("d"),date("Y"));   // Timestamp courant, 0h
            $timestampheure += 24*3600;                                      // Timestamp courant +24h

            // Meilleure date entre celle donnée en paramètre, celle calculée et la dernière date en base
            $date = ($date)?min($date, $timestampheure):$timestampheure;
            //$date = min($date, getMaxDate()); // Attention, il faudra prendre maxDate à 0h

            // Avance d'un jour tant que celui-ci n'est pas un lundi
            while ( date("w", $date) != 1 )
            {
                $date += 24*3600;
            }

            // Périodes
            $timestampfin = $date;                                           // Fin de la période
            $timestampdebut2 = strtotime(date("Y-m-d", $timestampfin) . " -".$duree." week");    // Début de période active
            $timestampdebut = strtotime(date("Y-m-d", $timestampdebut2) . " -".$duree." week"); // Début de période précédente

            $xlabel = $duree . " semaines";
            $dateformatsql = "sem %v (%x)";
            $divabonnement = 52;
            break;
        case "mois":
            // Calcul de la fin de période courante
            $timestampheure = mktime(0,0,0,date("m"),date("d"),date("Y")); // Timestamp courant, 0h

            // Meilleure date entre celle donnée en paramètre, celle calculée et la dernière date en base
            $date = ($date)?min($date, $timestampheure):$timestampheure;
            //$date = min($date, getMaxDate()); // Attention, il faudra prendre maxDate à 0h

            // Avance d'un jour tant qu'on n'est pas le premier du mois
            while ( date("d", $date) != 1 )
            {
                $date += 24*3600;
            }

            // Périodes
            $timestampfin = $date;                                         // Fin de la période
            $timestampdebut2 = mktime(0,0,0,date("m",$timestampfin)-$duree,1,date("Y",$timestampfin));      // Début de période active
            $timestampdebut = mktime(0,0,0,date("m",$timestampdebut2)-$duree,1,date("Y",$timestampdebut2)); // Début de période précédente

            $xlabel = $duree . " mois";
            $dateformatsql = "%b (%Y)";
            if ($duree > 6) $dateformatsql = "%b %Y";
            $divabonnement = 12;
            break;
        case "ans":
            // Calcul de la fin de période courante
            $timestampheure = mktime(0,0,0,date("m"),date("d"),date("Y"));         // Timestamp courant, 0h

            // Meilleure date entre celle donnée en paramètre, celle calculée et la dernière date en base
            $date = ($date)?min($date, $timestampheure):$timestampheure;
            //$date = min($date, getMaxDate()); // Attention, il faudra prendre maxDate à 0h

            $date = mktime(0,0,0,1,1,date("Y", $date)+1);                          // Année suivante, 0h

            // Périodes
            $timestampfin = $date;                                                 // Fin de la période
            $timestampdebut2 = mktime(0,0,0,1,1,date("Y",$timestampfin)-$duree);   // Début de période active
            $timestampdebut = mktime(0,0,0,1,1,date("Y",$timestampdebut2)-$duree); // Début de période précédente

            $xlabel = $duree . " an";
            //$xlabel = "l'année ".(date("Y",$timestampdebut2)-$duree)." et ".(date("Y",$timestampfin)-$duree);
            $dateformatsql = "%b %Y";
            $divabonnement = 12;
            break;
        default:
            die("Periode erronée, valeurs possibles: [8jours|8semaines|8mois|1an] !");
            break;
    }

    $query = queryHistory($optarif, $timestampdebut, $dateformatsql, $timestampfin);

    $result=mysql_query($query) or die ("<b>Erreur</b> dans la requète <b>" . $query . "</b> : "  . mysql_error() . " !<br>");
    $nbenreg = mysql_num_rows($result);
    $nbenreg--;
    $kwhprec = array();
    $kwhprec_detail = array();
    $date_deb=0; // date du 1er enregistrement
    $date_fin=time();

    // On initialise à vide
    // Cas si les périodes sont "nulles", on n'aura pas d'initialisation des tableaux
    foreach($teleinfo["PERIODES"][$optarif] as $ptec){
        $kwh[$ptec] = array();
        $kwhp[$ptec] = array();
    }
    $categories = array();
    $timestp = array();
    $timestpp = array();

    // Calcul des consommations
    while ($row = mysql_fetch_array($result))
    {
        $ts = intval($row["TIMESTAMP"]);
        if ($ts < $timestampdebut2) {
            // Période précédente
            $cumul = null; // reset (sinon on cumule à chaque étape de la boucle)
            $timestpp[] = $row["TIMESTAMP"];
            foreach($teleinfo["PERIODES"][$optarif] as $ptec){
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
            foreach($teleinfo["PERIODES"][$optarif] as $ptec){
                $kwh[$ptec][] = floatval(isset($row[$ptec]) ? $row[$ptec] : 0);
            }
        }
    }

    // On vérifie la durée de la période actuelle
    if (count($kwh) < $duree) {
        // pad avec une valeur négative, pour ajouter en début de tableau
        $timestp = array_pad ($timestp, -$duree, null);
        $categories = array_pad ($categories, -$duree, null);
        foreach($kwh as &$current){
            $current = array_pad ($current, -$duree, null);
        }
    }

    // On vérifie la durée de la période précédente
    if (count($kwhprec) < count(reset($kwh))) {
        // pad avec une valeur négative, pour ajouter en début de tableau
        $timestpp = array_pad ($timestpp, -count(reset($kwh)), null);
        $kwhprec = array_pad ($kwhprec, -count(reset($kwh)), null);
        foreach($kwhp as &$current){
            $current = array_pad ($current, -count(reset($kwh)), null);
        }
    }
    mysql_free_result($result);

    $ddannee = date("Y",$date_deb);
    $ddmois = date("m",$date_deb);
    $ddjour = date("d",$date_deb);
    $ddheure = date("G",$date_deb); //Heure, au format 24h, sans les zéros initiaux
    $ddminute = date("i",$date_deb);

    $date_deb_UTC=$date_deb*1000;

    $datetext = "$ddjour/$ddmois/$ddannee  $ddheure:$ddminute";
    $ddmois=$ddmois-1; // nécessaire pour Date.UTC() en javascript qui a le mois de 0 à 11 !!!

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

        $i++ ;
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
        'title' => "Consomation sur $xlabel",
        'subtitle' => $subtitle,
        'duree' => $duree,
        'periode' => $periode,
        'debut' => $timestampfin*1000,
        'optarif' => $optarif,
        'series' => $series,
        'prix' => $mnt,
        'prix_tot' => $total_mnt,
        'PREC_prix' => $mntp,
        'PREC_prix_tot' => $total_mntp,
        'categories' => $categories,
        'PREC_color' => $teleinfo["COULEURS"]["PREC"],
        'PREC_name' => 'Période Précédente',
        'PREC_data' => $kwhprec,
        'PREC_data_detail' => $kwhp
    );

    // Ajoute les séries
    foreach($teleinfo["PERIODES"][$optarif] as $ptec) {
        $history[$ptec."_color"] = $teleinfo["COULEURS"][$ptec];
        $history[$ptec."_data"] = $kwh[$ptec];
    }

    return $history;
}

function main() {
    global $db_connect;

    $query = isset($_GET['query'])?$_GET['query']:"daily";

    if (isset($query)) {
        mysql_connect($db_connect['serveur'], $db_connect['login'], $db_connect['pass']) or die("Erreur de connexion au serveur MySql");
        mysql_select_db($db_connect['base']) or die("Erreur de connexion a la base de donnees $base");
        mysql_query("SET NAMES 'utf8'");
        mysql_query("SET lc_time_names = 'fr_FR'");  // Pour afficher date en français dans MySql.

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
      mysql_close() ;

      echo json_encode($data);
    }
}

main();

?>
