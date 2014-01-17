<?php
setlocale(LC_ALL , "fr_FR" );
date_default_timezone_set("Europe/Paris");
error_reporting(0);

// Adapté du code de Domos.
// cf . http://vesta.homelinux.net/wiki/teleinfo_papp_jpgraph.html

// Config : Connexion MySql et requête. et prix du kWh
include_once("config.php");
include_once("queries.php");

/****************************************/
/*    Graph consommation instantanée    */
/****************************************/
function instantly () {
  global $db_table;
  global $refresh_auto, $refresh_delay;

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

  $nbdata=0;
  $nbenreg = mysql_num_rows($result);
  if ($nbenreg > 0) {
    $row = mysql_fetch_array($result);
    $optarif = $row["optarif"];
    $demain = $row["demain"];
    $date_deb = $row["timestamp"];
    $val = floatval(str_replace(",", ".", $row["papp"]));
  };
  mysql_free_result($result);

  $datetext = strftime("%c",$date_deb);

  $seuils = array (
    'min' => 0,
    'max' => 10000,
  );

  $instantly = array(
    'title' => "Consommation du $datetext",
    'subtitle' => "",
    'debut' => $date_deb*1000, // $date_deb_UTC,
    'W_name' => "Watts",
    'W_data'=> $val,
    'seuils' => $seuils,  // non utilisé pour l'instant
    'optarif' => $optarif,
    'demain' => $demain,
    'refresh_auto' => $refresh_auto,
    'refresh_delay' => $refresh_delay
  );

  return $instantly;
}

