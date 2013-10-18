
var start = new Date();

var totalBASE = 0;
var totalHP = 0;
var totalHC = 0;
var totalprix = 0;

var chart_elec0;
var chart_elec1;
var chart_elec2;

jQuery(function($) {
  Highcharts.setOptions({
    lang: {
      months: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
        'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
      weekdays: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
      decimalPoint: ',',
      thousandsSep: '.',
      rangeSelectorFrom: 'Du',
      rangeSelectorTo: 'au'
    },
    legend: {
      enabled: false
    },
    global: {
      useUTC: false
    }
  });
});

function init_chart0(data) {
  return {
    chart: {
      renderTo: 'chart0',
      type: 'gauge',
      plotBackgroundColor: null,
      plotBackgroundImage: null,
      plotBorderWidth: 0,
      plotShadow: false,
      events: {
        load: function(chart) {
          this.setTitle(null, {
            text: 'Construit en '+ (new Date() - start) +'ms'
          });
          if ($('#chart0legende').length) {
            $("#chart0legende").html(data.subtitle);
          }
          this.debut=data.debut;
          //init_chart0_navigation(data.duree, data.periode);
        }
      },
      borderColor: '#EBBA95',
      borderWidth: 2,
      borderRadius: 10,
    },
    title: {
      text: 'Puissance instantanée'
    },
    credits: {
      enabled: false
    },
    pane: {
      startAngle: -150,
      endAngle: 150,
      background: [{
        backgroundColor: {
          linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
          stops: [
            [0, '#FFF'],
            [1, '#333']
          ]
        },
        borderWidth: 0,
        outerRadius: '109%'
      }, {
        backgroundColor: {
          linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
          stops: [
            [0, '#333'],
            [1, '#FFF']
          ]
        },
        borderWidth: 1,
        outerRadius: '107%'
      }, {
        // default background
      }, {
        backgroundColor: '#DDD',
        borderWidth: 0,
        outerRadius: '105%',
        innerRadius: '103%'
      }]
    },

    // the value axis
    yAxis: {
      min: 0,
      max: 10000,
      
      minorTickInterval: 'auto',
      minorTickWidth: 1,
      minorTickLength: 10,
      minorTickPosition: 'inside',
      minorTickColor: '#666',

      tickPixelInterval: 30,
      tickWidth: 2,
      tickPosition: 'inside',
      tickLength: 10,
      tickColor: '#666',
      labels: {
        step: 4,
        rotation: 'auto'
      },
      title: {
        text: 'Watts'
      },
      plotBands: [{
        from: 0,
        to: 300,
        color: '#55BF3B' // green
      }, {
        from: 300,
        to: 1000,
        color: '#DDDF0D' // yellow
      }, {
        from: 1000,
        to: 3000,
        color: '#FFA500' // orange
      }, {
        from: 3000,
        to: 10000,
        color: '#DF5353' // red
      }]        
    },

    tooltip: {
      formatter: function() {
          //puissance=data.W_data;
          //fraicheur=data.date;
          tooltip = '<b>' + Highcharts.numberFormat(this.y, 0) +' Watts</b><br />';
          tooltip += 'Le '+  Highcharts.dateFormat('%A %e %B %Y à %H:%M', data.debut) +'<br />';
          return tooltip;
        }
    },
        
    series: [{
      name : data.W_name,
      data : [data.W_data],
      /*tooltip: {
        valueSuffix: ' Watts'
      }*/
    }],
  };
}

