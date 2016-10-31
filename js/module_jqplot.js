// JSLint options
/*global window:false, $:false, jQuery:false, setInterval:false, clearInterval:false, setTimeout:false, clearTimeout:false, Chart:false*/
/*jslint indent:4, todo:true, vars:true, unparam:true, newcap: true, nomen: true */

// Fonctions et variables externes
var start;
var chart_elec0;
var chart_elec1;
var chart_elec2;
var chart_loaded;
var refresh_chart0;
var refresh_chart1;
var refresh_chart2;
var tooltip_chart0;
var tooltip_chart1;
var tooltip_chart2;

var modJQPlot = (function () {
    "use strict";

    var defOptions = {
        // Only animate if we're not using excanvas (not in IE 7 or IE 8)..
        animate: !$.jqplot.use_excanvas,
        animateReplot: true,
        seriesDefaults: {
            renderer: $.jqplot.BarRenderer,
            rendererOptions: {
                smooth: true,
                barMargin: 40,
                animation: {
                    speed: 1000
                }
            },

            lineWidth: 2,
            fill: true,
            fillAndStroke: true,
            fillAlpha: 0.7,

            shadow: true,
            smooth: true,
            showMarker: false,

            //highlightMouseOver: true,
            pointLabels: {
                show: false
            }
        },
        grid: {
            shadow: false,
            background: 'transparent',
            drawBorder: false
        },
        highlighter: {
            show: true,
            showLabel: false,
            showMarker: true,
            showTootip: true,
            tooltipAxes: 'y',
            tooltipLocation: 'se'
        },
        legend: {
            show: true,
            renderer: $.jqplot.EnhancedLegendRenderer,
            rendererOptions: {
                numberRows: 1,
                seriesToggle: true
            },
            location: 's',
            placement: 'outsideGrid'
        }
    };

    jQuery(function ($) {
        $.jqplot.postDrawHooks.push(function () {
            /*if (this._drawCount !== 0) { // First time only
                start = 0;
            }*/
            chart_loaded(this.targetId === '#chart0_gauge0' ? '#chart0' : this.targetId, this.title.subtitle); //plot.getPlaceholder().selector)

            /*var timer;
            $(window).resize(function () {
                // Wait 100ms before redrawingh charts
                clearTimeout(timer);
                timer = setTimeout(function () {
                    if (($('#chart0').is(":visible")) && (chart_elec0 !== undefined)) {
                        $.each(chart_elec0, function (gauge_num, chart_gauge) {
                            chart_gauge.replot({resetAxes: true});
                        });
                    }
                    if (($('#chart1').is(":visible")) && (chart_elec1 !== undefined)) {
                        chart_elec1.replot({resetAxes: true});
                    }
                    if (($('#chart2').is(":visible")) && (chart_elec2 !== undefined)) {
                        chart_elec2.replot({resetAxes: true});
                    }
                }, 100);
            });*/
        });

        //$.jqplot.eventListenerHooks.push(['jqplotDblClick', chart_dblclicked]);
    });

    /*function chart_dblclicked(ev, gridpos, datapos, neighbor, plot) {
        plot.resetZoom();
    }*/

    function init_chart0(data, serie) {
        var chart0_gauges = [];
        var graphData = [];
        var graphOptions;

        // Préparation des séries à afficher (nombre de gauges)
        var serieNames = [];
        if (serie !== undefined) {
            serieNames.push(serie);
        } else {
            $.each(data.series, function (serie_name, serie_title) {
                serieNames.push(serie_name);
            });
        }

        // Titre (centré)
        if ($('#chart0_title').length === 0) {
            $('#chart0').prepend('<div class="jqplot-title" id="chart0_title"></div>');
        }
        $('#chart0_title').html(data.title);

        // Eléments du graphique
        var plotBandsInt = [];
        var plotBandsCol = [];
        $.each(serieNames, function (serie_num, serie_name) {
            // Ajoute un "div" pour chacune des gauges
            if ($("#chart0_gauge" + serie_num).length === 0) {
                $("#chart0").append('<div class="chart_gauge' + (serie_num % 2) + '" id="chart0_gauge' + serie_num + '"></div>');
            }

            // Seuils des gauges
            plotBandsInt = []; // RAZ
            plotBandsCol = []; // RAZ
            $.each(data.bands[serie_name], function (band_max, band_color) {
                plotBandsInt.push(
                    Math.min(band_max, data.seuils[serie_name].max)
                );
                plotBandsCol.push(
                    band_color
                );
            });

            graphData = [[data.data[serie_name]]];
            graphOptions = $.extend(true, {}, defOptions, {
                title: {
                    //text: serie_num === 0 ? data.title : '',
                    subtitle: data.subtitle  // Custom property used with postDrawHooks event
                },
                seriesDefaults: {
                    renderer: $.jqplot.MeterGaugeRenderer,
                    rendererOptions: {
                        //padding: 0,
                        //ringWidth: 1,
                        //ringColor: 'black',
                        label: data.data[serie_name] + ' '  + data.series[serie_name],
                        labelPosition: 'bottom',
                        labelHeightAdjust: -7,
                        min: data.seuils[serie_name].min,
                        max: data.seuils[serie_name].max,
                        intervals: plotBandsInt,
                        intervalColors: plotBandsCol
                    }
                },
                legend: {
                    show: false,
                    placement: "insideGrid"
                },
                highlighter: {
                    tooltipContentEditor: function (str, seriesIndex, pointIndex, jqPlot) {
                        return tooltip_chart0(seriesIndex, pointIndex);
                    }
                }
            });

            chart0_gauges.push(
                $.jqplot('chart0_gauge' + serie_num, graphData, graphOptions)
            );
        });

        // Animate gauges
        $.each(serieNames, function (serie_num, serie_name) {
            var current = 0;
            var maximum = data.data[serie_name];
            var step = maximum / 10;
            var timer = setInterval(function () {
                if (current > maximum) {
                    clearInterval(timer);
                    return;
                }
                chart0_gauges[serie_num].series[0].data[0] = [1, current];
                chart0_gauges[serie_num].replot();
                current += step;
            }, 1);
        });

        return chart0_gauges;
    }

    function init_chart1(data) {
        // Préparation des séries de données
        var graphSeries = [];
        var graphData = [];

        // Période courante
        $.each(data.series, function (serie_name, serie_title) {
            graphData.push(
                //data[serie_name + "_data"]
                // On a un souci avec les séries interrompues, genre HP / HC
                data[serie_name + "_data"].map(function (val, i) { return val[1] === null ? [val[0], 0] : val; })
            );
            graphSeries.push({
                label: data[serie_name + "_name"],
                color: data[serie_name + "_color"],
                renderer: $.jqplot.LineRenderer,
                show: (data[serie_name + "_data"].reduce(function (a, b) { return a + b[1]; }, 0) !== 0),
                showLabel: (data[serie_name + "_data"].reduce(function (a, b) { return a + b[1]; }, 0) !== 0)
            });
        });

        // Intensité
        /*graphSeries.push({
            name: data.I_name,
            data: data.I_data,
            type: 'spline',
            width: 1,
            shape: 'squarepin',
            yAxis: 1,
        });*/

        // Période précédente
        graphData.push(
            data.PREC_data
        );
        graphSeries.push({
            label: data.PREC_name,
            color: data.PREC_color,
            fill: false,
            renderer: $.jqplot.LineRenderer,
            xaxis: 'x2axis',
            show: (data.PREC_data.reduce(function (a, b) { return a + b; }, 0) !== 0),
            showLabel: (data.PREC_data.reduce(function (a, b) { return a + b; }, 0) !== 0)
        });

        var graphOptions = $.extend(true, {}, defOptions, {
            title: {
                text: data.title,
                subtitle: data.subtitle  // Custom property used with postDrawHooks event
            },
            seriesDefaults: {
                shadow: false
            },
            axes: {
                xaxis: {
                    renderer: $.jqplot.DateAxisRenderer,
                    tickOptions: {
                        formatString: '%H:%M'
                    },
                    min: data.navigator[0][0],
                    max: data.navigator[data.navigator.length - 1][0],
                    ticks: data.categories
                    //tickInterval:'6 hour'
                },
                x2axis: {
                    renderer: $.jqplot.DateAxisRenderer,
                    show: false,
                    showTicks: false,
                    tickOptions: {
                        formatString: '%H:%M'
                    },
                    min: data.navigator[0][0],
                    max: data.navigator[data.navigator.length - 1][0]
                    //tickInterval:'6 hour'
                },
                yaxis: {
                    label: "Watt",
                    min: 0,
                    //numberTicks: 5,
                    tickInterval: 1500,
                    //pad: 1,
                    labelRenderer: $.jqplot.CanvasAxisLabelRenderer
                }
            },
            canvasOverlay: {
                show: true,
                objects: [
                    {dashedHorizontalLine: {
                        lineCap: 'butt',
                        y: data.seuils.min,
                        lineWidth: 2,
                        xOffset: 0,
                        color: data.MIN_color,
                        shadow: true,
                        showTooltip: true,
                        tooltipLocation: 'ne',
                        tooltipFormatString: '<span style="color:' + data.MIN_color + '"><b>min. ' + data.seuils.min + 'w</span>'
                    }},
                    {dashedHorizontalLine: {
                        lineCap: 'butt',
                        y: data.seuils.max,
                        lineWidth: 2,
                        xOffset: 0,
                        color: data.MAX_color,
                        shadow: true,
                        showTooltip: true,
                        tooltipLocation: 'se',
                        tooltipFormatString: '<span style="color:' + data.MAX_color + '"><b>max. ' + data.seuils.max + 'w</span>'
                    }}
                ]
            },
            cursor: {
                show: true,
                zoom: true,
                dblClickReset: true,
                showTooltip: true,
                constrainZoomTo: 'x'
            },
            highlighter: {
                tooltipContentEditor: function (str, seriesIndex, pointIndex, jqPlot) {
                    return tooltip_chart1(seriesIndex, pointIndex);
                }
            },
            series: graphSeries
        });

        return $.jqplot('chart1', graphData, graphOptions);
    }

    function init_chart2(data) {
        // Préparation des séries de données
        var graphData = [];
        var graphSeries = [];

        // Période courante
        $.each(data.series, function (serie_name, serie_title) {
            graphData.push(
                data[serie_name + "_data"].map(function (a) { return a === null ? 0 : a; }, 0)
                //data[serie_name + "_data"]
            );
            graphSeries.push({
                label: serie_title,
                color: data[serie_name + "_color"],
                fill: (data[serie_name + "_type"] === 'column') ? false : true,
                renderer: (data[serie_name + "_type"] === 'column') ? ($.jqplot.BarRenderer) : ($.jqplot.LineRenderer),
                disableStack: false, // 'histo',
                xaxis: 'xaxis',
                pointLabels: {
                    show: true,
                    hideZeros: true,
                    formatString: "%#.2f"
                    //location:'n'
                }
                //show: (data[serie_name + "_data"].reduce(function (a, b) { return a + b; }, 0) !== 0),
                //showLabel: (data[serie_name + "_data"].reduce(function (a, b) { return a + b; }, 0) !== 0)
            });
            // show: false ne marche pas avec des séries empilées !
            // showLabel: false ne marche pas avec $.jqplot.EnhancedLegendRenderer !
        });

        // Période précédente
        var dataPREC = data.PREC_data.map(function (a) { return a === null ? 0 : a[1]; }, 0);
        var axisPREC = data.PREC_data.map(function (a) { return a === null ? "" : a[0]; }, 0);
        graphData.push(
            //data.PREC_data
            dataPREC
        );
        graphSeries.push({
            label: data.PREC_name,
            color: data.PREC_color,
            fill: false,
            renderer: (data.PREC_type === 'column') ? ($.jqplot.BarRenderer) : ($.jqplot.LineRenderer),
            disableStack: true, // 'previous',
            xaxis: 'x2axis',
            showMarker: true
            //show: (data.PREC_data.reduce(function (a, b) { return a + b; }, 0) !== 0),
            //showLabel: (data.PREC_data.reduce(function (a, b) { return a + b; }, 0) !== 0)
        });

        var graphOptions = $.extend(true, {}, defOptions, {
            title: {
                text: data.title,
                subtitle: data.subtitle  // Custom property used with postDrawHooks event
            },
            axes: {
                xaxis: {
                    renderer: $.jqplot.CategoryAxisRenderer,
                    ticks: data.categories.map(function (a) { return a === null ? "" : a; }, 0)
                },
                x2axis: {
                    renderer: $.jqplot.CategoryAxisRenderer,
                    show: false,
                    showTicks: false,
                    ticks: axisPREC
                },
                yaxis: {
                    label: "kWh",
                    min: 0,
                    labelRenderer: $.jqplot.CanvasAxisLabelRenderer
                }
            },
            highlighter: {
                showMarker: false,
                tooltipContentEditor: function (str, seriesIndex, pointIndex, jqPlot) {
                    return tooltip_chart2(seriesIndex, pointIndex);
                }
            },
            stackSeries: true,
            series: graphSeries
        });

        return $.jqplot('chart2', graphData, graphOptions);
    }

    return {
        init_chart0: init_chart0,
        init_chart1: init_chart1,
        init_chart2: init_chart2
    };
}());