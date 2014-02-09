// JSLint options
/*global console:false, document:false, $:false, jQuery:false, Highcharts:false, setInterval:false, clearInterval:false, Option:false*/
/*jslint todo:true, vars:true*/

var start = {}; // = new Date();

var animation = true;

var chart_elec0;
var chart_elec1;
var chart_elec2;

var timerID;
var chart_elec0_delay = 60; // secondes

function enable_timer(func, delay) {
    "use strict";

    //console.log("enable timer");
    timerID = setInterval(func, delay);
}

function disable_timer() {
    "use strict";

    //console.log("disable timer");
    clearInterval(timerID);
}

jQuery(function ($) {
    "use strict";

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

function init_chart0_navigation() {
    "use strict";

    // Date du calendrier
    var curDate = new Date();
    curDate.setTime($("#chart0").highcharts().debut);
    curDate.setDate(curDate.getDate() - 1); // -1 Jour
    $("#chart0_date").val(curDate.getTime());

}

function init_chart0(data) {
    "use strict";

    return {
        chart: {
            renderTo: 'chart0',
            animation: animation,
            type: 'gauge',
            plotBackgroundColor: null,
            plotBackgroundImage: null,
            plotBorderWidth: 0,
            plotShadow: false,
            events: {
                load: function (chart) {
                    this.setTitle(null, {
                        text: 'Construit en ' + (new Date() - start) + 'ms'
                    });
                    if ($('#chart0_legende').length) {
                        if (data.subtitle.length > 0) $('#chart0_legende').show();
                        $("#chart0_legende").html(data.subtitle);
                    }
                    this.debut = data.debut;
                    init_chart0_navigation();
                }
            },
            borderColor: '#EBBA95',
            borderWidth: 2,
            borderRadius: 10
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
            min: data.W_seuils.min,
            max: data.W_seuils.max,

            minorTickInterval: 'auto',
            minorTickWidth: 1,
            minorTickLength: 10,
            minorTickPosition: 'inside',
            minorTickColor: '#999',

            tickPixelInterval: 50,
            tickWidth: 2,
            tickPosition: 'inside',
            tickLength: 15,
            tickColor: '#666',
            labels: {
                step: 2,
                rotation: 'auto'
            },
            title: {
                text: 'Watts'
            },
            plotBands: [{
                from: Math.min(0, data.W_seuils.max),
                to: Math.min(300, data.W_seuils.max),
                color: '#55BF3B' // green
            }, {
                from: Math.min(300, data.W_seuils.max),
                to: Math.min(1000, data.W_seuils.max),
                color: '#DDDF0D' // yellow
            }, {
                from: Math.min(1000, data.W_seuils.max),
                to: Math.min(3000, data.W_seuils.max),
                color: '#FFA500' // orange
            }, {
                from: Math.min(3000, data.W_seuils.max),
                to: Math.min(10000, data.W_seuils.max),
                color: '#DF5353' // red
            }]
        },

        tooltip: {
            formatter: function () {
                var tooltip;
                //puissance=data.W_data;
                //fraicheur=data.date;
                tooltip = '<b>' + Highcharts.numberFormat(this.y, 0) + ' Watts</b><br />';
                tooltip += 'Le ' +  Highcharts.dateFormat('%A %e %B %Y à %H:%M', data.debut) + '<br />';
                return tooltip;
            }
        },

        series: [{
            name : data.W_name,
            data : [data.W_data]
            /*tooltip: {
                valueSuffix: ' Watts'
            }*/
        }]
    };
}

function init_chart1_navigation() {
    "use strict";

    // Date du calendrier
    var curDate = new Date();
    curDate.setTime($("#chart1").highcharts().debut);
    curDate.setDate(curDate.getDate() - 1); // -1 Jour
    $("#chart1_date").val(curDate.getTime());
}

function init_chart1(data) {
    "use strict";

    // Préparation des séries de données
    var graphSeries = [];

    // Période courante
    $.each(data.series, function (serie_name, serie_title) {
        graphSeries.push({
            name : data[serie_name + "_name"],
            data : data[serie_name + "_data"],
            color : data[serie_name + "_color"],
            id: serie_name,
            type : 'areaspline',
            threshold : null,
            tooltip : {
                yDecimals : 0,
                valueDecimals: 0
            },
            yAxis: 0,
            visible: (data[serie_name + "_data"].reduce(function(a, b) { return a + b[1]; }, 0) !== 0),
            showInLegend: (data[serie_name + "_data"].reduce(function(a, b) { return a + b[1]; }, 0) !== 0)
        });
    });

    // Intensité
    /*graphSeries.push({
        name : data.I_name,
        data: data.I_data,
        type: 'spline',
        width : 1,
        shape: 'squarepin',
        yAxis: 1,
    });*/

    // Période précédente
    graphSeries.push({
        name : data.PREC_name,
        data : data.PREC_data,
        color : data.PREC_color,
        type: 'spline',
        width : 1,
        shape: 'squarepin',
        tooltip : {
            yDecimals : 0,
            valueDecimals: 0
        },
        visible: (data.PREC_data.reduce(function(a, b) { return a + b[1]; }, 0) !== 0),
        showInLegend: (data.PREC_data.reduce(function(a, b) { return a + b[1]; }, 0) !== 0)
    });

    return {
        chart: {
            renderTo: 'chart1',
            animation: animation,
            events: {
                load: function (chart) {
                    this.setTitle(null, {
                        text: 'Construit en ' + (new Date() - start) + 'ms'
                    });
                    if ($('#chart1_legende').length) {
                        if (data.subtitle.length > 0) $('#chart1_legende').show();
                        $("#chart1_legende").html(data.subtitle);
                    }
                    this.debut = data.debut;
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
            }, {
                type : 'hour',
                count : 3,
                text : '3h'
            }, {
                type : 'hour',
                count : 6,
                text : '6h'
            }, {
                type : 'hour',
                count : 9,
                text : '9h'
            }, {
                type : 'hour',
                count : 12,
                text : '12h'
            }, {
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
        yAxis: [{ // Primary yAxis
            title: {
                text: 'Watt'
            },
            labels: {
                formatter: function () {
                    return this.value; // + ' w';
                }
            },
            lineWidth: 2,
            showLastLabel: true,
            min: 0,
            alternateGridColor: '#FDFFD5',
            minorGridLineWidth: 0,
            plotLines : [{ // lignes min et max
                value : data.seuils.min,
                color : data.MIN_color,
                dashStyle : 'shortdash',
                width : 2,
                label : {
                    align : 'right',
                    style : {
                        color : data.MIN_color,
                    },
                    text : 'minimum ' + data.seuils.min + 'w'
                }
            }, {
                value : data.seuils.max,
                color : data.MAX_color,
                dashStyle : 'shortdash',
                width : 2,
                label : {
                    align : 'right',
                    style : {
                        color : data.MAX_color
                    },
                    text : 'maximum ' + data.seuils.max + 'w'
                }
            }]
        /*}, { // Secondary yAxis
            opposite: true,
            gridLineWidth: 0,
            title: {
                text: 'A',
                style: {
                    color: '#4572A7'
                }
            },
            labels: {
                formatter: function () {
                    return this.value; // + ' A';
                },
                style: {
                    color: '#4572A7'
                }
            }*/
        }],

        series : graphSeries,
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
        }
    };
}

function init_chart2_navigation(duree, periode) {
    "use strict";

    var arrayDuree = [];
    var i;
    var txtdecalage;

    switch (periode) {
    case ("jours"):
        for (i = 1; i <= 31; i += 1) {
            arrayDuree[i] = i;
        }
        txtdecalage = "1 jour";
        break;
    case ("semaines"):
        for (i = 1; i <= 52; i += 1) {
            arrayDuree[i] = i;
        }
        txtdecalage = "1 sem.";
        break;
    case ("mois"):
        for (i = 1; i <= 12; i += 1) {
            arrayDuree[i] = i;
        }
        txtdecalage = "1 mois";
        break;
    case ("ans"):
        for (i = 1; i <= 4; i += 1) {
            arrayDuree[i] = i;
        }
        txtdecalage = "1 mois";
        break;
    default:
    }

    // Met à jour la liste déroulante "duree"
    var select = $('.select_chart2#duree');
    var options;
    if (select.prop) {
        options = select.prop('options');
    } else {
        options = select.attr('options');
    }
    $('option', select).remove();
    $.each(arrayDuree, function (val, text) {
        if (val > 0) {
            options[options.length] = new Option(text, val);
        }
    });

    // Valeurs par défaut
    $(".select_chart2#duree").val(duree);
    $('.select_chart2#duree').selectmenu('refresh', true);
    //$(".select_chart2#duree").refresh;
    $(".select_chart2#periode").val(periode);
    $('.select_chart2#periode').selectmenu('refresh', true);
    //$(".select_chart2#periode").refresh;

    // Libelles des boutons //ui-button-text
    var btn = {};
    btn = $("#chart2_date_prec").find('span.ui-button-text');
    if (btn.length == 0) { // jQuery Mobile n'utilise pas de <span>
        btn = btn.prevObject;
    }
    btn.html("- " + txtdecalage);

    btn = $("#chart2_date_suiv").find('span.ui-button-text');
    if (btn.length == 0) { // jQuery Mobile n'utilise pas de <span>
        btn = btn.prevObject;
    }
    btn.html("+ " + txtdecalage);

    // Date du calendrier
    var curDate = new Date();
    curDate.setTime($("#chart2").highcharts().debut);
    curDate.setDate(curDate.getDate() - 1); // -1 Jour
    $("#chart2_date").val(curDate.getTime());
}

function init_chart2(data) {
    "use strict";

    // Préparation des séries de données
    var graphSeries = [];

    // Période courante
    $.each(data.series, function (serie_name, serie_title) {
        graphSeries.push({
            name : serie_title,
            data : data[serie_name + "_data"],
            color : data[serie_name + "_color"],
            stack : 'histo',
            events: {
                click: function (e) {
                    //console.log (e.point.series.name);
                    //console.log (e.point.category);
                    //console.log (e.point.x);
                    //console.log (Highcharts.dateFormat('%A, %b %e, %Y', data.debut));
                    //console.log (data.debut);
                    var newdate = new Date();
                    newdate.setTime(data.debut);
                    newdate.setDate(newdate.getDate() + e.point.x);
                    console.log(newdate);
                }
            },
            dataLabels: {
                enabled: true,
                color: '#FFFFFF',
                y: 13,
                formatter: function () {
                    if (this.y !== 0) {
                        return this.y;
                    }
                    return "";
                },
                style: {
                    font: 'normal 13px Verdana, sans-serif'
                }
            },
            type: 'column',
            visible: (data[serie_name + "_data"].reduce(function(a, b) { return a + b; }, 0) !== 0),
            showInLegend: (data[serie_name + "_data"].reduce(function(a, b) { return a + b; }, 0) !== 0)
        });
    });

    // Période précédente
    graphSeries.push({
        name : data.PREC_name,
        data : data.PREC_data,
        color : data.PREC_color,
        /*stack : 'prec',*/
        type: 'spline',
        /*type: 'scatter',
        width : 1,
        color : 'red',
        threshold : null,
        tooltip : {
            yDecimals : 0
        }*/
        visible: (data.PREC_data.reduce(function(a, b) { return a + b; }, 0) !== 0),
        showInLegend: (data.PREC_data.reduce(function(a, b) { return a + b; }, 0) !== 0)
    });

    return {
        chart: {
            renderTo: 'chart2',
            animation: animation,
            events: {
                load: function (chart) {
                    this.setTitle(null, {
                        text: 'Construit en ' + (new Date() - start) + 'ms'
                        //text: data.subtitle
                    });
                    if ($('#chart2_legende').length) {
                        if (data.subtitle.length > 0) $('#chart2_legende').show();
                        $("#chart2_legende").html(data.subtitle);
                    }
                    this.debut = data.debut;
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
            labels: {
                formatter: function () {
                    //if (this.axis.categories[this.values] !== null) {
                    if (this.axis.categories.indexOf(this.value) !== -1) {
                        return this.value;
                    }
                    return "";
                }
            },
            categories: data.categories
        }],
        yAxis: {
            title: {
                text: 'kWh'
            },
            labels: {
                formatter: function () {
                    return this.value; // + ' kWh';
                }
            },
            min: 0,
            minorGridLineWidth: 0
        },
        tooltip: {
            useHTML: true,
            formatter: function () {
                var tooltip;

                var thisSerieName = this.series.name;
                var thisPtX = this.point.x;
                var thisPtY = this.point.y;

                if (thisSerieName !== data.PREC_name) { // Période courante
                    // Date & Consommation
                    tooltip = '<span style="color:' + this.series.color + '"><b>Détails de la période</b></span><br />';
                    tooltip += '<b>' + this.key + ' </b> ~ <b> Total ' + data.optarif + ' : ' + Highcharts.numberFormat(this.point.stackTotal, 2) + ' kWh</b><br />';

                    // Abonnement
                    tooltip += 'Abonnement : ' + Highcharts.numberFormat(data.prix.ABONNEMENTS[thisPtX], 2) + ' Euro<br />';

                    // Taxes
                    tooltip += 'Taxes :<br />';
                    $.each(data.prix.TAXES, function (serie_name, serie_data) {
                        tooltip += serie_name + ' : ' + Highcharts.numberFormat(data.prix.TAXES[serie_name][thisPtX], 2) + ' Euro<br />';
                    });

                    // Coût détaillé
                    tooltip += 'Consommé :<br />';
                    $.each(data.series, function (serie_name, serie_title) {
                        // Ici, on est hors de porté du "this" de la fonction formatter.
                        // On utilise donc les variables nécessaire (thisPtX...)
                        if (data.prix.TARIFS[serie_name][thisPtX] !== 0) {
                            tooltip +='<span style="color:' + data[serie_name + "_color"] + '">';
                            if ((serie_title === thisSerieName) && (Object.keys(data.series).length > 1)) {
                                tooltip += "* ";
                            }
                            tooltip += serie_name + ' : ' + Highcharts.numberFormat(data.prix.TARIFS[serie_name][thisPtX], 2) + ' Euro';
                            tooltip += ' (' + data[serie_name + "_data"][thisPtX] + ' kWh)<br />';
                            tooltip +='</span>';
                        }
                    });

                    // Coût total
                    tooltip += '<b>Total : ' + Highcharts.numberFormat(data.prix.TOTAL[thisPtX], 2) + ' Euro<b>';
                } else { // Période Précédente
                    // Date & Consommation
                    tooltip = '<span style="color:' + data.PREC_color + '"><b>Détails de la période précédente</b></span><br />';
                    tooltip += '<b>' + this.key + ' </b> ~ <b> Total ' + data.optarif + ' : ' + Highcharts.numberFormat(this.y, 2) + ' kWh</b><br />';

                    // Abonnement
                    tooltip += 'Abonnement : ' + Highcharts.numberFormat(data.PREC_prix.ABONNEMENTS[thisPtX], 2) + ' Euro<br />';

                    // Taxes
                    tooltip += 'Taxes :<br />';
                    $.each(data.PREC_prix.TAXES, function (serie_name, serie_data) {
                        tooltip += serie_name + ' : ' + Highcharts.numberFormat(data.PREC_prix.TAXES[serie_name][thisPtX], 2) + ' Euro<br />';
                    });

                    // Coût détaillé
                    tooltip += 'Consommé :<br />';
                    $.each(data.series, function (serie_name, serie_title) {
                        // Ici, on est hors de porté du "this" de la fonction formatter.
                        // On utilise donc les variables nécessaire (thisPtX...)
                        if (data.PREC_prix.TARIFS[serie_name][thisPtX] !== 0) {
                            tooltip +='<span style="color:' + data[serie_name + "_color"] + '">';
                            if ((serie_title === thisSerieName) && (Object.keys(data.series).length > 1)) {
                                tooltip += "* ";
                            }
                            tooltip += serie_name + ' : ' + Highcharts.numberFormat(data.PREC_prix.TARIFS[serie_name][thisPtX], 2) + ' Euro';
                            tooltip += ' (' + data.PREC_data_detail[serie_name][thisPtX] + ' kWh)<br />';
                            tooltip +='</span>';
                        }
                    });

                    // Coût total
                    tooltip += '<b>Total : ' + Highcharts.numberFormat(data.PREC_prix.TOTAL[thisPtX], 2) + ' Euro<b>';
                }
                return tooltip;
            }
        },
        plotOptions: {
            column: {
                stacking: 'normal'
            }
        },
        series: graphSeries,
        legend: {
            enabled: true,
            borderColor: 'black',
            borderWidth: 1,
            shadow: true
        },
        navigation: {
            menuItemStyle: {
                fontSize: '10px'
            }
        }
    };
}

function refresh_chart0(date) {
    "use strict";

    // Remise à zéro du chronomètre
    start = new Date();

    // Désactivation du rafraichissement automatique (le cas échéant)
    disable_timer();

    // Lancement de la requête instantly
    $.getJSON('json.php?query=instantly', function (data) {
        // Création / Remplacement du graphique
        chart_elec0 = new Highcharts.Chart(init_chart0(data));

        // Activation du rafraichissement automatique
        chart_elec0_delay = data.refresh_delay;
        if (data.refresh_auto) {
            enable_timer(refresh_chart0, chart_elec0_delay * 1000);
        }
    });
}

function refresh_chart1(date) {
    "use strict";

    // Remise à zéro du chronomètre
    start = new Date();

    // Lancement de la requête daily
    var parameters = (date ? "&date=" + date.getTime() / 1000 : "");
    $.getJSON('json.php?query=daily' + parameters, function (data) {
        // Création / Remplacement du graphique
        chart_elec1 = new Highcharts.StockChart(init_chart1(data));
    });
}

function refresh_chart2(duree, periode, date) {
    "use strict";

    // Remise à zéro du chronomètre
    start = new Date();

    // Lancement de la requête historique
    var parameters = (duree ? "&duree=" + duree : "") + (periode ? "&periode=" + periode : "") + (date ? "&date=" + date.getTime() / 1000 : "");
    $.getJSON('json.php?query=history' + parameters, function (data) {
        // Création / Remplacement du graphique
        chart_elec2 = new Highcharts.Chart(init_chart2(data));
    });
}

function process_chart0_button(object) {
    "use strict";

    refresh_chart0();
}

function process_chart1_button(object) {
    "use strict";

    var curdate = $("#chart1").highcharts().debut;
    var newdate = new Date();
    newdate.setTime(curdate);

    switch (object.value) {
    case "date":
        newdate = undefined;
        $("#chart1_date").datepicker('show');
        break;
    case "1prec":
        newdate.setDate(newdate.getDate() - 1);
        break;
    case "1suiv":
        newdate.setDate(newdate.getDate() + 1);
        break;
    case "now":
        newdate = null;
        break;
    default:
        newdate = null;
    }

    if (newdate !== undefined) {
        refresh_chart1(newdate);
    }
}

function process_chart2_button(object) {
    "use strict";

    var periode = $(".select_chart2#periode").val();
    var duree = $(".select_chart2#duree").val();
    var curdate = $("#chart2").highcharts().debut;
    var newdate = new Date();
    newdate.setTime(curdate);

    var coefdate;
    // Type de changement de date
    switch (object.value) {
    case "date":
        newdate = undefined;
        $("#chart2_date").datepicker('show');
        break;
    case "1prec":
        // on recule
        coefdate = -1;
        break;
    case "1suiv":
        // on avance
        coefdate = 1;
        break;
    case "now":
        // retour à aujourd'hui
        newdate = null;
        break;
    default:
        // on ne change rien
        coefdate = 0;
    }

    // Calcul du décalage de date
    if (newdate) {
        switch (periode) {
        case ("jours"):
            // décalage d'un jour
            newdate.setDate(newdate.getDate() + coefdate);
            break;
        case ("semaines"):
            // décalage d'une semaine
            newdate.setDate(newdate.getDate() + 7 * coefdate);
            break;
        case ("mois"):
            // décalage d'un mois
            newdate.setMonth(newdate.getMonth() + coefdate);
            break;
        case ("ans"):
            // décalage d'un mois
            newdate.setMonth(newdate.getMonth() + coefdate);
            break;
        default:
            // on ne change rien
            newdate.setDate(newdate.getDate());
        }
    }

    if (newdate !== undefined) {
        refresh_chart2(duree, periode, newdate);
    }
}

function process_chart2_select(object) {
    "use strict";

    var periode = $(".select_chart2#periode").val();
    var duree = $(".select_chart2#duree").val();
    var curdate = $("#chart2").highcharts().debut;
    var newdate = new Date();
    newdate.setTime(curdate);

    /* // Teste si on doit changer de période
    if (this.id === "periode") {
        periode = object.item.value;
    } else if (this.id === "duree") {
        duree = object.item.value;
    }*/
    refresh_chart2(duree, periode, newdate);
}

function refresh_charts(pageName) {
    "use strict";

    switch (pageName) {
    case 'page0':
        // Crée le graphique 0 (instantly)
        refresh_chart0();
        break;
    case 'page1':
        // Crée le graphique 1 (daily)
        refresh_chart1();
        break;
    case 'page2':
        // Crée le graphique 2 (history)
        refresh_chart2();
        break;
    default:
        // Crée le graphique 0 (instantly)
        refresh_chart0();
        // Crée le graphique 1 (daily)
        refresh_chart1();
        // Crée le graphique 2 (history)
        refresh_chart2();
    }
}

function change_date() {
    "use strict";
    var thisID = $(this).attr("id");

    var newdate = new Date();
    newdate.setTime($(this).val());
    newdate.setDate(newdate.getDate() + 1); // +1 Jour

    switch (thisID) {
    case 'chart0_date':
        refresh_chart0(newdate);
        break;
    case 'chart1_date':
        refresh_chart1(newdate);
        break;
    case 'chart2_date':
        refresh_chart2(newdate);
        break;
    default:
    }
}

function init_events() {
    "use strict";

    // Evénements boutons (click)
    $('.button_chart0').unbind('click').click(
        function () {process_chart0_button(this); }
    );
    $('.button_chart1').unbind('click').click(
        function () {process_chart1_button(this); }
    );
    $('.button_chart2').unbind('click').click(
        function () {process_chart2_button(this); }
    );
    // Evénement selectmenu (change)
    if ($.mobile) {
        $('.select_chart2').unbind('change').change(
            function (e, object) {process_chart2_select(object); }
        );
    } else {
        $('.select_chart2').selectmenu({
            change: function (e, object) {process_chart2_select(object); }
        });
    }
    // Evénement datepicker (select)
    $('.datepicker').each(function () {
        var itemID = "#" + $(this).attr('id');
        $(itemID).datepicker("option", "onSelect", change_date);
    });
}

if ($.mobile) {
     //jq mobile loaded
    $(document).on("pageshow", '[data-role="page"]', function (event, ui) {
        "use strict";

        // Initialisation jQueryUI datepicker
        $.datepicker.setDefaults($.datepicker.regional["fr"]);
        $('.datepicker').each(function () {
            var itemID = "#" + $(this).attr('id');
            $(itemID).datepicker({
                showAnim: "blind",
                showOn: "button",
                //buttonImage: "images/glyphish/83-calendar.png",
                buttonImage: "images/tango icons/X-office-calendar-alpha.png",
                buttonText: "Sélection de la date",
                buttonImageOnly: true,
                showButtonPanel: true,
                changeMonth: true,
                changeYear: true,
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: $.datepicker.TIMESTAMP, // $.datepicker.ISO_8601,
                maxDate: 0
            });
        });

        // Enhance tooltip appearence, using JQuery styling
        if ($(document).tooltip) {
            // UI loaded
            $(document).tooltip({
                // use the built-in fadeIn/fadeOut effect
                effect: "fade"
            });
        } else {
            // not
        }

        init_events();

        var pageName;
        // Disable previous indicator
        if (typeof (ui.prevPage) === 'object') {
            pageName = ui.prevPage.attr("id");
            $("#nav_" + pageName, "#" + pageName).removeClass("ui-btn-active");
        }
        // Enable selected indicator
        if (typeof ($(this)) === 'object') {
            pageName = $(this).attr("id");
            $("#nav_" + pageName, "#" + pageName).addClass("ui-btn-active");
        }

    });
    $(document).on("pagechange", function (event, ui) {
        "use strict";

        var pageName;
        if (typeof (ui.toPage) === 'object') {
            pageName = ui.toPage.attr("id");
            refresh_charts(pageName);
        }
    });

} else {
    $(document).ready(function () {
        "use strict";

        // Initialisation jQueryUI button
        $('.button_chart0').button();
        $('.button_chart1').button();
        $('.button_chart2').button();

        // Icones jQueryUI
        $('#chart0_refresh').button("option", "icons", {primary: "ui-icon-refresh"});
        $('#chart1_date_prec').button( "option", "icons", {primary: "ui-icon-arrowthick-1-w"});
        $('#chart1_date_select').button( "option", "icons", {primary: "ui-icon-calendar", secondary: "ui-icon-triangle-1-s"});
        $('#chart1_date_suiv').button( "option", "icons", {secondary: "ui-icon-arrowthick-1-e"});

        $('#chart2_date_prec').button( "option", "icons", {primary: "ui-icon-arrowthick-1-w"});
        $('#chart2_date_now').button( "option", "icons", {primary: "ui-icon-calendar"});
        $('#chart2_date_select').button( "option", "icons", {primary: "ui-icon-calendar", secondary: "ui-icon-triangle-1-s"});
        $('#chart2_date_suiv').button( "option", "icons", {secondary: "ui-icon-arrowthick-1-e"});

        // Initialisation jQueryUI selectmenu
        $('.select_chart2').selectmenu({
            dropdown: false
        });
        // Overflow : permet de limiter la hauteur des listes déroulantes (via css)
        $('.select_chart2').selectmenu("menuWidget").addClass("ui-selectmenu-overflow");

        // Initialisation jQueryUI datepicker
        $.datepicker.setDefaults($.datepicker.regional["fr"]);
        $('.datepicker').each(function () {
            var itemID = "#" + $(this).attr('id');
            $(itemID).datepicker({
                showAnim: "blind",
                showOn: "button",
                //buttonImage: "images/glyphish/83-calendar.png",
                buttonImage: "images/tango icons/X-office-calendar-alpha.png",
                buttonText: "Sélection de la date",
                buttonImageOnly: true,
                showButtonPanel: true,
                changeMonth: true,
                changeYear: true,
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: $.datepicker.TIMESTAMP, // $.datepicker.ISO_8601,
                maxDate: 0
            });
        });

        // Enhance tooltip appearence, using JQuery styling
        if ($(document).tooltip) {
            // UI loaded
            $(document).tooltip({
                // use the built-in fadeIn/fadeOut effect
                effect: "fade"
            });
        } else {
            // not
        }

        init_events();

        // Enable tab navigation
        if ($('#tabs').length > 0) {
            $('#tabs')
                .tabs({
                    create: function(event, ui) {
                        var pageName;
                        if (typeof (ui.panel) === 'object') {
                            pageName = ui.panel.attr("id");
                            refresh_charts(pageName);
                        }
                    },
                    activate: function(event, ui) {
                        var pageName;
                        if (typeof (ui.newPanel) === 'object') {
                            pageName = ui.newPanel.attr("id");
                            refresh_charts(pageName);
                        }
                    }
                });
        } else {
            // Rafraîchit tous les graphiques à la fois
            refresh_charts();
        }

        // Sablier durant les requêtes AJAX (style CSS à définir)
        $(document)
            .ajaxStart(function () {
                // Ajoute la classe CCS 'busy'
                $(this).addClass('busy');
                // Désactive les éléments de navigation
                //$('.button_chart0').button("option", "disabled", true);
                //$('.button_chart0').button("disable");
                //$('.button_chart0').prop("disabled", true);
                //$('.button_chart0').addClass("ui-state-disabled");
                //$('.button_chart1').addClass("ui-state-disabled");
                //$('.button_chart2').addClass("ui-state-disabled");
                //$('.select_chart2').addClass("ui-state-disabled");
            })
            .ajaxStop(function () {
                // Supprime la classe CCS 'busy'
                $(this).removeClass('busy');
                // Active les éléments de navigation
                //$('.button_chart0').removeClass("ui-state-disabled");
                //$('.button_chart1').removeClass("ui-state-disabled");
                //$('.button_chart2').removeClass("ui-state-disabled");
                //$('.select_chart2').addClass("ui-state-disabled");
            });
    });
}