function init_chart1(data) {
  return {
    chart: {
      renderTo: 'chart1',
      animation: false,
      events: {
        load: function(chart) {
          this.setTitle(null, {
            text: 'Construit en '+ (new Date() - start) +'ms'
          });
          if ($('#chart1legende').length) {
            $("#chart1legende").html(data.subtitle);
          }
          this.debut=data.debut;
          init_chart1_navigation(data.duree, data.periode);
        }
      },
      borderColor: '#EBBA95',
      borderWidth: 2,
      borderRadius: 10,
      ignoreHiddenSeries: false
    },
    credits: {
      enabled: false
    },
    title: {
      text : data.title
    },
    subtitle: {
      text: 'Construit en...'
    },
    rangeSelector : {
      buttons : [{
        type : 'hour',
        count : 1,
        text : '1h'
      },{
        type : 'hour',
        count : 3,
        text : '3h'
      },{
        type : 'hour',
        count : 6,
        text : '6h'
      },{
        type : 'hour',
        count : 9,
        text : '9h'
      },{
        type : 'hour',
        count : 12,
        text : '12h'
      },{
        type : 'all',
        count : 1,
        text : 'All'
      }],
      selected : 5,
      inputEnabled : false
    },
    xAxis: {
      type: 'datetime',
       dateTimeLabelFormats: {
          hour: '%H:%M',
          day: '%H:%M',
          week: '%H:%M',
          month: '%H:%M'
       }
    },
    yAxis: [{
      labels: {
        formatter: function() {
           return this.value +' w';
        }
      },
      title: {
        text: 'Watt'
      },
      lineWidth: 2,
      showLastLabel: true,
      min: 0,
      alternateGridColor: '#FDFFD5',
      minorGridLineWidth: 0,
      plotLines : [{ // lignes min et max
        value : data.seuils.min,
        color : 'green',
        dashStyle : 'shortdash',
        width : 2,
        label : {
          text : 'minimum ' + data.seuils.min + 'w'
        }
      }, {
        value : data.seuils.max,
        color : 'red',
        dashStyle : 'shortdash',
        width : 2,
        label : {
          text : 'maximum ' + data.seuils.max + 'w'
        }
      }]
    }],

    series : [{
        name : data.BASE_name,
        data : data.BASE_data,
        id: 'BASE',
        type : 'areaspline',
        threshold : null,
        tooltip : {
            yDecimals : 0
        }
    }, {
        name : data.HP_name,
        data : data.HP_data,
        id: 'HP',
        type : 'areaspline',
        threshold : null,
        tooltip : {
            yDecimals : 0
        }
    }, {
        name : data.HC_name,
        data : data.HC_data,
        id: 'HC',
        type : 'areaspline',
        threshold : null,
        tooltip : {
            yDecimals : 0
        }
    },
    /*{
        name : data.I_name,
        data: data.I_data,
        type: 'spline',
        width : 1,
        shape: 'squarepin'
    },*/
    {
        name : data.JPrec_name,
        data: data.JPrec_data,
        type: 'spline',
        width : 1,
        shape: 'squarepin',
        tooltip : {
            yDecimals : 0
        }
    }],
    legend: {
      enabled: true,
      borderColor: 'black',
      borderWidth: 1,
      shadow: true
    },
    navigator: {
      baseSeries: 2,
      top: 390,
      menuItemStyle: {
        fontSize: '10px'
      },
      series: {
        name: 'navigator',
        data: data.navigator
      }
    },
    scrollbar: { // scrollbar "stylée" grise
      barBackgroundColor: 'gray',
      barBorderRadius: 7,
      barBorderWidth: 0,
      buttonBackgroundColor: 'gray',
      buttonBorderWidth: 0,
      buttonBorderRadius: 7,
      trackBackgroundColor: 'none',
      trackBorderWidth: 1,
      trackBorderRadius: 8,
      trackBorderColor: '#CCC'
    },
  }
}

