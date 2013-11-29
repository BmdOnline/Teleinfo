<?php
setlocale(LC_ALL , "fr_FR" );
date_default_timezone_set("Europe/Paris");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta content="no-cache" http-equiv="Pragma">

    <link rel="shortcut icon" href="./favicon.ico">

    <!--
      jquery-ui :
      recompilé avec selectmenu, à l'aide de :
          node.js (nvm / npm) & grunt
              sudo apt-get install npm
              sudo npm install grunt -g
              git clone https://github.com/jquery/jquery-ui.git --branch selectmenu
              cd jquery-ui
              npm install
              grunt build
    -->

    <link rel="stylesheet" href="./css/smoothness/jquery-ui-1.10.1pre.selectmenu.min.css">

    <script type="text/javascript" src="./js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="./js/jquery-ui-1.10.1pre.selectmenu.min.js"></script>

    <!-- Attention à l'ordre des déclarations -->
    <script type="text/javascript" src="./js/highcharts.js"></script>
    <script type="text/javascript" src="./js/highstock.js"></script>
    <script type="text/javascript" src="./js/highcharts-more.js"></script>

    <link rel="stylesheet" href="teleinfo.css">
    <script type='text/javascript' src="teleinfo.js"></script>

    <title>graph conso électrique</title>
</head>

<body>
  <div class="container">
    <div> <!-- <div style="float: left;"> <!-- Chart0 -->
      <div style="text-align: center;">
          <button class="button_chart0" id="chart0_refresh" value="now">Rafraichir</button>
      </div>
      <br />
      <div id="chart0" style="width: 300px; height: 300px; margin: 0 auto"></div>
      <br /><br />
    </div>

    <!--<div stype="clear:both; margin: 2em 0;" />-->

    <div> <!-- <div style="float: right;"> <!-- Chart1 -->
      <div style="text-align: center;">
          <button class="button_chart1" id="chart1_date_prec" value="1prec">&laquo;&nbsp;- 24h</button>
          <button class="button_chart1" id="chart1_date_now" value="now">Aujourd'hui</button>
          <button class="button_chart1" id="chart1_date_suiv" value="1suiv">+ 24h&nbsp;&raquo;</button>
      </div>
      <br />
      <div id="chart1" style="width: 800px; height: 500px; margin: 0 auto"></div>
      <br /><br />
    </div>

    <!--<div stype="clear:both; margin: 2em 0;" />-->

    <div> <!-- <div style="float: none;"> <!-- Chart2 -->
      <div style="text-align: center;">
          <select class="select_chart2" id="duree">
              <option value="1">1</option>
              <option value="8">8</option>
              <option value="14">14</option>
          </select>
          <select class="select_chart2" id="periode">
              <option value="jours">Jour(s)</option>
              <option value="semaines">Semaine(s)</option>
              <option value="mois">Mois(s)</option>
              <option value="ans">An(s)</option>
          </select>
    
          <br />
          <button class="button_chart2" id="chart2_date_prec" value="1prec">&laquo;</button>
          <button class="button_chart2" id="chart2_date_now" value="now">Aujourd'hui</button>
          <button class="button_chart2" id="chart2_date_suiv" value="1suiv">&raquo;</button>
      </div>
      <br />
      <div id="chart2legende" style="text-align: center;" >Coût sur la période ...</div>
      <br />
      <div id="chart2" style="width: 800px; height: 400px; margin: 0 auto"></div>
      <br /><br />
    </div>
  </div>
</body>
</html>