/****************************************************************************************/
/*    Graph consomation w des 24 dernières heures + en parrallèle consomation d'Hier    */
/****************************************************************************************/
function daily () {
  global $db_table;

  $courbe_titre[0]="Heures de Base";
  $courbe_min[0]=5000;
  $courbe_max[0]=0;
  $courbe_titre[1]="Heures Pleines";
  $courbe_min[1]=5000;
  $courbe_max[1]=0;
  $courbe_titre[2]="Heures Creuses";
  $courbe_min[2]=5000;
  $courbe_max[2]=0;

  $courbe_titre[3]="Intensité";
  $courbe_min[3]=45;
  $courbe_max[3]=0;

  $date = isset($_GET['date'])?$_GET['date']:null;

  $heurecourante = date('H') ;              // Heure courante.
  $timestampheure = mktime($heurecourante+1,0,0,date("m"),date("d"),date("Y"));  // Timestamp courant à heure fixe (mn et s à 0).

  // Meilleure date entre celle donnée en paramètre et celle calculée
  $date = ($date)?min($date, $timestampheure):$timestampheure;

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

  $array_BASE = array();
  $array_HP = array();
  $array_HC = array();
  $array_I = array();
  $array_JPrec = array();
  $navigator = array();

  $row = mysql_fetch_array($result);
  $ts = intval($row["timestamp"]);

  while (($ts < $timestampdebut2) && ($nbenreg>0) ){
    $ts = ( $ts + 24*3600 ) * 1000;
    $val = floatval(str_replace(",", ".", $row["papp"]));
    $array_JPrec[] = array($ts, $val); // php recommande cette syntaxe plutôt que array_push
    $row = mysql_fetch_array($result);
    $ts = intval($row["timestamp"]);
    $nbenreg--;
  }

  while ($nbenreg > 0 ){
    if ($date_deb==0) {
      $date_deb = $row["timestamp"];
    }
    $ts = intval($row["timestamp"]) * 1000;
    if ( $row["ptec"] == "TH.." )      // Test si heures de base.
    {
      $val = floatval(str_replace(",", ".", $row["papp"]));
      $array_BASE[] = array($ts, $val); // php recommande cette syntaxe plutôt que array_push
      $array_HP[] = array($ts, null);
      $array_HC[] = array($ts, null);
      $navigator[] = array($ts, $val);
      if ($courbe_max[0]<$val) {$courbe_max[0] = $val; $courbe_maxdate[0] = $ts;};
      if ($courbe_min[0]>$val) {$courbe_min[0] = $val; $courbe_mindate[0] = $ts;};
    }
    elseif ( $row["ptec"] == "HP" )      // Test si heures pleines.
    {
      $val = floatval(str_replace(",", ".", $row["papp"]));
      $array_BASE[] = array($ts, null); // php recommande cette syntaxe plutôt que array_push
      $array_HP[] = array($ts, $val);
      $array_HC[] = array($ts, null);
      $navigator[] = array($ts, $val);
      if ($courbe_max[1]<$val) {$courbe_max[1] = $val; $courbe_maxdate[1] = $ts;};
      if ($courbe_min[1]>$val) {$courbe_min[1] = $val; $courbe_mindate[1] = $ts;};
    }
    elseif ( $row["ptec"] == "HC" )      // Test si heures creuses.
    {
      $val = floatval(str_replace(",", ".", $row["papp"]));
      $array_BASE[] = array($ts, null); // php recommande cette syntaxe plutôt que array_push
      $array_HP[] = array($ts, null);
      $array_HC[] = array($ts, $val);
      $navigator[] = array($ts, $val);
      if ($courbe_max[2]<$val) {$courbe_max[2] = $val; $courbe_maxdate[2] = $ts;};
      if ($courbe_min[2]>$val) {$courbe_min[2] = $val; $courbe_mindate[2] = $ts;};
    }
    elseif ( $row["ptec"] == "HPJB" )      // Test si heures pleines jours bleus.
    {
      $val = floatval(str_replace(",", ".", $row["papp"]));
      $array_BASE[] = array($ts, null);
      $array_HP[] = array($ts, $val);
      $array_HC[] = array($ts, null);
      $navigator[] = array($ts, $val);
      if ($courbe_max[2]<$val) {$courbe_max[2] = $val; $courbe_maxdate[2] = $ts;};
      if ($courbe_min[2]>$val) {$courbe_min[2] = $val; $courbe_mindate[2] = $ts;};
    }
    elseif ( $row["ptec"] == "HCJB" )      // Test si heures creuses jours bleus.
    {
      $val = floatval(str_replace(",", ".", $row["papp"]));
      $array_BASE[] = array($ts, null);
      $array_HP[] = array($ts, null);
      $array_HC[] = array($ts, $val);
      $navigator[] = array($ts, $val);
      if ($courbe_max[2]<$val) {$courbe_max[2] = $val; $courbe_maxdate[2] = $ts;};
      if ($courbe_min[2]>$val) {$courbe_min[2] = $val; $courbe_mindate[2] = $ts;};
    }
    elseif ( $row["ptec"] == "HPJW" )      // Test si heures pleines jours blancs.
    {
      $val = floatval(str_replace(",", ".", $row["papp"]));
      $array_BASE[] = array($ts, null);
      $array_HP[] = array($ts, $val);
      $array_HC[] = array($ts, null);
      $navigator[] = array($ts, $val);
      if ($courbe_max[2]<$val) {$courbe_max[2] = $val; $courbe_maxdate[2] = $ts;};
      if ($courbe_min[2]>$val) {$courbe_min[2] = $val; $courbe_mindate[2] = $ts;};
    }
    elseif ( $row["ptec"] == "HCJW" )      // Test si heures creuses jours blancs.
    {
      $val = floatval(str_replace(",", ".", $row["papp"]));
      $array_BASE[] = array($ts, null);
      $array_HP[] = array($ts, null);
      $array_HC[] = array($ts, $val);
      $navigator[] = array($ts, $val);
      if ($courbe_max[2]<$val) {$courbe_max[2] = $val; $courbe_maxdate[2] = $ts;};
      if ($courbe_min[2]>$val) {$courbe_min[2] = $val; $courbe_mindate[2] = $ts;};
    }
    elseif ( $row["ptec"] == "HPJR" )      // Test si heures pleines jours rouges.
    {
      $val = floatval(str_replace(",", ".", $row["papp"]));
      $array_BASE[] = array($ts, null);
      $array_HP[] = array($ts, $val);
      $array_HC[] = array($ts, null);
      $navigator[] = array($ts, $val);
      if ($courbe_max[2]<$val) {$courbe_max[2] = $val; $courbe_maxdate[2] = $ts;};
      if ($courbe_min[2]>$val) {$courbe_min[2] = $val; $courbe_mindate[2] = $ts;};
    }
    elseif ( $row["ptec"] == "HCJR" )      // Test si heures creuses jours rouges.
    {
      $val = floatval(str_replace(",", ".", $row["papp"]));
      $array_BASE[] = array($ts, null);
      $array_HP[] = array($ts, null);
      $array_HC[] = array($ts, $val);
      $navigator[] = array($ts, $val);
      if ($courbe_max[2]<$val) {$courbe_max[2] = $val; $courbe_maxdate[2] = $ts;};
      if ($courbe_min[2]>$val) {$courbe_min[2] = $val; $courbe_mindate[2] = $ts;};
    }

    $val = floatval(str_replace(",", ".", $row["iinst1"])) ;
    $array_I[] = array($ts, $val); // php recommande cette syntaxe plutôt que array_push
    if ($courbe_max[3]<$val) {$courbe_max[3] = $val; $courbe_maxdate[3] = $ts;};
    if ($courbe_min[3]>$val) {$courbe_min[3] = $val; $courbe_mindate[3] = $ts;};
    // récupérer prochaine occurence de la table
    $row = mysql_fetch_array($result);
    $nbenreg--;
    $nbdata++;
  }
  $optarif = $row["optarif"];
  mysql_free_result($result);

  $date_fin = $ts/1000;

  $plotlines_max = max($courbe_max[0], $courbe_max[1], $courbe_max[2]);
  $plotlines_min = min($courbe_min[0], $courbe_min[1], $courbe_min[2]);

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

  //$datetext = "$ddjour/$ddmois/$ddannee  $ddheure:$ddminute au $ddjour_fin/$ddmois_fin/$ddannee_fin  $ddheure_fin:$ddminute_fin";
  $datetext = "$ddjour/$ddmois  $ddheure:$ddminute au $ddjour_fin/$ddmois_fin  $ddheure_fin:$ddminute_fin";

  $seuils = array (
    'min' => $plotlines_min,
    'max' => $plotlines_max,
  );

  $daily = array(
    'title' => "Graph du $datetext",
    'subtitle' => "",
    'debut' => $timestampfin*1000, // $date_deb_UTC,
    'BASE_name' => $courbe_titre[0]." / min ".$courbe_min[0]." max ".$courbe_max[0],
    'BASE_data'=> $array_BASE,
    'HP_name' => $courbe_titre[1]." / min ".$courbe_min[1]." max ".$courbe_max[1],
    'HP_data' => $array_HP,
    'HC_name' => $courbe_titre[2]." / min ".$courbe_min[2]." max ".$courbe_max[2],
    'HC_data' => $array_HC,
    'I_name' => $courbe_titre[3]." / min ".$courbe_min[3]." max ".$courbe_max[3],
    'I_data' => $array_I,
    'JPrec_name' => 'Période précédente', //'Hier',
    'JPrec_data' => $array_JPrec,
    'navigator' => $navigator,
    'seuils' => $seuils,
    'optarif' => $optarif
  );

  return $daily;
}

