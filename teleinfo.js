// JSLint options
/*global console:false, document:false, $:false, jQuery:false, Highcharts:false, setInterval:false, clearInterval:false, Option:false*/
/*jslint todo:true, vars:true*/

var start = {}; // = new Date();

var detailPrix = [];
/*var totalBASE = 0;
var totalHP = 0;
var totalHC = 0;*/
var totalPrix = 0;
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

    // Libelles des boutons
    $("#chart0_refresh").html("Rafra&icirc;chir");
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

    // Libelles des boutons
    $("#chart1_date_prec").html("&laquo;&nbsp;- 24h");
    $("#chart1_date_now").html("Aujourd'hui");
    $("#chart1_date_suiv").html("+ 24h&nbsp;&raquo;");
}

function init_chart1(data) {
    "use strict";

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
        yAxis: [{
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
                yDecimals : 0,
                valueDecimals: 0
            },
            showInLegend: ((data.optarif === "BASE") ? true : false)
        }, {
            name : data.HP_name,
            data : data.HP_data,
            id: 'HP',
            type : 'areaspline',
            threshold : null,
            tooltip : {
                yDecimals : 0,
                valueDecimals: 0
            },
            showInLegend: ((data.optarif !== "BASE") ? true : false)
        }, {
            name : data.HC_name,
            data : data.HC_data,
            id: 'HC',
            type : 'areaspline',
            threshold : null,
            tooltip : {
                yDecimals : 0,
                valueDecimals: 0
            },
            showInLegend: ((data.optarif !== "BASE") ? true : false)
        }, {
            /*name : data.I_name,
            data: data.I_data,
            type: 'spline',
            width : 1,
            shape: 'squarepin'
        }, {*/
            name : data.JPrec_name,
            data: data.JPrec_data,
            type: 'spline',
            width : 1,
            shape: 'squarepin',
            tooltip : {
                yDecimals : 0,
                valueDecimals: 0
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

    // Libelles des boutons
    $("#chart2_date_prec").html("&laquo;&nbsp;- " + txtdecalage);
    $("#chart2_date_now").html("Aujourd'hui");
    $("#chart2_date_suiv").html("+ " + txtdecalage + "&nbsp;&raquo;");
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
        type: 'spline'
        /*type: 'scatter',
        width : 1,
        color : 'red',
        threshold : null,
        tooltip : {
            yDecimals : 0
        }*/
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
            formatter: function () {
                var tooltip;
                var thisSerieName = this.series.name;
                var thisPtX = this.point.x;
                var thisPtY = this.point.y;

                if (this.series.name === data.PREC_name) { // 'Période Précédente') {
                    // Date & Consommation
                    tooltip = '<b>' + this.key + ' </b><br /><b>' + this.series.name + ' : ' + Highcharts.numberFormat(this.y, 2) + ' kWh</b><br />';

                    // Coût détaillé
                    totalPrix = 0;
                    $.each(data.series, function (serie_name, serie_title) {
                        // Ici, on est hors de porté du "this" de la fonction formatter.
                        // On utilise donc les variables nécessaire (thisPtX...)
                        detailPrix[serie_name] = data.prix[serie_name] * data.PREC_data_detail[serie_name][thisPtX];
                        if ((Object.keys(data.series).length > 1) && (detailPrix[serie_name] !== 0)) {
                            tooltip += serie_name + ' : ' + Highcharts.numberFormat(detailPrix[serie_name], 2) + ' Euro (' + data.PREC_data_detail[serie_name][thisPtX] + ' kWh)<br />';
                        }
                        totalPrix += detailPrix[serie_name];
                    });

                    // Coût total
                    totalPrix = Highcharts.numberFormat((totalPrix + data.prix.abonnement), 2);
                    tooltip += 'Abonnement sur la période : ' + Highcharts.numberFormat(data.prix.abonnement, 2) + ' Euro<br />';
                    tooltip += '<b>Total: ' + totalPrix + ' Euro<b>';
                } else {
                    // Date & Consommation
                    tooltip = '<b>' + this.key + ' </b><br /><b> Tarification ' + data.optarif + ' : ' + Highcharts.numberFormat(this.point.stackTotal, 2) + ' kWh</b><br />';

                    // Coût détaillé
                    totalPrix = 0;
                    $.each(data.series, function (serie_name, serie_title) {
                        // Ici, on est hors de porté du "this" de la fonction formatter.
                        // On utilise donc les variables nécessaire (thisPtX...)
                        detailPrix[serie_name] = data.prix[serie_name] * data[serie_name + "_data"][thisPtX];
                        if ((Object.keys(data.series).length > 1) && (detailPrix[serie_name] !== 0)) {
                            if (serie_title === thisSerieName) {
                                tooltip += "* ";
                            }
                            tooltip += serie_name + ' : ' + Highcharts.numberFormat(detailPrix[serie_name], 2) + ' Euro (' + data[serie_name + "_data"][thisPtX] + ' kWh)<br />';
                        }
                        totalPrix += detailPrix[serie_name];
                    });

                    // Coût total
                    totalPrix = Highcharts.numberFormat((totalPrix + data.prix.abonnement), 2);
                    tooltip += 'Abonnement sur la période : ' + Highcharts.numberFormat(data.prix.abonnement, 2) + ' Euro<br />';
                    tooltip += '<b>Total: ' + totalPrix + ' Euro<b>';
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
    refresh_chart1(newdate);
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

    refresh_chart2(duree, periode, newdate);
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
}

if ($.mobile) {
     //jq mobile loaded
    $(document).on("pageshow", '[data-role="page"]', function (event, ui) {
        "use strict";

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

        // Initialisation jQueryUI selectmenu
        $('.select_chart2').selectmenu({
            dropdown: false
        });
        // Overflow : permet de limiter la hauteur des listes déroulantes (via css)
        $('.select_chart2').selectmenu("menuWidget").addClass("ui-selectmenu-overflow");

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