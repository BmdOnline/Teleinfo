
var start = new Date();

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

var totalBASE = 0;
var totalHP = 0;
var totalHC = 0;
var totalprix = 0;

var chart_elec1;
var chart_elec2;

$(document).ready(function() {
  $.getJSON('json.php?query=daily', function(data) {
    // Create the chart
    chart_elec1 = new Highcharts.StockChart(init_chart1(data));
  });

  $.getJSON('json.php?query=history', function(data) {
    // Create the chart
    chart_elec2 = new Highcharts.Chart(init_chart2(data));
  });

  function init_chart1(data) {
    return {
      chart: {
        renderTo: 'chart1',
        events: {
          load: function(chart) {
            this.setTitle(null, {
              text: 'Construit en '+ (new Date() - start) +'ms'
            });
            if ($('#chart1legende').length) {
              $("#chart1legende").html(data.subtitle);
            }
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
          name : data.HP_name,
          data : data.HP_data,
          id: 'HP',
          type : 'areaspline',
          threshold : null,
          tooltip : {
              yDecimals : 0
          },
          showInLegend: ((data.tarif_type == "HCHP")?true:false)
      }, {
          name : data.HC_name,
          data : data.HC_data,
          id: 'HC',
          type : 'areaspline',
          threshold : null,
          tooltip : {
              yDecimals : 0
          },
          showInLegend: ((data.tarif_type == "HCHP")?true:false)
      },/*{
          name : data.I_name,
          data: data.I_data,
          type: 'spline',
          width : 1,
          shape: 'squarepin'
      },*/{
          name : data.JPrec_name,
          data: data.JPrec_data,
          type: 'spline',
          width : 1,
          shape: 'squarepin',
          tooltip : {
              yDecimals : 0
          }
      }, {
          name : data.BASE_name,
          data : data.BASE_data,
          id: 'BASE',
          type : 'areaspline',
          threshold : null,
          tooltip : {
              yDecimals : 0
          },
          showInLegend: ((data.tarif_type == "HCHP")?false:true)
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
        events: {
          load: function(chart) {
            this.setTitle(null, {
              text: 'Construit en '+ (new Date() - start) +'ms'
            });
            if ($('#chart2legende').length) {
              $("#chart2legende").html(data.subtitle);
            }
          }
        },
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
          totalBASE=data.prix.BASE*((this.series.name == 'Heures de Base')? this.y :this.point.stackTotal-this.y);
          totalHP=data.prix.HP*((this.series.name == 'Heures Pleines')? this.y :this.point.stackTotal-this.y);
          totalHC=data.prix.HC*((this.series.name == 'Heures Creuses')? this.y :this.point.stackTotal-this.y);
          totalprix=Highcharts.numberFormat(( totalBASE + totalHP + totalHC + data.prix.abonnement ),2);
          tooltip = '<b> '+ this.x +' <b><br /><b>'+ this.series.name +' '+ Highcharts.numberFormat(this.y, 2) +' kWh<b><br />';
          //tooltip += 'BASE : '+ Highcharts.numberFormat(totalBASE,2) + ' Euro / HP : '+ Highcharts.numberFormat(totalHP,2) + ' Euro / HC : ' + Highcharts.numberFormat(totalHC,2) + ' Euro<br />';
          if (data.tarif_type != "HCHP") {
            tooltip += 'BASE : '+ Highcharts.numberFormat(totalBASE,2) + ' Euro <br />';
          } else {
            tooltip += 'HP : '+ Highcharts.numberFormat(totalHP,2) + ' Euro / HC : ' + Highcharts.numberFormat(totalHC,2) + ' Euro<br />';
          }
          tooltip += 'Abonnement sur la période : '+ Highcharts.numberFormat(data.prix.abonnement,2) +' Euro<br />';
          tooltip += '<b> Total: '+ totalprix +' Euro<b>';
          return tooltip;
        }
      },
      plotOptions: {
        column: {
          stacking: 'normal',
        }
      },
      series: [{
        name : data.HP_name,
        data : data.HP_data,
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
        type: 'column',
        showInLegend: ((data.tarif_type == "HCHP")?true:false)
      }, {
        name : data.HC_name,
        data : data.HC_data,
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
        type: 'column',
        showInLegend: ((data.tarif_type == "HCHP")?true:false)
      }, {
        name : data.BASE_name,
        data : data.BASE_data,
        events: {
          click: function(e) {
            var newdate = new Date();
            newdate.setTime (data.debut);
            newdate.setDate(newdate.getDate()+e.point.x);
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
        type: 'column',
        showInLegend: ((data.tarif_type == "HCHP")?false:true)
      }],
      navigation: {
        menuItemStyle: {
          fontSize: '10px'
        }
      }
    }
  }

  function refresh_chart1(date) {
    // remise à zéro du chronomètre
    start = new Date();

    // on relance la requête historique
    $.getJSON('json.php?query=daily&date='+parseInt(date.getTime()/1000), function(data) {
      // Remplacement du graphique
      chart_elec1= new Highcharts.StockChart(init_chart1(data));
    });
  }
  
  function refresh_chart2(periode) {
    // remise à zéro du chronomètre
    start = new Date();

    // on relance la requête historique
    $.getJSON('json.php?query=history&periode='+periode, function(data) {
      // Remplacement du graphique
      chart_elec2 = new Highcharts.Chart(init_chart2(data));
    });
  }
  
  $('.button_chart1').click(function() {
    curdate = chart_elec1.series[0].xData[chart_elec1.series[0].xData.length-1];
    var newdate = new Date();
    newdate.setTime (curdate);
    switch (this.value)
    {
      case "1prec":
        newdate.setDate(newdate.getDate()-1);
        break;
      case "1suiv":
        newdate.setDate(newdate.getDate()+1);
        break;
      case "now":
        newdate = new Date();
        break;
    }

    refresh_chart1(newdate);
  });

  $('.button_chart2').click(function() {
    refresh_chart2(this.value);
  });

});
