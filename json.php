<?php
setlocale(LC_ALL , "fr_FR" );
date_default_timezone_set("Europe/Paris");
error_reporting(0);

// Adapté du code de Domos.
// cf . http://vesta.homelinux.net/wiki/teleinfo_papp_jpgraph.html

// Config : Connexion MySql et requête. et prix du kWh
include_once("config.php");

/****************************************/
/*    Graph consommation instantanée    */
/****************************************/
function instantly () {
  global $db_table;
  global $tarif_type;
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
    $date_deb = $row["timestamp"];
    $val = floatval(str_replace(",", ".", $row["papp"]));
  };

  $seuils = array (
    'min' => 0,
    'max' => 10000,
  );


  return array(
    'title' => "Graph du $datetext",
    'subtitle' => "",
    'debut' => $date_deb*1000, // $date_deb_UTC,
    'W_name' => "Watts",
    'W_data'=> $val,
    'seuils' => $seuils,  // non utilisé pour l'instant
    'tarif_type' => $tarif_type,
    'refresh_auto' => $refresh_auto,
    'refresh_delay' => $refresh_delay
  );
}

/****************************************************************************************/
/*    Graph consomation w des 24 dernières heures + en parrallèle consomation d'Hier    */
/****************************************************************************************/
function daily () {
  global $db_table;
  global $tarif_type;

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

  return array(
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
    'tarif_type' => $tarif_type
    );
}

