<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta content="no-cache" http-equiv="Pragma">
    <title>graph conso électrique</title>

<?php

if (!isset($_GET["jquery"])) {
    echo '<script type="text/javascript" src="./js/jquery-1.4.2.min.js"></script>';
    echo '<script type="text/javascript" src="./js/jquery-ui-1.8.5.custom.min.js"></script>';  
    echo '<link rel="stylesheet" href="./css/themes/ui-lightness/jquery.ui.all.css">';
        
    echo '<script type="text/javascript" src="./js/highcharts_sv.js"></script>'; 
    echo '<script type="text/javascript" src="./js/highchartsexporting.js"></script>';
}
?>
    
<?php

 
// Connexion MySql et requète.
$serveur="localhost"; 
$login="root";
$base="domotique";
$table="teleinfo";
$pass="";

// Génére un graphe en image PNG en focntion des donnée téléinfo PAPP de la base MySql.
// Puissance apparente en watts HP et HC.
// Par Domos.
// cf . http://vesta.homelinux.net/wiki/teleinfo_papp_jpgraph.html


$N_device = 1;
$x2=array();
$x2[0] = array();
$x2[1] = array();
$N_device3 = 0;
$x3[0] = array();

$N_device4 = 1;

$courbe_titre[0]="Heure Pleines";
$courbe_min[0]=5000;
$courbe_max[0]=0;
$courbe_titre[1]="Heure Creuses";
$courbe_min[1]=5000;
$courbe_max[1]=0;

$courbe_titre[2]="Intensitée";
$courbe_min[2]=45;
$courbe_max[2]=0;
 
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
 
setlocale   ( LC_ALL , "fr_FR" );
 
// Formatage Date.
function TimeCallback($aVal) 
{
    return Date('H:i', $aVal);
}
 
$periodesecondes = 24*3600 ;              // 24h.
$heurecourante = date('H') ;              // Heure courante.
$timestampheure = gmmktime($heurecourante,0,0,date("m"),date("d"),date("Y"));  // Timestamp courant à heure fixe (mn et s à 0).
$timestampdebut = $timestampheure - $periodesecondes ;        // Recule de 24h.


mysql_connect($serveur, $login, $pass) or die("Erreur de connexion au serveur MySql");
mysql_select_db($base) or die("Erreur de connexion a la base de donnees $base");
 


$query="SELECT timestamp, rec_date, rec_time, ptec, papp, inst1 
  FROM `$table` 
  WHERE timestamp >= $timestampdebut 
  ORDER BY timestamp" ;
 
$result=mysql_query($query) or die ("<b>Erreur</b> dans la requète <b>" . $query . "</b> : "  . mysql_error() . " !<br>");

    $nbdata=0; 
    $nbenreg = mysql_num_rows($result);
    $nbenreg--;
    $date_deb=0; // date du 1er enregistrement
    $date_fin=time();
    $dateheure=$date_deb;

    $row = mysql_fetch_array($result);
    while ($nbenreg >0 or $dateheure < $date_fin){
      if ($row["timestamp"] == $dateheure or ($row["timestamp"] <= ($dateheure + 20)) or $date_deb==0) {
        if ($date_deb==0) {
          $date_deb = $row["timestamp"];
          $dateheure = $row["timestamp"];
        }
      
        $timestp[$nbdata] = intval($row["timestamp"]) ;
        
        if ( $row["ptec"] == "HP" )      // Test si heures pleines.
        {
          $x2[0][$nbdata] = floatval(str_replace(",", ".", $row["papp"]));
          if ($nbdata != 0 and $x2[0][$nbdata-1] == "null"){$x2[0][$nbdata-1] = $x2[1][$nbdata-1];} 
          $x2[1][$nbdata] = "null" ;
          // max et min de la courbes
          $val=$x2[0][$nbdata]; 
          if ($courbe_max[0]<$val) {$courbe_max[0] = $val; $courbe_maxdate[0] = $row["timestamp"];};
          if ($courbe_min[0]>$val) {$courbe_min[0] = $val; $courbe_mindate[0] = $row["timestamp"];};
        }
        else
        {
          if ( $row["ptec"] == "HC" )
          {
            $x2[0][$nbdata] = "null" ;
            $x2[1][$nbdata] = floatval(str_replace(",", ".", $row["papp"]));
            if ($nbdata != 0 and $x2[1][$nbdata-1] == "null"){$x2[1][$nbdata-1] = $x2[0][$nbdata-1];}
            // max et min de la courbes
            $val=$x2[1][$nbdata]; 
            if ($courbe_max[1]<$val) {$courbe_max[1] = $val; $courbe_maxdate[1] = $row["timestamp"];};
            if ($courbe_min[1]>$val) {$courbe_min[1] = $val; $courbe_mindate[1] = $row["timestamp"];};
          }
        }
        $x3[0][$nbdata] = floatval(str_replace(",", ".", $row["inst1"])) ;
        
        // max et min de la courbes $n et moyenne
        $val=$x3[0][$nbdata];
        if ($courbe_max[2]<$val) {$courbe_max[2] = $val; $courbe_maxdate[2] = $row["timestamp"];};
        if ($courbe_min[2]>$val) {$courbe_min[2] = $val; $courbe_mindate[2] = $row["timestamp"];};
        
        // récupérer prochaine occurence de la table
        $row = mysql_fetch_array($result);
        $nbenreg--;
      
      }else {
        $x2[0][$nbdata] = "null";
        $x2[1][$nbdata] = "null";
        $x3[0][$nbdata] = "null";
      }
      $dateheure = $dateheure + 60 ; // Ajout de 1 minute   
      $nbdata++;
    }