function init_chart2(data) {
  return {
    chart: {
      renderTo: 'chart2',
      animation: false,
      events: {
        load: function(chart) {
          this.setTitle(null, {
            text: 'Construit en '+ (new Date() - start) +'ms'
            //text: data.subtitle
          });
          if ($('#chart2legende').length) {
            $("#chart2legende").html(data.subtitle);
          }
          this.debut=data.debut;
          init_chart2_navigation(data.duree, data.periode);
        }
      },
      borderColor: '#EBBA95',
      borderWidth: 2,
      borderRadius: 10,
      defaultSeriesType: 'column',
      ignoreHiddenSeries: false
    },
    credits: {
      enabled: false
    },
    title: {
      text : data.title
    },
    subtitle: {
      text: 'Construit en...'
    },
    xAxis: [{
       categories: data.categories
    }],
    yAxis: {
      title: {
        text: 'kWh'
      },
      min: 0,
      minorGridLineWidth: 0,
      labels: { formatter: function() { return this.value +' kWh' } }
    },
    tooltip: {
      formatter: function() {
        if (this.series.name == 'Période Précédente') {
          tooltip = '<b>'+ this.series.name +'</b><br />';
          tooltip += '<b>'+ this.key + '</b><br />';
          tooltip += 'Total: '+ Highcharts.numberFormat(this.y, 2) +' kWh<br />';
        }
        else {
          totalBASE=data.prix.BASE*((this.series.name == 'Heures de Base')? this.y :this.point.stackTotal-this.y);
          totalHP=data.prix.HP*((this.series.name == 'Heures Pleines')? this.y :this.point.stackTotal-this.y);
          totalHC=data.prix.HC*((this.series.name == 'Heures Creuses')? this.y :this.point.stackTotal-this.y);
          totalprix=Highcharts.numberFormat(( totalBASE + totalHP + totalHC + data.prix.abonnement ),2);
          tooltip = '<b> '+ this.x +' </b><br /><b>'+ this.series.name +' '+ Highcharts.numberFormat(this.y, 2) +' kWh</b><br />';
          tooltip += 'BASE : '+ Highcharts.numberFormat(totalBASE,2) + ' Euro / HP : '+ Highcharts.numberFormat(totalHP,2) + ' Euro / HC : ' + Highcharts.numberFormat(totalHC,2) + ' Euro<br />';
          tooltip += 'Abonnement sur la période : '+ Highcharts.numberFormat(data.prix.abonnement,2) +' Euro<br />';
          tooltip += '<b> Total: '+ totalprix +' Euro</b>';
        }
        return tooltip;
      }
    },
    plotOptions: {
      column: {
        stacking: 'normal',
      }
    },
    series: [{
      name : data.BASE_name,
      data : data.BASE_data,
      stack : 'hphc',
      events: {
        click: function(e) {
          //console.log (e.point.series.name);
          //console.log (e.point.category);
          //console.log (e.point.x);
          //console.log (Highcharts.dateFormat('%A, %b %e, %Y', data.debut));
          //console.log (data.debut);
          var newdate = new Date();
          newdate.setTime (data.debut);
          newdate.setDate(newdate.getDate()+e.point.x);
          console.log(newdate);
        }
      },
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
    }, {
      name : data.HP_name,
      data : data.HP_data,
      stack : 'hphc',
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
    }, {
      name : data.HC_name,
      data : data.HC_data,
      stack : 'hphc',
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
    }, {
      name : data.PREC_name,
      data : data.PREC_data,
      /*stack : 'prec',*/
      type: 'spline',
      /*type: 'scatter',
      width : 1,
      color : 'red',
      marker: {
          symbol: 'triangle-down',
          radius: 8,
          fillColor: null,
          lineWidth: 1,
          lineColor: null // inherit from series
      },*/
      /*threshold : null,
      tooltip : {
          yDecimals : 0
      }*/
    }
    ],
    navigation: {
      menuItemStyle: {
        fontSize: '10px'
      }
    }
  }
}

function init_chart1_navigation() {
  // Libelles des boutons
  $(".button_chart1#chart1_date_prec").html("&laquo;&nbsp;- 24h");
  $(".button_chart1#chart1_date_now").html("Aujourd'hui");
  $(".button_chart1#chart1_date_suiv").html("+ 24h&nbsp;&raquo;");
}

