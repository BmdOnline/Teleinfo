<?php
setlocale(LC_ALL , "fr_FR" );
date_default_timezone_set("Europe/Paris");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta content="no-cache" http-equiv="Pragma">

    <link rel="stylesheet" href="./css/themes/ui-lightness/jquery.ui.all.css">
    <link rel="shortcut icon" href="./favicon.ico">

    <script type="text/javascript" src="./js/jquery-1.6.2.min.js"></script>
    <script type="text/javascript" src="./js/jquery-ui-1.8.5.custom.min.js"></script>

    <script type="text/javascript" src="./js/highcharts.js"></script>
    <script type="text/javascript" src="./js/highstock.js"></script>

    <script type='text/javascript' src="teleinfo.js"></script>

    <title>graph conso électrique</title>

<style type="text/css">
button[class="button_chart1"],
button[class="button_chart2"]
{
width:120px;
height:30px;
margin-left:20px;
font-size:1em;
font-weight:bold;
border:none;
color:#CECECE;
text-shadow:0px -1px 0px #000;
background:#1f2026;
background:-moz-linear-gradient(top,#1f2026,#15161a);
background:-webkit-gradient(linear,left top,left bottom,from(#1f2026),to(#15161a));
-webkit-border-radius:5px;
   -moz-border-radius:5px;
        border-radius:5px;
-webkit-box-shadow:0px 0px 1px #000;
   -moz-box-shadow:0px 0px 1px #000;
        box-shadow:0px 0px 1px #000;
}
button[class="button_chart1"]:hover,
button[class="button_chart2"]:hover
{
background:#343640;
background:-moz-linear-gradient(top,#343640,#15161a);
background:-webkit-gradient(linear,left top,left bottom,from(#343640),to(#15161a));
color:#FFFFFF;
}
</style>

  </head>
  <body>
    <div style="text-align: center;">
        <button class="button_chart1" value="1prec">- 24h</button>
        <button class="button_chart1" value="now">Aujourd'hui</button>
        <button class="button_chart1" value="1suiv">+ 24h</button>
    </div>
    <br />
    <div id="chart1" style="width: 800px; height: 500px; margin: 0 auto"></div>
    <br /><br />
    
    <div style="text-align: center;">
        <button class="button_chart2" value="8jours">8 jours</button>
        <button class="button_chart2" value="8semaines">8 semaines</button>
        <button class="button_chart2" value="8mois">8 mois</button>
        <button class="button_chart2" value="1an">1 an</button>
    </div>
    <br />
    <div id="chart2legende" style="text-align: center;" >Coût sur la période ...</div>
    <br />
    <div id="chart2" style="width: 800px; height: 400px; margin: 0 auto"></div>

  </body>
</html>
