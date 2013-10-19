<?php
setlocale(LC_ALL , "fr_FR" );
date_default_timezone_set("Europe/Paris");

// Adapté du code de Domos.
// cf . http://vesta.homelinux.net/wiki/teleinfo_papp_jpgraph.html

// Base de donnée Téléinfo:
/*
Format de la table:
timestamp   rec_date   rec_time   adco     optarif isousc   hchp     hchc     ptec   inst1   inst2   inst3   imax1   imax2   imax3   pmax   papp   hhphc   motdetat   ppot   adir1   adir2   adir3
1234998004   2009-02-19   00:00:04   700609361116   HC..   20   11008467   10490214   HP   1   0   1   18   23   22   8780   400   E   000000     00   0   0   0
1234998065   2009-02-19   00:01:05   700609361116   HC..   20   11008473   10490214   HP   1   0   1   18   23   22   8780   400   E   000000     00   0   0   0
1234998124   2009-02-19   00:02:04   700609361116   HC..   20   11008479   10490214   HP   1   0   1   18   23   22   8780   390   E   000000     00   0   0   0
1234998185   2009-02-19   00:03:05   700609361116   HC..   20   11008484   10490214   HP   1   0   0   18   23   22   8780   330   E   000000     00   0   0   0
1234998244   2009-02-19   00:04:04   700609361116   HC..   20   11008489   10490214   HP   1   0   0   18   23   22   8780   330   E   000000     00   0   0   0
1234998304   2009-02-19   00:05:04   700609361116   HC..   20   11008493   10490214   HP   1   0   0   18   23   22   8780   330   E   000000     00   0   0   0
1234998365   2009-02-19   00:06:05   700609361116   HC..   20   11008498   10490214   HP   1   0   0   18   23   22   8780   320   E   000000     00   0   0   0
*/

// Connexion MySql et requête.
$serveur="localhost";
$login="teleinfo";
$base="teleinfo";
$table="teleinfo";
$pass="teleinfo";

// prix du kWh :
// prix TTC au 1/01/2012 :
$prixBASE = (0.0812+0.009+0.009)*1.196; // kWh + CSPE + TCFE, TVA 19.6%
$prixHP = 0;
$prixHC = 0;
// Abpnnement pour disjoncteur 30 A
$abo_annuel = 12*(5.36+1.92/2)*1.055; // Abonnement + CTA, TVA 5.5%

/***************************************/
/*    Graph consommation instantanée    */
/***************************************/
function instantly () {
  global $table;

  $date = isset($_GET['date'])?$_GET['date']:null;

  $heurecourante = date('H') ;              // Heure courante.
  $timestampheure = mktime($heurecourante+1,0,0,date("m"),date("d"),date("Y"));  // Timestamp courant à heure fixe (mn et s à 0).

  // Meilleure date entre celle donnée en paramètre et celle calculée
  $date = ($date)?min($date, $timestampheure):$timestampheure;

  $periodesecondes = 24*3600 ;                            // 24h.
  $timestampfin = $date;
  $timestampdebut2 = $date - $periodesecondes ;           // Recule de 24h.
  $timestampdebut = $timestampdebut2 - $periodesecondes ; // Recule de 24h.

  $query="SELECT unix_timestamp(date) as timestamp, date(date) as rec_date, time(date) as rec_time, ptec, papp, iinst1
    FROM `$table`
    WHERE date=(select max(date) FROM `$table`)";

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
  );
}

/****************************************************************************************/
/*    Graph consommation w des 24 dernières heures + en parrallèle consommation d'Hier    */
/****************************************************************************************/
function daily () {
  global $table;

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

  $query="SELECT unix_timestamp(date) as timestamp, date(date) as rec_date, time(date) as rec_time, ptec, papp, iinst1
    FROM `$table`
    WHERE unix_timestamp(date) >= $timestampdebut
    AND unix_timestamp(date) < $timestampfin
    ORDER BY unix_timestamp(date)";

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
    );
}

