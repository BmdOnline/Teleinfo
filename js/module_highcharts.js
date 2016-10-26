// JSLint options
/*global $:false, jQuery:false, Highcharts:false*/
/*jslint indent:4, todo:true, vars:true, unparam:true */

// Fonctions et variables externes
var start;
var chart_loaded;
var refresh_chart0;
var refresh_chart1;
var refresh_chart2;
var tooltip_chart0;
var tooltip_chart1;
var tooltip_chart2;

// utilisé pour rechercher la date la plus proche (tooltip)
/*function max(arr) { return Math.max.apply(null, arr); }
function min(arr) { return Math.min.apply(null, arr); }
function nearest(arr, x) {
    "use strict";

    var l = [], h = [];

    arr.forEach(function (v) {
        ((v < x) && l.push(v)) || ((v > x) && h.push(v));
    });

    return { "low": arr.indexOf(max(l)), "high": arr.indexOf(min(h)) };
}*/

var hcTheme = {
    global: {
        useUTC: false
    },
    lang: {
        months: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
        shortMonths: [ 'Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin',
            'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
        weekdays: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
        decimalPoint: ',',
        thousandsSep: '.',
        rangeSelectorFrom: 'Du',
        rangeSelectorTo: 'au'
    },
    credits: {
        enabled: false
    },
    chart: {
        animation: true,
        /*animation: {
            duration: 800,
            easing: 'swing'
        },*/
        backgroundColor: null // Défini en CSS fond blanc détouré orange. null pour ne pas créer de fond, même vide.
    },
    plotOptions: {
        column: {
            dataLabels: {
                enabled: false,
                overflow: 'crop',
                crop: true,
                formatter: function () {
                    "use strict";

                    return (this.y === 0) ? "" : this.y;
                }
            }
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
        alternateGridColor: 'rgba(0, 0, 0, 0)', // Défini en CSS .highcharts-plot-band. 'rgba(0, 0, 0, 0)' pour forcer la création des bandes
        minorGridLineWidth: 0,
        labels: {
            //rotation: -90,
            align: 'left',
            x: 5
        }
    },
    legend: {
        enabled: false,
        //borderColor: rgba(0, 0, 0, 0), // Défini en CSS .highcharts-legend-item et .highcharts-legend-box
        borderWidth: 1,
        shadow: true
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
    navigator: {
        series: {
            lineWidth: 2,
            fillOpacity: 0.55
        }
    },
    pane: { // Gauge
        startAngle: -150,
        endAngle: 150,
        background: [{
            borderWidth: 0,
            outerRadius: '109%',
            backgroundColor: {
                linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                stops: [
                    [0, '#FFF'],
                    [1, '#333']
                ]
            }
        }, {
            borderWidth: 1,
            outerRadius: '107%',
            backgroundColor: {
                linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                stops: [
                    [0, '#333'],
                    [1, '#FFF']
                ]
            }
        }, {
            // default background
        }, {
            borderWidth: 0,
            outerRadius: '105%',
            innerRadius: '103%',
            backgroundColor: '#DDD'
        }]
    }
};

jQuery(function ($) {
    "use strict";

    // Apply the theme
    Highcharts.setOptions(hcTheme);
});

function chart_subtitle(targetId, subTitle) {
    "use strict";

    $(targetId).highcharts().setTitle(null, {
        text: subTitle
    });
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
    var bandMaxValue;
    var plotValColor = [];

    $.each(serieNames, function (serie_num, serie_name) {
        // Position de chacune des gauges (horizontalement ou verticalement)
        gPos = (gStep / 2) + (gStep * serie_num);
        if ($("#chart0").height() <= $("#chart0").width()) {
            centerPos = [ // Horizontal
                gPos + '%',
                '50%'
            ];
            paneSize = Math.min($("#chart0").width() / 2 * 0.8, $("#chart0").height() * 0.75);
        } else {
            centerPos = [ // Vertical
                '50%',
                gPos + '%'
            ];
            paneSize = Math.min($("#chart0").width() * 0.8, $("#chart0").height() / 2 * 0.75);
        }
        // Seuils des gauges
        band_min = 0;
        plotBands = []; // RAZ
        $.each(data.bands[serie_name], function (band_max, band_color) {
            bandMaxValue = Math.min(band_max, data.seuils[serie_name].max);

            if ((band_min < data.data[serie_name]) && (bandMaxValue >= data.data[serie_name])) {
                // Série à afficher
                plotValColor[serie_name] = band_color;
            }

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
            tickInterval: data.seuils[serie_name].max / 10, // 10 graduations au total
            tickWidth: 2,
            tickLength: 15,
            tickPosition: 'inside',
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
            color : plotValColor[serie_name],
            yAxis: serie_num
        });
    });

    Highcharts.setOptions(hcTheme);

    return new Highcharts.Chart({
        chart: {
            renderTo: 'chart0',
            type: 'gauge',
            plotBackgroundColor: null,
            plotBackgroundImage: null,
            plotBorderWidth: 0,
            plotShadow: false,
            events: {
                load: function () {chart_loaded('#' + this.renderTo.id, data.subtitle, chart_subtitle); },
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
                return tooltip_chart0(this.series.index, this.point.x);
            }
        },
        pane: graphPanes,
        yAxis: graphYAxis,
        series: graphSeries
    });
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

    return new Highcharts.StockChart({
        chart: {
            renderTo: 'chart1',
            events: {
                load: function () {chart_loaded('#' + this.renderTo.id, data.subtitle, chart_subtitle); }
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
            crosshairs: true,
            useHTML: true,
            formatter: function () {
                //return tooltip_chart1(this.series.index, this.points[0].point.index); // pas avec highcharts 4
                return tooltip_chart1(0, this.points[0].point.index); // pas avec highcharts 4
                //var ptX = Math.max(0, nearest(this.points[0].series.xData, this.x).low);
                //return tooltip_chart1(/*this.series.index*/ 0, ptX);
            }
        },
        series: graphSeries,
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
    });
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
            zIndex: 1, // Passe derrière la courbe de la période précédente
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
                enabled: true,
                align: data.show3D ? 'left' : 'center',
                style: {
                    textShadow: 'none'
                }
            },
            visible: (data[serie_name + "_data"].reduce(function (a, b) { return a + b; }, 0) !== 0),
            showInLegend: (data[serie_name + "_data"].reduce(function (a, b) { return a + b; }, 0) !== 0)
        });
    });

    // Période précédente
    graphSeries.push({
        name : data.PREC_name,
        // En 3D, on remplace spline par scatter
        // Et on transforme la série en (x, y, z)
        data: ((data.PREC_type === 'spline') && (data.show3D)) ? data.PREC_data.map(function (a, b, c) { return a === null ? [b, 0, 1] : [b, a[1], 1]; }, 0) : data.PREC_data,
        zIndex: 2, // Passe devant l'histogramme de la période courante
        color : data.PREC_color,
        type: ((data.PREC_type === 'spline') && (data.show3D)) ? 'scatter' : data.PREC_type, // 'spline',
        lineWidth: 2, // scatter
        stack: 1, // 'previous',
        visible: (data.PREC_data.reduce(function (a, b) { return a + b; }, 0) !== 0),
        showInLegend: (data.PREC_data.reduce(function (a, b) { return a + b; }, 0) !== 0)
    });

    Highcharts.setOptions(hcTheme);

    return new Highcharts.Chart({
    //return {
        chart: {
            type: 'column', // Used for 3D representation (look at plotOptions)
            defaultSeriesType: 'column',
            //margin: [20, 0, 50, 0],
            options3d: {
                enabled: data.show3D,
                alpha: 15,
                beta: 15
                //depth: 50,
                //viewDistance: 15
            },
            renderTo: 'chart2',
            events: {
                load: function () {chart_loaded('#' + this.renderTo.id, data.subtitle, chart_subtitle); }
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
                groupZPadding: 20 // Spacing between columns on the z-axis
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
        zAxis: {
            min: 0,
            max: 50,
            labels: {
                enabled: false
            }
        },
        tooltip: {
            /*backgroundColor: {
                linearGradient: [0, 0, 0, 60],
                stops: [
                    [0, '#FFFFFF'],
                    [1, '#E0E0E0']
                ]
            },*/

            useHTML: true,
            formatter: function () {
                return tooltip_chart2(this.series.index, this.point.x);
            }
        },
        series: graphSeries,
        legend: {
            enabled: true
        }
    });
}