function init_chart2_navigation(duree, periode) {
  switch (periode) {
    case ("jours"):
      var arrayDuree = new Array();
      for( i=1; i <= 31; i++){
        arrayDuree[i] = i;
      }
      txtdecalage="1 jour";
      break;
    case ("semaines"):
      /*var arrayDuree = {
        '2' : '2', '4' : '4', '5' : '5', '10' : '10'
      };*/
      var arrayDuree = new Array();
      for( i=1; i <= 52; i++){
        arrayDuree[i] = i;
      }
      txtdecalage="1 sem.";
      break;
    case ("mois"):
      var arrayDuree = new Array();
      for( i=1; i <= 12; i++){
        arrayDuree[i] = i;
      }
      txtdecalage="1 mois";
      break;
    case ("ans"):
      var arrayDuree = new Array();
      for( i=1; i <= 4; i++){
        arrayDuree[i] = i;
      }
      txtdecalage="1 mois";
      break;
    default:
  }

  // Met à jour la liste déroulante "duree"
  var select = $('.select_chart2#duree');
  if(select.prop) {
    var options = select.prop('options');
  }
  else {
    var options = select.attr('options');
  }
  $('option', select).remove();
  $.each(arrayDuree, function(val, text) {
    if (val>0) {
      options[options.length] = new Option(text, val);
    };
  });

  // Valeurs par défaut
  $(".select_chart2#duree").val(duree);
  $('.select_chart2#duree').selectmenu('refresh', true);
  //$(".select_chart2#duree").refresh;
  $(".select_chart2#periode").val(periode);
  $('.select_chart2#periode').selectmenu('refresh', true);
  //$(".select_chart2#periode").refresh;
  
  // Libelles des boutons
  $(".button_chart2#chart2_date_prec").html("&laquo;&nbsp;- " + txtdecalage);
  $(".button_chart2#chart2_date_now").html("Aujourd'hui");
  $(".button_chart2#chart2_date_suiv").html("+ " + txtdecalage + "&nbsp;&raquo;");
}

function refresh_chart0(date) {
  //console.log (date);

  // remise à zéro du chronomètre
  start = new Date();

  // on relance la requête instantly
  $.getJSON('json.php?query=instantly', function(data) {
    // Remplacement du graphique
    chart_elec0= new Highcharts.Chart(init_chart0(data));
  });
}

function refresh_chart1(date) {
  //console.log (date);

  // remise à zéro du chronomètre
  start = new Date();

  // on relance la requête daily
  parameters=(date?"&date="+date.getTime()/1000:"");
  $.getJSON('json.php?query=daily'+parameters, function(data) {
    // Remplacement du graphique
    chart_elec1= new Highcharts.StockChart(init_chart1(data));
  });
}

function refresh_chart2(duree, periode, date) {
  //console.log (periode);

  // remise à zéro du chronomètre
  start = new Date();

  // on relance la requête historique
  parameters=(duree?"&duree="+duree:"")+(periode?"&periode="+periode:"")+(date?"&date="+date.getTime()/1000:"");
  $.getJSON('json.php?query=history'+parameters, function(data) {
    // Remplacement du graphique
    chart_elec2 = new Highcharts.Chart(init_chart2(data));
  });
}

function process_chart0_button(object) {
  refresh_chart0();
}

function process_chart1_button(object) {
  //curdate = this.chart_elec1.series[0].xData[this.chart_elec1.series[0].xData.length-1];
  curdate = this.chart_elec1.debut;
  var newdate = new Date();
  newdate.setTime (curdate);
  switch (object.value)
  {
    case "1prec":
      newdate.setDate(newdate.getDate()-1);
      break;
    case "1suiv":
      newdate.setDate(newdate.getDate()+1);
      break;
    case "now":
      newdate = null;
      break;
  }

  refresh_chart1(newdate);
}