/*************************************************************/
/*    Graph cout sur période [8jours|8semaines|8mois|1an]    */
/*************************************************************/
function history() {
  global $table;
  global $abo_annuel;
  global $prixBASE;
  global $prixHP;
  global $prixHC;

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
      $dateformatsql = "sem %v";
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
      $dateformatsql = "%b";
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
      $dateformatsql = "%b";
      $abonnement = $abo_annuel / 12;
      break;
    default:
      die("Periode erronée, valeurs possibles: [8jours|8semaines|8mois|1an] !");
      break;
  }

  /*print_r(date("r", $timestampdebut));
  print_r(date("r", $timestampdebut2));
  print_r(date("r", $timestampfin));
  die();/**/

  $query="SET lc_time_names = 'fr_FR'" ;  // Pour afficher date en français dans MySql.
  mysql_query($query) ;
  $query="SELECT unix_timestamp(date) as timestamp, date(date) as rec_date, DATE_FORMAT(date(date), '$dateformatsql') AS 'periode' ,
    ROUND( ((MAX(`base`) - MIN(`base`)) / 1000) ,1 ),
    ROUND( ((MAX(`hchp`) - MIN(`hchp`)) / 1000) ,1 ),
    ROUND( ((MAX(`hchc`) - MIN(`hchc`)) / 1000) ,1 )
    FROM `$table`
    WHERE unix_timestamp(date) >= '$timestampdebut'
    AND unix_timestamp(date) < '$timestampfin'
    GROUP BY periode
    ORDER BY rec_date" ;
  //die ($query);
  $result=mysql_query($query) or die ("<b>Erreur</b> dans la requète <b>" . $query . "</b> : "  . mysql_error() . " !<br>");
  $nbenreg = mysql_num_rows($result);
  $nbenreg--;
  $kwhprec = array();
  $no = 0 ;
  $date_deb=0; // date du 1er enregistrement
  $date_fin=time();

  while ($row = mysql_fetch_array($result))
  {
    $ts = intval($row["timestamp"]);
    if ($ts < $timestampdebut2) {
      $val = floatval(str_replace(",", ".", $row[3]))
        + floatval(str_replace(",", ".", $row[4]))
        + floatval(str_replace(",", ".", $row[5]));
      $kwhprec[] = array($row["periode"], $val); // php recommande cette syntaxe plutôt que array_push
//      $kwhprec[] = $val; // php recommande cette syntaxe plutôt que array_push
    }
    else {
      if ($date_deb==0) {
        $date_deb = strtotime($row["rec_date"]);
      }
      $date[$no] = $row["rec_date"];
      $timestp[$no] = $row["periode"];
      $kwhbase[$no]=floatval(str_replace(",", ".", $row[3]));
      $kwhhp[$no]=floatval(str_replace(",", ".", $row[4]));
      $kwhhc[$no]=floatval(str_replace(",", ".", $row[5]));
      $no++ ;
    }
  }

  if (count($kwhprec)<count($kwhbase)) {
    // pad avec une valeur négative, pour ajouter en début de tableau
    $kwhprec = array_pad ($kwhprec, -count($kwhbase), null);
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
  $mnt_kwhhp = 0;
  $mnt_kwhhc = 0;
  $mnt_abonnement = 0;
  $i = 0;
  while ($i < count($kwhhp))
  {
    $mnt_kwhbase += $kwhbase[$i] * $prixBASE;
    $mnt_kwhhp += $kwhhp[$i] * $prixHP;
    $mnt_kwhhc += $kwhhc[$i] * $prixHC;
    $mnt_abonnement += $abonnement;
    $i++ ;
  }

  $mnt_total = $mnt_abonnement + $mnt_kwhbase + $mnt_kwhhp + $mnt_kwhhc;

  $prix = array (
    'abonnement' => $abonnement,
    'BASE' => $prixBASE,
    'HP' => $prixHP,
    'HC' => $prixHC,
  );

  return array(
    'title' => "Consommation sur $xlabel",
    'subtitle' => "Coût sur la période ".round($mnt_total,2)." Euro<br />( Abonnement : ".round($mnt_abonnement,2)." + BASE : ".round($mnt_kwhbase,2)." + HP : ".round($mnt_kwhhp,2)." + HC : ".round($mnt_kwhhc,2)." )",
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
    'categories' => $timestp,
    'prix' => $prix,
    );
}

$query = isset($_GET['query'])?$_GET['query']:"daily";

if (isset($query)) {
  mysql_connect($serveur, $login, $pass) or die("Erreur de connexion au serveur MySql");
  mysql_select_db($base) or die("Erreur de connexion a la base de donnees $base");
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