mysql_free_result($result) ;

$periode = $_GET['periode'] ;
 
if (! $periode) { $periode = "8jours" ; } ;
switch ($periode) {
  case "8jours":
    $nbjours = 7 ;                // nb jours.
    $xlabel = "jours" ;
    $periodesecondes = $nbjours*24*3600 ;          // Periode en secondes.
    $timestampheure = gmmktime(0,0,0,date("m"),date("d"),date("Y"));    // Timestamp courant.
    $timestampdebut = $timestampheure - $periodesecondes ;      // Recule de $periodesecondes.
    $dateformatsql = "%a %e" ;
    break;
  case "8semaines":
    $timestampdebut = gmmktime(0,0,0, date("m")-2, date("d"), date("Y"));
    $nbjour=1 ;
    while ( date("w", $timestampdebut) != 1 )  // Avance d'un jour tant que celui-ci n'est pas un lundi. 
    {
      $timestampdebut = gmmktime(0,0,0, date("m")-2, date("d")+$nbjour, date("Y"));
      $nbjour++ ;
    }
    $xlabel = "semaines" ;
    $dateformatsql = "sem %v" ;
    break;
  case "8mois":
    $timestampdebut = gmmktime(0,0,0, date("m")-7, 1, date("Y"));
    $xlabel = "mois" ;
    $dateformatsql = "%b" ;
    break;
  default:
    die("Periode erronée, valeurs possibles: [8jours|8semaines|8mois] !");
    break;
}

$query="SET lc_time_names = 'fr_FR'" ;  // Pour afficher date en français dans MySql.
mysql_query($query) ; 
$query="SELECT rec_date, DATE_FORMAT(rec_date, '$dateformatsql') AS 'periode' ,
  ROUND( ((MAX(`hchp`) - MIN(`hchp`)) / 1000) ,1 ), 
  ROUND( ((MAX(`hchc`) - MIN(`hchc`)) / 1000) ,1 )  
  FROM `$table` 
  WHERE timestamp > '$timestampdebut'
  GROUP BY periode
  ORDER BY rec_date" ; 

$result=mysql_query($query) or die ("<b>Erreur</b> dans la requète <b>" . $query . "</b> : "  . mysql_error() . " !<br>"); 
$num_rows = mysql_num_rows($result) ;
$no = 0 ;
while ($row = mysql_fetch_array($result)) 
{
  //printf("%s, %s, %s<br \>", $row["rec_date"], $row["periode"], $row[2], $row[3]) ;
  $date[$no] = $row["rec_date"] ;
  $timestp[$no] = $row["periode"] ;
  $kwhhp[$no]=floatval(str_replace(",", ".", $row[2]));
  $kwhhc[$no]=floatval(str_replace(",", ".", $row[3]));
  $no++ ;
}
$date_digits_dernier_releve=explode("-", $date[count($date) -1]) ; 
$date_dernier_releve =  Date('d/m/Y', gmmktime(0,0,0, $date_digits_dernier_releve[1] ,$date_digits_dernier_releve[2], $date_digits_dernier_releve[0])) ;

mysql_free_result($result) ;


mysql_close() ;
 

$date =  Date('d/m/Y, H:i', $timestp[count($timestp) -1]) ;
 