function process_chart2_button(object) {
  periode = $(".select_chart2#periode").val();
  duree = $(".select_chart2#duree").val();
  curdate = this.chart_elec2.debut;
  var newdate = new Date();
  newdate.setTime (curdate);
  // Teste si on doit changer de date
  //if (object.id.substring(0,4) == "date") {
  if (1==1) {
      // Type de changement de date
      switch (object.value)
      {
        case "1prec":
          // on recule
          coefdate=-1;
          break;
        case "1suiv":
          // on avance
          coefdate=1;
          break;
        case "now":
          // retour à aujourd'hui
          newdate = null;
          break;
        default:
          // on ne change rien
          coefdate=0;
      }
      // Calcul du décalage de date
      if (newdate) {
        switch (periode) {
        case ("jours"):
          // décalage d'un jour
          newdate.setDate(newdate.getDate()+1*coefdate);
          break;
        case ("semaines"):
          // décalage d'une semaine
          newdate.setDate(newdate.getDate()+7*coefdate);
          break;
        case ("mois"):
          // décalage d'un mois
          newdate.setMonth(newdate.getMonth()+1*coefdate);
          break;
        case ("an"):
          // décalage d'un mois
          newdate.setMonth(newdate.getMonth()+1*coefdate);
          break;
        default:
          // on ne change rien
          newdate.setDate(newdate.getDate());
        }
      }
  }
  else {
    newdate=null;
  }

  refresh_chart2(duree, periode, newdate);
};
 
function process_chart2_select(e, object){
  periode = $(".select_chart2#periode").val();
  duree = $(".select_chart2#duree").val();
  curdate = this.chart_elec2.debut;
  var newdate = new Date();
  newdate.setTime (curdate);

  // Teste si on doit changer de période
  if (this.id == "periode") {
    periode = object.item.value;
  }
  else if (this.id == "duree") {
    duree = object.item.value;
  }
  else {
  }

  refresh_chart2(duree, periode, newdate);
}
    
$(document).ready(function() {
  // Initialisation jQueryUI button & selectmenu
  $('.button_chart0').button();
  $('.button_chart1').button();
  $('.button_chart2').button();
  $('.select_chart2').selectmenu();

  // Options selectmenu
  $('.select_chart2').selectmenu({
    dropdown: false,
  });

  // Overflow : permet de limiter la hauteur des listes déroulantes (via css)
  $('.select_chart2').selectmenu("menuWidget").addClass("overflow");

  // Evènements boutons (click) et selectmenu (change)
  $('.button_chart0').click(
    function(){process_chart0_button(this)}
  );
  $('.button_chart1').click(
    function(){process_chart1_button(this)}
  );
  $('.button_chart2').click(
    function(){process_chart2_button(this)}
  );
  $('.select_chart2').selectmenu({
    change: function (e, object){process_chart2_select(e, object)}
  });

  // Sablier durant les requêtes AJAX (style CSS à définir)
  $("html").bind("ajaxStart", function(){
    // Ajoute la classe CCS 'busy'
    $(this).addClass('busy');
    // Désactive les éléments de navigation
    $('.button_chart0').button("option", "disabled", true);
    $('.button_chart1').button("option", "disabled", true);
    $('.select_chart2').selectmenu("option", "disabled", true);
    $('.button_chart2').button("option", "disabled", true);
    //$('.button_chart1').attr("disabled", true);
    //$('.select_chart2').selectmenu('refresh', true);
  }).bind("ajaxStop", function(){
    // Supprime la classe CCS 'busy'
    $(this).removeClass('busy');
    // Active les éléments de navigation
    $('.button_chart0').button("option", "disabled", false);
    $('.button_chart1').button("option", "disabled", false);
    $('.select_chart2').selectmenu("option", "disabled", false);
    $('.button_chart2').button("option", "disabled", false);
    //$('.button_chart1').attr("disabled", false);
    //$('.select_chart2').selectmenu('refresh', false);
  });

  // Crée le graphique 0 (instantly)
  $.getJSON('json.php?query=instantly', function(data) {
    chart_elec0 = new Highcharts.Chart(init_chart0(data));
  });

  // Crée le graphique 1 (daily)
  $.getJSON('json.php?query=daily', function(data) {
    chart_elec1 = new Highcharts.StockChart(init_chart1(data));
  });

  // Crée le graphique 2 (history)
  $.getJSON('json.php?query=history', function(data) {
    chart_elec2 = new Highcharts.Chart(init_chart2(data));
  });

});
