<?php

// Connexion MySql et requête.
$serveur="localhost"; 
$login="teleinfo";
$base="domotique";
$table="teleinfo";
$pass="";

$tarif_type = "HCHP"; // vaut soit "HCHP" soit "BASE"

// prix du kWh :
// prix TTC au 1/01/2012 :
if ( $tarif_type != "HCHP") {
  // prix tarif Base EDF
  $prixBASE = (0.0812+0.009+0.009)*1.196; // kWh + CSPE + TCFE, TVA 19.6%
  $prixHP = 0;
  $prixHC = 0;
  // Abpnnement pour disjoncteur 30 A
  $abo_annuel = 12*(5.36+1.92/2)*1.055; // Abonnement + CTA, TVA 5.5%
} else {
  // prix tarif HP/HC EDF
  $prixBASE = 0;
  $prixHP = 0.1312;
  $prixHC = 0.0895;
  // Abpnnement pour disjoncteur 45 A
  $abo_annuel = 112.87;
}


/* Requêtes à adapter en fonction de la structure de votre table */ 
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

function querydaily ($timestampdebut, $timestampfin) {
  global $table;

  /*
  $query="SELECT UNIX_TIMESTAMP(date) AS timestamp, date(date) AS rec_date, time(date) AS rec_time, ptec, papp, iinst1
    FROM `$table` 
    WHERE UNIX_TIMESTAMP(date) >= $timestampdebut 
    AND UNIX_TIMESTAMP(date) < $timestampfin 
    ORDER BY UNIX_TIMESTAMP(date)";
  */

  $query="SELECT timestamp, rec_date, rec_time, ptec, papp, inst1  AS iinst1 
    FROM `$table` 
    WHERE timestamp >= $timestampdebut 
    AND timestamp < $timestampfin 
    ORDER BY timestamp";

  return $query;
}

function queryhistory ($timestampdebut, $dateformatsql) {
  global $table;
 
  /*
  $query="SELECT date(date) AS rec_date, DATE_FORMAT(date(date), '$dateformatsql') AS 'periode' ,
    ROUND( ((MAX(`base`) - MIN(`base`)) / 1000) ,1 ) AS base, 
    ROUND( ((MAX(`hchp`) - MIN(`hchp`)) / 1000) ,1 ) AS hp, 
    ROUND( ((MAX(`hchc`) - MIN(`hchc`)) / 1000) ,1 ) AS hc 
    FROM `$table` 
    WHERE UNIX_TIMESTAMP(date) > '$timestampdebut'
    GROUP BY periode
    ORDER BY rec_date" ;
  */

  $query="SELECT rec_date, DATE_FORMAT(rec_date, '$dateformatsql') AS 'periode' ,
    ROUND( ((MAX(`hchp`) - MIN(`hchp`)) / 1000) ,1 ) AS hp, 
    ROUND( ((MAX(`hchc`) - MIN(`hchc`)) / 1000) ,1 ) AS hc  
    FROM `$table` 
    WHERE timestamp > '$timestampdebut'
    GROUP BY periode
    ORDER BY rec_date" ;

  return $query;
}


?>