/*************************************************************/
/*    Graph cout sur période [8jours|8semaines|8mois|1an]    */
/*************************************************************/
function history() {
  global $db_table;
  global $liste_ptec;

  $optarif = getOpTarif();
  $tab_prix = getTarifs($optarif);
  ksort($tab_prix);
  $prix = end($tab_prix);

  $duree = isset($_GET['duree'])?$_GET['duree']:8;
  $periode = isset($_GET['periode'])?$_GET['periode']:"jours";
  $date = isset($_GET['date'])?$_GET['date']:null;

  switch ($periode) {
    case "jours":
      // Calcul de la fin de période courante
      $timestampheure = mktime(0,0,0,date("m"),date("d"),date("Y"));   // Timestamp courant, 0h
      $timestampheure += 24*3600;                                      // Timestamp courant +24h

      // Meilleure date entre celle donnée en paramètre et celle calculée
      $date = ($date)?min($date, $timestampheure):$timestampheure;

      // Périodes
      $periodesecondes = $duree*24*3600;                               // Periode en secondes
      $timestampfin = $date;                                           // Fin de la période
      $timestampdebut2 = $timestampfin - $periodesecondes;             // Début de période active
      $timestampdebut = $timestampdebut2 - $periodesecondes;           // Début de période précédente

      $xlabel = $duree  . " jours";
      $dateformatsql = "%a %e";
      $abonnement = $prix["AboAnnuel"] / 365;
      break;
    case "semaines":
      // Calcul de la fin de période courante
      $timestampheure = mktime(0,0,0,date("m"),date("d"),date("Y"));   // Timestamp courant, 0h
      $timestampheure += 24*3600;                                      // Timestamp courant +24h

      // Meilleure date entre celle donnée en paramètre et celle calculée
      $date = ($date)?min($date, $timestampheure):$timestampheure;

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
      $abonnement = $prix["AboAnnuel"] / 52;
      break;
    case "mois":
      // Calcul de la fin de période courante
      $timestampheure = mktime(0,0,0,date("m"),date("d"),date("Y")); // Timestamp courant, 0h
      //$timestampheure = mktime(0,0,0,date("m")+1,1,date("Y"));     // Mois suivant, 0h

      // Meilleure date entre celle donnée en paramètre et celle calculée
      $date = ($date)?min($date, $timestampheure):$timestampheure;
      $date = mktime(0,0,0,date("m")+1,1,date("Y"));                 // Mois suivant, 0h

      // Périodes
      $timestampfin = $date;                                         // Fin de la période
      $timestampdebut2 = mktime(0,0,0,date("m",$timestampfin)-$duree,1,date("Y",$timestampfin));      // Début de période active
      $timestampdebut = mktime(0,0,0,date("m",$timestampdebut2)-$duree,1,date("Y",$timestampdebut2)); // Début de période précédente

      $xlabel = $duree . " mois";
      $dateformatsql = "%b (%Y)";
      if ($duree > 6) $dateformatsql = "%b %Y";
      $abonnement = $prix["AboAnnuel"] / 12;
      break;
    case "ans":
      // Calcul de la fin de période courante
      $timestampheure = mktime(0,0,0,date("m"),date("d"),date("Y"));         // Timestamp courant, 0h

      // Meilleure date entre celle donnée en paramètre et celle calculée
      $date = ($date)?min($date, $timestampheure):$timestampheure;
      $date = mktime(0,0,0,1,1,date("Y", $date)+1);                          // Année suivante, 0h

      // Périodes
      $timestampfin = $date;                                                 // Fin de la période
      $timestampdebut2 = mktime(0,0,0,1,1,date("Y",$timestampfin)-$duree);   // Début de période active
      $timestampdebut = mktime(0,0,0,1,1,date("Y",$timestampdebut2)-$duree); // Début de période précédente

      $xlabel = $duree . " an";
      //$xlabel = "l'année ".(date("Y",$timestampdebut2)-$duree)." et ".(date("Y",$timestampfin)-$duree);
      $dateformatsql = "%b %Y";
      $abonnement = $prix["AboAnnuel"] / 12;
      break;
    default:
      die("Periode erronée, valeurs possibles: [8jours|8semaines|8mois|1an] !");
      break;
  }

  $query="SET lc_time_names = 'fr_FR'" ;  // Pour afficher date en français dans MySql.
  mysql_query($query);

  $query = queryHistory($timestampdebut, $dateformatsql, $timestampfin);

  $result=mysql_query($query) or die ("<b>Erreur</b> dans la requète <b>" . $query . "</b> : "  . mysql_error() . " !<br>");
  $nbenreg = mysql_num_rows($result);
  $nbenreg--;
  $kwhprec = array();
  $kwhprec_detail = array();
  $date_deb=0; // date du 1er enregistrement
  $date_fin=time();

  // On initialise à vide
  // Cas si la période précédente est "nulle", on n'aura pas d'initialisation du tableau
  foreach($liste_ptec[$optarif] as $ptec => $caption){
    $kwhp[$ptec] = [];
  }

  // Calcul des consommations
  while ($row = mysql_fetch_array($result))
  {
    $ts = intval($row["timestamp"]);
    if ($ts < $timestampdebut2) {
      // Période précédente
      $cumul = null; // reset (sinon on cumule à chaque étape de la boucle)
      foreach($liste_ptec[$optarif] as $ptec => $caption){
        // On conserve le détail (qui sera affiché en infobulle)
        $kwhp[$ptec][] = floatval(isset($row[$ptec]) ? $row[$ptec] : 0);
        // On calcule le total consommé (qui sera affiché en courbe)
        $cumul[] = isset($row[$ptec]) ? $row[$ptec] : 0;
      }
      $kwhprec[] = array($row["periode"], array_sum($cumul)); // php recommande cette syntaxe plutôt que array_push
    }
    else {
      // Période courante
      if ($date_deb==0) {
        $date_deb = strtotime($row["rec_date"]);
      }
      // Ajout les éléments actuels à chaque tableau
      $rdate[] = $row["rec_date"];
      $timestp[] = $row["periode"];
      foreach($liste_ptec[$optarif] as $ptec => $caption){
        $kwh[$ptec][] = floatval(isset($row[$ptec]) ? $row[$ptec] : 0);
      }
    }
  }

  // On vérifie la durée de la période actuelle
  if (count($kwh) < $duree) {
    // pad avec une valeur négative, pour ajouter en début de tableau
    $timestp = array_pad ($timestp, -$duree, null);
    foreach($kwh as &$current){
      $current = array_pad ($current, -$duree, null);
    }
  }

  // On vérifie la durée de la période précédente
  if (count($kwhprec) < count(reset($kwh))) {
    // pad avec une valeur négative, pour ajouter en début de tableau
    $kwhprec = array_pad ($kwhprec, -count(reset($kwh)), null);
    foreach($kwhp as &$current){
      $current = array_pad ($current, -count(reset($kwh)), null);
    }
  }
  $date_digits_dernier_releve=explode("-", $rdate[count($rdate) -1]) ;
  $date_dernier_releve =  Date('d/m/Y', gmmktime(0,0,0, $date_digits_dernier_releve[1] ,$date_digits_dernier_releve[2], $date_digits_dernier_releve[0])) ;

  mysql_free_result($result);

  $ddannee = date("Y",$date_deb);
  $ddmois = date("m",$date_deb);
  $ddjour = date("d",$date_deb);
  $ddheure = date("G",$date_deb); //Heure, au format 24h, sans les zéros initiaux
  $ddminute = date("i",$date_deb);

  $date_deb_UTC=$date_deb*1000;

  $datetext = "$ddjour/$ddmois/$ddannee  $ddheure:$ddminute";
  $ddmois=$ddmois-1; // nécessaire pour Date.UTC() en javascript qui a le mois de 0 à 11 !!!

  // Calcul des consommations
  foreach($liste_ptec[$optarif] as $ptec => $caption){
    $mnt_kwh[$ptec] = 0;
    $total_kwh[$ptec] = 0;
    $mnt_kwhp[$ptec] = 0;
    $total_kwhp[$ptec] = 0;
  }

  $mnt_abo = 0;
  $mnt_abop = 0;
  $i = 0;

  while ($i < count(reset($kwh))) {
    foreach($liste_ptec[$optarif] as $ptec => $caption) {
      $mnt_kwh[$ptec] += $kwh[$ptec][$i] * $prix["periode"][strtoupper($ptec)];
      $total_kwh[$ptec] += $kwh[$ptec][$i];

      $mnt_kwhp[$ptec] += $kwhp[$ptec][$i] * $prix["periode"][strtoupper($ptec)];
      $total_kwhp[$ptec] += $kwhp[$ptec][$i];
    }
    $mnt_abo += $abonnement;
    $mnt_abop += $abonnement;
    $i++ ;
  }
  $mnt_total = $mnt_abo + array_sum($mnt_kwh);
  $mnt_totalp = $mnt_abop + array_sum($mnt_kwhp);

  /* Prix à retourner */
  $prix = $prix["periode"];
  $prix["abonnement"] = $abonnement;

  // Subtitle pour la période courante
  $subtitle = "<b>Coût sur la période</b> ".round($mnt_total,2)." Euro (".array_sum($total_kwh)." KWh)<br />";
  $subtitle = $subtitle."(Abonnement : ".round($mnt_abo,2);
  foreach($liste_ptec[$optarif] as $ptec => $caption) {
    if ($mnt_kwh[$ptec] != 0) {
      $subtitle = $subtitle." + ".$ptec." : ".round($mnt_kwh[$ptec],2);
    }
  }
  $subtitle = $subtitle.")<br /><b>Total KWh</b> ";

  $prefix = "";
  foreach($liste_ptec[$optarif] as $ptec => $caption) {
    if ($total_kwh[$ptec] != 0) {
      $subtitle = $subtitle.$prefix.$ptec." : ".$total_kwh[$ptec];
      if ($prefix=="") {
        $prefix = " + ";
      }
    }
  }

  // Subtitle pour la période courante
  $subtitle = "<b>Coût sur la période</b> ".round($mnt_total,2)." Euro (".array_sum($total_kwh)." KWh)<br />";
  $subtitle = $subtitle."(Abonnement : ".round($mnt_abo,2);
  foreach($liste_ptec[$optarif] as $ptec => $caption) {
    if ($mnt_kwh[$ptec] != 0) {
      $subtitle = $subtitle." + ".$ptec." : ".round($mnt_kwh[$ptec],2);
    }
  }
  $subtitle = $subtitle.")";
  if ((count($liste_ptec[$optarif]) > 1) && (array_sum($total_kwh) > 0)) {
    $subtitle = $subtitle."<br /><b>Total KWh</b> ";
    $prefix = "";
    foreach($liste_ptec[$optarif] as $ptec => $caption) {
      if ($total_kwh[$ptec] != 0) {
        $subtitle = $subtitle.$prefix.$ptec." : ".$total_kwh[$ptec];
        if ($prefix=="") {
          $prefix = " + ";
        }
      }
    }
  }

  // Subtitle pour la période précédente
  $subtitle = $subtitle."<br /><b>Coût sur la période précédente</b> ".round($mnt_totalp,2)." Euro (".array_sum($total_kwhp)." KWh)<br />";
  $subtitle = $subtitle."(Abonnement : ".round($mnt_abo,2);
  foreach($liste_ptec[$optarif] as $ptec => $caption) {
    if ($mnt_kwhp[$ptec] != 0) {
      $subtitle = $subtitle." + ".$ptec." : ".round($mnt_kwhp[$ptec],2);
    }
  }
  $subtitle = $subtitle.")";
  if ((count($liste_ptec[$optarif]) > 1) && (array_sum($total_kwhp) > 0)) {
    $subtitle = $subtitle."<br /><b>Total KWh</b> ";
    $prefix = "";
    foreach($liste_ptec[$optarif] as $ptec => $caption) {
      if ($total_kwhp[$ptec] != 0) {
        $subtitle = $subtitle.$prefix.$ptec." : ".$total_kwhp[$ptec];
        if ($prefix=="") {
          $prefix = " + ";
        }
      }
    }
  }

  $history = array(
    'title' => "Consomation sur $xlabel",
    'subtitle' => $subtitle,
    'duree' => $duree,
    'periode' => $periode,
    'debut' => $timestampfin*1000,
    'optarif' => $optarif,
    'series' => $liste_ptec[$optarif],
    'prix' => $prix,
    'categories' => $timestp,
    'PREC_name' => 'Période Précédente',
    'PREC_data' => $kwhprec,
    'PREC_data_detail' => $kwhp
  );

  // Ajoute les séries
  foreach($liste_ptec[$optarif] as $ptec => $caption) {
    $history[$ptec."_data"] = $kwh[$ptec];

  }

  return $history;
}

$query = isset($_GET['query'])?$_GET['query']:"daily";

if (isset($query)) {
  mysql_connect($db_serveur, $db_login, $db_pass) or die("Erreur de connexion au serveur MySql");
  mysql_select_db($db_base) or die("Erreur de connexion a la base de donnees $base");
  mysql_query("SET NAMES 'utf8'");

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
  echo json_encode($data);

  mysql_close() ;
}

?>