// Get start time
$start = $timestp[0] ;
$end = $timestp[count($timestp) -1] ;

$papp_actuelle=$x2[0][count($x2[0]) -1] + $x2[1][count($x2[1]) -1];
$iinst_actuelle=$x3[0][count($x3[0]) - 1] + 0 ;

?>

    <script type="text/javascript">
<?php
// cf. http://www.phpcs.com/codes/PHP-TO-JS-CONVERSION-VARIABLE-PHP-VERS-JAVASCRIPT_13232.aspx
function php2js ($var) {
    if (is_array($var)) {
        $res = "[";
        $array = array();
        foreach ($var as $a_var) {
            $array[] = php2js($a_var);
        }
        return "[" . join(",", $array) . "]";
    }
    elseif (is_bool($var)) {
        return $var ? "true" : "false";
    }
    elseif (is_int($var) || is_integer($var) || is_double($var) || is_float($var)) {
        return $var;
    }
    elseif ($var=="null") {
        return "" . addslashes(stripslashes($var)) . "";
    }if (is_string($var)) {
        return "\"" . addslashes(stripslashes($var)) . "\"";
    }
    // autres cas: objets, on ne les gère pas
    return FALSE;
}s

$ddannee = date("Y",$date_deb);
$ddmois = date("m",$date_deb);
$ddjour = date("d",$date_deb);
$ddheure = date("G",$date_deb); //Heure, au format 24h, sans les zéros initiaux
$ddminute = date("i",$date_deb);

$date_deb_UTC=$date_deb*1000;

$datetext = "$ddjour/$ddmois/$ddannee  $ddheure:$ddminute";
$ddmois=$ddmois-1; // nécessaire pour Date.UTC() en javascript qui a le mois de 0 à 11 !!!

$n=0;
while ($n <= $N_device) {
  
      
  echo 'var data_elec'.$n.' = '.php2js($x2[$n]).';';
  echo "\n";
  $n++;
};


echo 'var data_elec2 = '.php2js($x3[0]).';';
echo "\n";

?>          

var myDate = new <?echo "Date($ddannee, $ddmois, $ddjour, $ddheure, $ddminute, 0);";  /*Date(myYear, myMonth-1,myDay, myHour,0,0,0);*/  ?>
var timezone_delay = -myDate.getTimezoneOffset()*60*1000;
myDate = new Date(myDate.getTime() + timezone_delay);

    
      var chart_elec;
      $(document).ready(function() {
        chart_elec = new Highcharts.Chart({
          chart: {
            renderTo: 'container',
            defaultSeriesType: 'area', 
            ignoreHiddenSeries: false,
            zoomType: 'x'
          },
          title: {
            text: '<?echo "Graph du $datetext ";?>'
          },
          xAxis: {
            type: 'datetime',
            maxZoom: 1 * 3600 * 1000, // 1 heures
            title: {
              text: null
            }
          },
          yAxis: [{ // Primary yAxis
             labels: {
                formatter: function() {
                   return this.value +' w';
                }
             },
             title: {
                text: 'Watt',
                margin: 70
             }
          }, { // Secondary yAxis
             labels: {
                formatter: function() {
                   return this.value +' A';
                }
             },
             title: {
                text: 'Ampère',
                margin: 100
             },
             opposite: true,
             min: 0
          }],
          tooltip: {
            formatter: function() {
                return ''+
                Highcharts.dateFormat('%e. %b %Y, %H:%M', this.x) +'<br/><b>'+ this.y +
                (this.series.name == '<?echo "$courbe_titre[2] / min $courbe_min[2] max $courbe_max[2]";?>' ? ' A' : ' W')+'<b>';
            }
          },
          plotOptions: {
            area: {
              lineWidth: 1,
              states: {
                hover: {
                  lineWidth: 1
                }
              },
              marker: {
                enabled: false,
                states: {
                  hover: {
                    enabled: true,
                    symbol: 'circle',
                    radius: 2,
                    lineWidth: 1
                  }
                }  
              },
              pointInterval: 60000, // 1 minute
              pointStart: <? echo $date_deb_UTC; ?>
            },
            spline: {
              lineWidth: 1,
              states: {
                hover: {
                  lineWidth: 2
                }
              },
              marker: {
                enabled: false,
                states: {
                  hover: {
                    enabled: true,
                    symbol: 'circle',
                    radius: 2,
                    lineWidth: 1
                  }
                }  
              },
              pointInterval: 60000, // 1 minute
              pointStart: <? echo $date_deb_UTC; ?>
            }
          },
          series: [
<?
$n=0;
while ($n <= $N_device) {  
  
  echo "{ name : '".$courbe_titre[$n]." / min ".$courbe_min[$n]." max ".$courbe_max[$n]."',";
  echo "pointInterval: 60 * 1000,";
  echo "pointStart: $date_deb_UTC,";
  echo ' data : data_elec'.$n.'},';
  echo "\n";
  $n++;
}

echo "{ name : '".$courbe_titre[2]." / min ".$courbe_min[2]." max ".$courbe_max[2]."',";
echo "type: 'spline',";
echo "yAxis: 1,";
echo "pointInterval: 60 * 1000,";
echo "pointStart: $date_deb_UTC,";
echo ' data : data_elec2 },';
echo "\n";

?>          
          ]
          ,
          subtitle: {
             text: '<? echo "Puissance actuelle: $papp_actuelle Watts --- Itensitée actuelle: $iinst_actuelle A";?>',
             align: 'left',
             x: 10,
             y: 400
          },
          credits: {
            enabled: false
          },
          navigation: {
            menuItemStyle: {
              fontSize: '10px'
            }
          }
        });
        
        
      });
      
