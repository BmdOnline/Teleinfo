// JSLint options
/*global console:false, document:false, $:false, jQuery:false, Highcharts:false, setInterval:false, clearInterval:false, Option:false*/
/*jslint todo:true, vars:true*/

var start = {}; // = new Date();

var chart_elec0;
var chart_elec1;
var chart_elec2;

var timerID;
var chart_elec0_delay = 60; // secondes

var hcTheme = {
    global: {
        useUTC: false
    },
    lang: {
        months: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
        shortMonths: [ 'Jan' , 'Fév' , 'Mar' , 'Avr' , 'Mai' , 'Juin' ,
            'Juil' , 'Août' , 'Sep' , 'Oct' , 'Nov' , 'Déc'],
        weekdays: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
        decimalPoint: ',',
        thousandsSep: '.',
        rangeSelectorFrom: 'Du',
        rangeSelectorTo: 'au'
    },
    credits: {
        enabled: false
    },
    /*colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572',
             '#FF9655', '#FFF263', '#6AF9C4'], */
    chart: {
        animation: true,
        /*animation: {
            duration: 800,
            easing: 'swing'
        },*/
        borderColor: '#EBBA95',
        borderWidth: 2,
        borderRadius: 10
        /*backgroundColor: {
            linearGradient: [0, 0, 500, 500],
            stops: [
                [0, 'rgb(255, 255, 255)'],
                [1, 'rgb(240, 240, 255)']
            ]
        },*/
    },
    title: {
        style: {
            color: '#333333',
            font: 'bold 16px Verdana, sans-serif'
            //font: 'bold 16px "Trebuchet MS", Verdana, sans-serif'
        }
    },
    subtitle: {
        style: {
            color: '#666666',
            font: 'bold 12px Verdana, sans-serif'
            //font: 'bold 12px "Trebuchet MS", Verdana, sans-serif'
        }
    },
    plotOptions: {
        column: {
            dataLabels: {
                enabled: false,
                overflow: 'crop',
                crop: true,
                color: '#FFFFFF',
                formatter: function () {
                    if (this.y === 0) {
                        return "";
                    }
                    return this.y;
                }
                /*style: {
                    font: 'normal 13px Verdana, sans-serif'
                }*/
            }
        },
        gauge: {
            dataLabels: {
                color: '#666666',
                backgroundColor: '#FDFFD5',
            },
        }
    },
    xAxis: {
        dateTimeLabelFormats: {
            /*millisecond: '%H:%M:%S.%L',
            second: '%H:%M:%S',
            minute: '%H:%M',
            hour: '%H:%M',
            day: '%e. %b',
            week: '%e. %b',
            month: '%b \'%y',
            year: '%Y'*/
            hour: '%H:%M',
            //day: '%H:%M',
            week: '%H:%M',
            month: '%H:%M'
        }
    },
    yAxis: {
        lineWidth: 2,
        showLastLabel: true,
        min: 0,
        maxPadding: 0.1,
        alternateGridColor: '#FDFFD5',
        minorGridLineWidth: 0,
        labels: {
            //rotation: -90,
            align: 'left',
            x: 5
        },
    },
    legend: {
        enabled: false,
        borderColor: 'black',
        borderWidth: 1,
        shadow: true
        /*itemStyle: {
            font: '9pt Trebuchet MS, Verdana, sans-serif',
            color: 'black'
        },
        itemHoverStyle:{
            color: 'gray'
        },*/
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
    /*scrollbar: { // scrollbar "stylée" grise
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
    },*/
    navigator: {
        //top: 360,
        menuItemStyle: {
            fontSize: '10px'
        },
        series: {
            color: '#4572A7',
            fillOpacity: 0.55
        }
    },
    navigation: {
        menuItemStyle: {
            fontSize: '10px'
        }
    },
    pane: { // Gauge
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
    }
};

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

    // Apply the theme
    //Highcharts.setOptions(Highcharts.theme);
    Highcharts.setOptions(hcTheme);
});

function init_chart0_navigation() {
    "use strict";

    // Date du calendrier
    var curDate = new Date();
    curDate.setTime($("#chart0").highcharts().debut);
    curDate.setDate(curDate.getDate() - 1); // -1 Jour
    $("#chart0_date").val(curDate.getTime());

}