/*************************************************************/
/*    Graph cout sur période [8jours|8semaines|8mois|1an]    */
/*************************************************************/
function history() {
  global $db_table;
  global $abo_annuel;
  global $prixBASE;
  global $prixHP;
  global $prixHC;
  global $tarif_type;

  $tab_prix = getTarifs($tarif_type);

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
      $abonnement = $abo_annuel / 365;
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
      $dateformatsql = "sem %v (%Y)";
      $abonnement = $abo_annuel / 52;
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
      $abonnement = $abo_annuel / 12;
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
      $abonnement = $abo_annuel / 12;
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
  $no = 0 ;
  $date_deb=0; // date du 1er enregistrement
  $date_fin=time();

  while ($row = mysql_fetch_array($result))
  {
    $ts = intval($row["timestamp"]);
    if ($ts < $timestampdebut2) {
      $val = floatval(str_replace(",", ".", $row[base]))
        + floatval(str_replace(",", ".", $row[hp]))
        + floatval(str_replace(",", ".", $row[hc]));
      $kwhprec[] = array($row["periode"], $val); // php recommande cette syntaxe plutôt que array_push
      $kwhprec_detail[] = array($row[base], $row[hp], $row[hc]);
//      $kwhprec[] = $val; // php recommande cette syntaxe plutôt que array_push
    }
    else {
      if ($date_deb==0) {
        $date_deb = strtotime($row["rec_date"]);
      }
      $date[$no] = $row["rec_date"];
      $timestp[$no] = $row["periode"];
      $kwhbase[$no]=floatval(str_replace(",", ".", $row[base]));
      $kwhhp[$no]=floatval(str_replace(",", ".", $row[hp]));
      $kwhhc[$no]=floatval(str_replace(",", ".", $row[hc]));
      $no++ ;
    }
  }

  if (count($kwhprec)<count($kwhbase)) {
    // pad avec une valeur négative, pour ajouter en début de tableau
    $kwhprec = array_pad ($kwhprec, -count($kwhbase), null);
    $kwhprec_detail = array_pad ($kwhprec_detail, -count($kwhbase), null);
  }

  $date_digits_dernier_releve=explode("-", $date[count($date) -1]) ;
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

  $mnt_kwhbase = 0;
  $total_kwhbase = 0;
  $mnt_kwhhp = 0;
  $total_kwhhp = 0;
  $mnt_kwhhc = 0;
  $total_kwhhc = 0;
  $mnt_abonnement = 0;
  $i = 0;
  while ($i < count($kwhhp))
  {
    $mnt_kwhbase += $kwhbase[$i] * $prixBASE;
    $total_kwhbase += $kwhbase[$i];
    $mnt_kwhhp += $kwhhp[$i] * $prixHP;
    $total_kwhhp += $kwhhp[$i];
    $mnt_kwhhc += $kwhhc[$i] * $prixHC;
    $total_kwhhc += $kwhhc[$i];
    $mnt_abonnement += $abonnement;
    $i++ ;
  }

  $mnt_total = $mnt_abonnement + $mnt_kwhbase + $mnt_kwhhp + $mnt_kwhhc;

  $mnt_kwhbase_Prec = 0;
  $total_kwhbase_Prec = 0;
  $mnt_kwhhp_Prec = 0;
  $total_kwhhp_Prec = 0;
  $mnt_kwhhc_Prec = 0;
  $total_kwhhc_Prec = 0;
  $mnt_abonnement_Prec = 0;
  $i = 0;
  while ($i < count($kwhprec_detail))
  {
    $mnt_kwhbase_Prec += $kwhprec_detail[$i][0] * $prixBASE;
    $mnt_kwhhp_Prec += $kwhprec_detail[$i][1] * $prixHP;
    $total_kwhhp_Prec += $kwhprec_detail[$i][1];
    $mnt_kwhhc_Prec += $kwhprec_detail[$i][2] * $prixHC;
    $total_kwhhc_Prec += $kwhprec_detail[$i][2];
    $mnt_abonnement_Prec += $abonnement;
    $i++ ;
  }

  $mnt_total_Prec = $mnt_abonnement_Prec + $mnt_kwhbase_Prec + $mnt_kwhhp_Prec + $mnt_kwhhc_Prec;

  $prix = array (
    'abonnement' => $abonnement,
    'BASE' => $prixBASE,
    'HP' => $prixHP,
    'HC' => $prixHC,
  );

  if ($tarif_type == "HCHP") {
    $subtitle = "Coût sur la période ".round($mnt_total,2)." Euro<br />( Abonnement : ".round($mnt_abonnement,2)." + HP : ".round($mnt_kwhhp,2)." + HC : ".round($mnt_kwhhc,2)." )";
    $subtitle = $subtitle."<br /> Total KWhHP : $total_kwhhp Total KWhHC : $total_kwhhc Cumul : " . ($total_kwhhp + $total_kwhhc);
    $subtitle = $subtitle."<br />Coût sur la période précédente ".round($mnt_total_Prec,2)." Euro<br />( Abonnement : ".round($mnt_abonnement_Prec,2)." + HP : ".round($mnt_kwhhp_Prec,2)." + HC : ".round($mnt_kwhhc_Prec,2)." )";
    $subtitle = $subtitle."<br /> Total KWhHP : $total_kwhhp_Prec Total KWhHC : $total_kwhhc_Prec Cumul : " . ($total_kwhhp_Prec + $total_kwhhc_Prec);
  } else {
    $subtitle = "Coût sur la période ".round($mnt_total,2)." Euro<br />( Abonnement : ".round($mnt_abonnement,2)." + BASE : ".round($mnt_kwhbase,2)." )";
    $subtitle = $subtitle."<br />Coût sur la période précédente ".round($mnt_total_Prec,2)." Euro<br />( Abonnement : ".round($mnt_abonnement_Prec,2)." + BASE : ".round($mnt_kwhbase_Prec,2)." )";
  }

  return array(
    'title' => "Consomation sur $xlabel",
    'subtitle' => $subtitle,
    'duree' => $duree,
    'periode' => $periode,
    'debut' => $timestampfin*1000,
    'BASE_name' => 'Heures de Base',
    'BASE_data'=> $kwhbase,
    'HP_name' => 'Heures Pleines',
    'HP_data' => $kwhhp,
    'HC_name' => 'Heures Creuses',
    'HC_data' => $kwhhc,
    'PREC_name' => 'Période Précédente',
    'PREC_data' => $kwhprec,
    'PREC_data_detail' => $kwhprec_detail,
    'categories' => $timestp,
    'prix' => $prix,
    'tarif_type' => $tarif_type
    );
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