<?

      
  echo 'var datakwhhp = '.php2js($kwhhp).';';
  echo "\n";
  echo 'var datakwhhc = '.php2js($kwhhc).';';
  echo "\n";
  echo 'var datatimestp = '.php2js($timestp).';';
  echo "\n";

?>          

 var prixHP = 0.1235;
 var totalHP = 0;
 var prixHC = 0.0784;
 var totalHC = 0;
 var totalprix = 0;

    
      var chart_elec4;
      $(document).ready(function() {
        chart_elec4 = new Highcharts.Chart({
          chart: {
            renderTo: 'container4',
            defaultSeriesType: 'column', 
            ignoreHiddenSeries: false,
          },
          title: {
            text: 'Consomation sur 8 <?echo $xlabel;?>'
          },
      xAxis: [{
         categories: datatimestp
      }],                                           
          yAxis: {
            title: {
              text: 'Kwh',
              margin: 60
            },
            min: 0,
            minorGridLineWidth: 0,
            labels: { formatter: function() { return this.value +' Kwh' } }
          },
          tooltip: {
            formatter: function() {
                totalHP=Highcharts.numberFormat((prixHP*Highcharts.numberFormat((this.series.name == 'Heures Pleines' ? this.y :this.point.stackTotal-this.y), 2)),2);
                totalHC=Highcharts.numberFormat((prixHC*Highcharts.numberFormat((this.series.name == 'Heures Creuses' ? this.y :this.point.stackTotal-this.y), 2)),2);
                totalprix=Highcharts.numberFormat(parseFloat(totalHP)+parseFloat(totalHC),2);
                return '<b> '+ this.x +' <b><br/><b>'+ this.series.name +' '+ Highcharts.numberFormat(this.y, 2) +' Kwh <b><br/>'+
                      ' HP :'+ totalHP +' Euro / HC :'+ totalHC +' Euro <br/>'+
                      '<b> Total: '+ totalprix +' Euro<b>';
            }
          },
          plotOptions: {
            column: {
              stacking: 'normal',
            }
          },
          series: [
            { name : 'Heures Pleines',
             data : datakwhhp ,
            
            dataLabels: {
                        enabled: true,
                        color: '#FFFFFF',
                        y: 13,
                        formatter: function() {
                           return this.y;
                        },
                        style: {
                           font: 'normal 13px Verdana, sans-serif'
                        }
                     },
            type: 'column'
            },
            { name : 'Heures Creuses',
             data : datakwhhc ,
            
            dataLabels: {
                        enabled: true,
                        color: '#FFFFFF',
                        y: 13,
                        formatter: function() {
                           return this.y;
                        },
                        style: {
                           font: 'normal 13px Verdana, sans-serif'
                        }
                     }
            
            }
          ]
          ,
          credits: {
            enabled: false
          },
          navigation: {
            menuItemStyle: {
              fontSize: '10px'
            }
          }
        });
        
        
      });

    </script>
    
  </head>
  <body>
    
    <div id="container" style="width: 800px; height: 400px; margin: 0 auto"></div>
    <br />
    <div id="container4" style="width: 800px; height: 400px; margin: 0 auto"></div>
        
  </body>
</html>