function init_chart0(data, serie) {
    "use strict";

    // Préparation des séries à afficher (nombre de gauges)
    var serieNames = [];
    if (serie !== undefined) {
        serieNames.push(serie);
    } else {
        $.each(data.series, function (serie_name, serie_title) {
            serieNames.push(serie_name);
        });
    }

    // Eléments du graphique
    var graphPanes = [];
    var plotBands = [];
    var graphYAxis = [];
    var graphSeries = [];
    var gPos;
    var gStep = 100 / serieNames.length;
    var centerPos = [];
    var paneSize;
    var band_min;
    $.each(serieNames, function (serie_num, serie_name) {
        // Position de chacune des gauges (horizontalement ou verticalement)
        gPos = (gStep / 2) + (gStep * serie_num);
        if ($("#chart0").height() <= $("#chart0").width()) {
            centerPos = [ // Horizontal
                Highcharts.numberFormat(gPos, 0) + '%',
                '50%'
            ];
            paneSize = Math.min($("#chart0").width() / 2 * 0.8, $("#chart0").height() * 0.75);
        } else {
            centerPos = [ // Vertical
                '50%',
                Highcharts.numberFormat(gPos, 0) + '%'
            ];
            paneSize = Math.min($("#chart0").width() * 0.8, $("#chart0").height() / 2 * 0.75);
        }
        // Seuils des gauges
        band_min = 0;
        plotBands = []; // RAZ
        $.each(data.bands[serie_name], function (band_max, band_color) {
            plotBands.push({
                from: Math.min(band_min, data.seuils[serie_name].max),
                to: Math.min(band_max, data.seuils[serie_name].max),
                color: band_color
            });
            band_min = band_max;
        });

        // Default Options doesn't works for pane. So we merge them manually
        graphPanes.push(Highcharts.merge(hcTheme.pane, {
                center: centerPos,
                size: paneSize
        }));

        graphYAxis.push({
            min: data.seuils[serie_name].min,
            max: data.seuils[serie_name].max,
            pane: serie_num,
            minorTickInterval: 'auto',
            minorTickWidth: 1,
            minorTickLength: 10,
            minorTickPosition: 'inside',
            minorTickColor: '#999',

            tickInterval: data.seuils[serie_name].max / 10, // 10 graduations au total
            tickWidth: 2,
            tickLength: 15,
            tickPosition: 'inside',
            tickColor: '#666',
            labels: {
                step: 2,
                rotation: 'auto'
            },
            title: {
                text: data.series[serie_name] // 'Watts'
            },
            plotBands: plotBands
        });
        graphSeries.push({
            dataLabels: {
                enabled: true
            },
            name : data.series[serie_name],
            data : [data.data[serie_name]],
            yAxis: serie_num
        });
    });

    Highcharts.setOptions(hcTheme);
    return {
        chart: {
            renderTo: 'chart0',
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
                        if (data.subtitle.length > 0) { $('#chart0_legende').show(); }
                        $("#chart0_legende").html(data.subtitle);
                    }
                    this.debut = data.debut;
                    init_chart0_navigation();
                },
                resize: function () {
                    /*var plotWidth = this.plotSizeX; // this.width - this.marginLeft - this.marginRight;
                    var plotHeight = this.plotSizeY; // this.height - this.marginTop - this.marginBottom;
                    console.log ("plot area : " + plotWidth + "x" + plotHeight);*/
                    refresh_chart0();
                }
            }
        },
        title: {
            text: data.title // 'Puissance instantanée'
        },
        subtitle: {
            text: 'Construit en...'
        },
        tooltip: {
            formatter: function () {
                var tooltip;
                //puissance=data.W_data;
                //fraicheur=data.date;
                tooltip = '<b>' + Highcharts.numberFormat(this.y, 0) + ' ' + this.series.name + '</b><br />';
                tooltip += 'Le ' +  Highcharts.dateFormat('%A %e %B %Y à %H:%M', data.debut) + '<br />';
                return tooltip;
            }
        },
        pane: graphPanes,
        yAxis: graphYAxis,
        series: graphSeries
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
            visible: (data[serie_name + "_data"].reduce(function (a, b) { return a + b[1]; }, 0) !== 0),
            showInLegend: (data[serie_name + "_data"].reduce(function (a, b) { return a + b[1]; }, 0) !== 0)
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
        visible: (data.PREC_data.reduce(function (a, b) { return a + b[1]; }, 0) !== 0),
        showInLegend: (data.PREC_data.reduce(function (a, b) { return a + b[1]; }, 0) !== 0)
    });

    Highcharts.setOptions(hcTheme);
    return {
        chart: {
            renderTo: 'chart1',
            events: {
                load: function (chart) {
                    this.setTitle(null, {
                        text: 'Construit en ' + (new Date() - start) + 'ms'
                    });
                    if ($('#chart1_legende').length) {
                        if (data.subtitle.length > 0) { $('#chart1_legende').show(); }
                        $("#chart1_legende").html(data.subtitle);
                    }
                    this.debut = data.debut;
                    init_chart1_navigation(data.duree, data.periode);
                }
            },
            ignoreHiddenSeries: false
        },
        title: {
            text : data.title
        },
        subtitle: {
            text: 'Construit en...'
        },
        xAxis: {
            type: 'datetime'
        },
        yAxis: [{ // Primary yAxis
            opposite: false,
            title: {
                text: 'Watt'
            },
            labels: {
                formatter: function () {
                    return this.value; // + ' w';
                }
            },
            plotLines : [{ // lignes min et max
                value : data.seuils.min,
                color : data.MIN_color,
                dashStyle : 'shortdash',
                width : 2,
                label : {
                    align : 'right',
                    style : {
                        color : data.MIN_color
                    },
                    text : 'min. ' + data.seuils.min + 'w'
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
                    text : 'max. ' + data.seuils.max + 'w'
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
        tooltip: {
            crosshairs: true
        },
        series : graphSeries,
        legend: {
            enabled: true
        },
        navigator: {
            series: {
                name: 'navigator',
                type: 'areaspline',
                data: data.navigator
            }
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
    if (btn.length === 0) { // jQuery Mobile n'utilise pas de <span>
        btn = btn.prevObject;
    }
    btn.html("- " + txtdecalage);

    btn = $("#chart2_date_suiv").find('span.ui-button-text');
    if (btn.length === 0) { // jQuery Mobile n'utilise pas de <span>
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
            type: data[serie_name + "_type"], // 'column',
            stack : 0, // 'histo',
            /*events: {
                click: function (e) {
                    var newdate = new Date();
                    newdate.setTime(data.debut);
                    newdate.setDate(newdate.getDate() + e.point.x);
                    console.log(newdate);
                }
            },*/
            dataLabels: {
                enabled: true
            },
            visible: (data[serie_name + "_data"].reduce(function (a, b) { return a + b; }, 0) !== 0),
            showInLegend: (data[serie_name + "_data"].reduce(function (a, b) { return a + b; }, 0) !== 0)
        });
    });

    // Période précédente
    graphSeries.push({
        name : data.PREC_name,
        data : data.PREC_data,
        color : data.PREC_color,
        type: data.PREC_type, // 'spline',
        stack: 1, // 'previous',
        visible: (data.PREC_data.reduce(function (a, b) { return a + b; }, 0) !== 0),
        showInLegend: (data.PREC_data.reduce(function (a, b) { return a + b; }, 0) !== 0)
    });

    Highcharts.setOptions(hcTheme);
    return {
        chart: {
            type: 'column', // Used for 3D representation (look at plotOptions)
            defaultSeriesType: 'column',
            options3d: {
                enabled: data.show3D,
                alpha: 15,
                beta: 15,
                depth: 50
            },
            renderTo: 'chart2',
            events: {
                load: function (chart) {
                    this.setTitle(null, {
                        text: 'Construit en ' + (new Date() - start) + 'ms'
                    });
                    if ($('#chart2_legende').length) {
                        if (data.subtitle.length > 0) { $('#chart2_legende').show(); }
                        $("#chart2_legende").html(data.subtitle);
                    }
                    this.debut = data.debut;
                    init_chart2_navigation(data.duree, data.periode);
                }
            },
            ignoreHiddenSeries: false
        },
        title: {
            text : data.title
        },
        subtitle: {
            text: 'Construit en...'
        },
        plotOptions: {
            column: {
                stacking: true, // 'normal',
                // When 3D and spline, don't do stacking
                //stacking: data.show3D ? (data.PREC_type === "column") : (data.PREC_type === "spline"),
                depth: 50,
                grouping: false,
                groupZPadding: 20
            }
            /*spline: {
                stacking: true, // 'normal',
                depth: 50,
            }*/
        },
        xAxis: [{
            labels: {
                formatter: function () {
                    //if (this.axis.categories[this.values] === null) {
                    if (this.axis.categories.indexOf(this.value) === -1) {
                        return "";
                    }
                    return this.value;
                }
            },
            categories: data.categories
        }],
        yAxis: {
            opposite: false,
            title: {
                text: 'kWh'
            },
            labels: {
                formatter: function () {
                    return this.value; // + ' kWh';
                }
            }
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
                    tooltip += '<span><b>' + data.optarif[Object.keys(data.optarif)[0]] + ' </b></span><br />';
                    tooltip += '<b>' + this.key + ' </b> ~ <b> Total : ' + Highcharts.numberFormat(this.point.stackTotal, 2) + ' kWh</b><br />';

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
                            tooltip += '<span style="color:' + data[serie_name + "_color"] + '">';
                            if ((serie_title === thisSerieName) && (Object.keys(data.series).length > 1)) {
                                tooltip += "* ";
                            }
                            tooltip += serie_name + ' : ' + Highcharts.numberFormat(data.prix.TARIFS[serie_name][thisPtX], 2) + ' Euro';
                            tooltip += ' (' + data[serie_name + "_data"][thisPtX] + ' kWh)<br />';
                            tooltip += '</span>';
                        }
                    });

                    // Coût total
                    tooltip += '<b>Total : ' + Highcharts.numberFormat(data.prix.TOTAL[thisPtX], 2) + ' Euro<b>';
                } else { // Période Précédente
                    // Date & Consommation
                    tooltip = '<span style="color:' + data.PREC_color + '"><b>Détails de la période précédente</b></span><br />';
                    tooltip += '<span><b>' + data.optarif[Object.keys(data.optarif)[0]] + ' </b></span><br />';
                    tooltip += '<b>' + this.key + ' </b> ~ <b> Total : ' + Highcharts.numberFormat(this.y, 2) + ' kWh</b><br />';

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
                            tooltip += '<span style="color:' + data[serie_name + "_color"] + '">';
                            if ((serie_title === thisSerieName) && (Object.keys(data.series).length > 1)) {
                                tooltip += "* ";
                            }
                            tooltip += serie_name + ' : ' + Highcharts.numberFormat(data.PREC_prix.TARIFS[serie_name][thisPtX], 2) + ' Euro';
                            tooltip += ' (' + data.PREC_data_detail[serie_name][thisPtX] + ' kWh)<br />';
                            tooltip += '</span>';
                        }
                    });

                    // Coût total
                    tooltip += '<b>Total : ' + Highcharts.numberFormat(data.PREC_prix.TOTAL[thisPtX], 2) + ' Euro<b>';
                }
                return tooltip;
            }
        },
        series: graphSeries,
        legend: {
            enabled: true
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
                //buttonImage: "images/tango icons/X-office-calendar-alpha.png",
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

    // Sablier durant les requêtes AJAX (style CSS à définir)
    $(document)
        .ajaxStart(function () {
            "use strict";

            $.mobile.loading("show");
            // Désactive les éléments de navigation
            $('.ui-page').addClass("ui-state-disabled");
            //$('.ui-btn').addClass("ui-state-disabled");
            //$('.ui-select').addClass("ui-state-disabled");
        })
        .ajaxStop(function () {
            "use strict";

            // Supprime la classe CCS 'busy'
            $.mobile.loading("hide");
            // Active les éléments de navigation
            $('.ui-page').removeClass("ui-state-disabled");
            //$('.ui-btn').removeClass("ui-state-disabled");
            //$('.ui-select').removeClass("ui-state-disabled");
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
        $('#chart1_date_prec').button("option", "icons", {primary: "ui-icon-arrowthick-1-w"});
        $('#chart1_date_select').button("option", "icons", {primary: "ui-icon-calendar", secondary: "ui-icon-triangle-1-s"});
        $('#chart1_date_suiv').button("option", "icons", {secondary: "ui-icon-arrowthick-1-e"});

        $('#chart2_date_prec').button("option", "icons", {primary: "ui-icon-arrowthick-1-w"});
        $('#chart2_date_now').button("option", "icons", {primary: "ui-icon-calendar"});
        $('#chart2_date_select').button("option", "icons", {primary: "ui-icon-calendar", secondary: "ui-icon-triangle-1-s"});
        $('#chart2_date_suiv').button("option", "icons", {secondary: "ui-icon-arrowthick-1-e"});

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
                //buttonImage: "images/tango icons/X-office-calendar-alpha.png",
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
        }

        init_events();

        // Enable tab navigation
        if ($('#tabs').length > 0) {
            $('#tabs')
                .tabs({
                    create: function (event, ui) {
                        var pageName;
                        if (typeof (ui.panel) === 'object') {
                            pageName = ui.panel.attr("id");
                            refresh_charts(pageName);
                        }
                    },
                    activate: function (event, ui) {
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
                //$('.wait').show();
                $('.wait').addClass("ui-icon-loading");
                // Désactive les éléments de navigation
                $('.ui-tabs-panel').addClass("ui-state-disabled");
                //$('.ui-button').addClass("ui-state-disabled");
                //$('.ui-selectmenu-button').addClass("ui-state-disabled");
            })
            .ajaxStop(function () {
                //$('.wait').hide();
                $('.wait').removeClass("ui-icon-loading");
                // Active les éléments de navigation
                $('.ui-tabs-panel').removeClass("ui-state-disabled");
                //$('.ui-button').removeClass("ui-state-disabled");
                //$('.ui-selectmenu-button').removeClass("ui-state-disabled");
            });
    });
}