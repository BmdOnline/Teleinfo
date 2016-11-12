// JSLint options
/*global console:false, document:false, $:false, jQuery:false, Option:false*/
/*jslint indent:4, todo:true, vars:true, unparam:true, newcap: true, nomen: true */

/* Plugins utilisés :
    splines : curvedLines
        https://github.com/MichaelZinsmaier/CurvedLines
    dashes
        https://github.com/cquartier/flot.dashes
    gauges
        https://github.com/toyoty99/flot.gauge
    animations : growraf
        https://github.com/thgreasi/growraf
    labels : valuelabels
        https://github.com/winne27/flot-valuelabels
    tooltip
        https://github.com/krzysu/flot.tooltip
    resize
        Nécessite : http://benalman.com/projects/jquery-resize-plugin/

   Plugins non utilisés :
   categories :
        http://www.flotcharts.org/flot/examples/categories/index.html
   axislabels :
        https://github.com/markrcote/flot-axislabels
   downsample :
        https://github.com/sveinn-steinarsson/flot-downsample

*/

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

var modFlot = (function () {
    "use strict";

    var defCSS = {
        chart: {
            height: "500px" // $("<div class='flot-chart' />").css("height")
        },
        gauge: {
            backgroundColor: $("<div class='flot-gauge' />").css("background-color"),
            borderColor: $("<div class='flot-gauge' />").css("border-top-color"),
            value: {
                fontColor: $("<div class='flot-gauge-value' />").css("color"),
                fontSize: 16, // a specified number, or 'auto'
                fontFamily: $("<div class='flot-gauge-value' />").css("font-family")
            },
            threshold: {
                fontColor: $("<div class='flot-gauge-threshold' />").css("color"),
                fontSize: 10, // a specified number, or 'auto'
                fontFamily: $("<div class='flot-gauge-threshold' />").css("font-family")
            }
        },
        label: {
            color: $("<div class='flot-label' />").css("color"),
            font: $("<div class='flot-label' />").css("font-style") + ' '  + $("<div class='flot-label' />").css("font-size") + ' ' + $("<div class='flot-label' />").css("font-family")
        }
    };

    var defOptions = {
        grid: {
            show: true,
            hoverable: true,
            clickable: true,
            margin: {
                top: 50,
                bottom: 30
            },
            borderWidth: 1
        },
        series: {
            canvasRender: true,
            hoverable: true,
            grow: {
                active: true,
                duration: 1000,
                growings: [{
                    stepDirection: "up"
                }]
            },
            labelPlacement: "above",
            shadowsize: 5,
            bars: {
                show: false,
                barWidth: 0.6,
                align: "center"
            },
            lines: {
                show: false
            },
            curvedLines: {
                active: true
            },
            points: {
                show: false,
                fill: true
            },
            valueLabels: {
                show: true,
                decimals: 1,
                align: 'center',
                valign: 'below',
                hideZero: true,
                useBackground : false,
                fontcolor: defCSS.label.color,
                font: defCSS.label.font
            }
        },
        yaxes: [{
            show: true,
            axisLabelUseCanvas: true,
            min: 0
        }],
        legend: {
            show: true,
            hoverable: true,
            clickable: true,
            position: "sw",
            noColumns: 3,
            margin: [0, -56]
        },
        tooltip: {
            show: true,
            defaultTheme: false,
            cssClass: 'flot-tooltip',
            onHover: function (flotItem, tooltipEl) {
                $(tooltipEl).css({
                    "border-color": flotItem.series.color
                });
            }
        }
    };

    jQuery(function ($) {
        // Do something here

    });

    // Plugin de gestion de titres
    // https://github.com/flot/flot/blob/master/PLUGINS.md
    (function ($) {
        var options = {
            /*title: {
                label: "Chart Title" //chart title
            }*/
            title: "Chart Title" //chart title
        };

        function init(plot, classes) {
            function draw(plot, canvascontext) {
                var t = plot.getOptions().title;
                var p = plot.getPlaceholder();

                if (p.children(".flot-text").length === 0) {
                    $("<div class='flot-text'></div>").prependTo(p);
                }

                var layer = p.children(".flot-text");
                //var layer = this.getTextLayer();

                if ($(".flot-title", layer).length === 0) {
                    $("<div class='flot-title'></div>").css({
                        position: "absolute",
                        width: "100%"
                    }).text(t).prependTo(layer);
                }
            }
            plot.hooks.draw.push(draw);
        }

        $.plot.plugins.push({
            init: init,
            options: options,
            name: "title",
            version: "1.0"
        });
    }(jQuery));

    // Plugin de gestion de la légende (clic pour affichier/marquer une série)
    // https://github.com/flot/flot/blob/master/PLUGINS.md
    (function ($) {
        var options = {
            series: {
                showInLegend: true
            },
            legend: {
                labelFormatter: function (label, series) {
                    // series is the series object for the label
                    // label is the original label
                    if (series.showInLegend) {
                        return label;
                    }
                    return null;
                }
            }
        };

        function init(plot, classes) {
            plot.legendItems = []; // Array with shown items

            function processOffset(plot, offset) {
                // Identifies series to display
                var chartData = plot.getData();
                $.each(chartData, function (serie_num, serie) {
                    if (serie.showInLegend) {
                        plot.legendItems.push(serie);
                    }
                });

                // May calculate grid margins, according to legend height
                offset.bottom += 10;
                //offset.bottom += 42;
            }

            function legendClick(event, plot) {
                var cellNum = ((event.target.closest("tr").rowIndex) * event.target.closest("table").rows[0].cells.length) + event.target.closest("td").cellIndex;
                var seriesIdx = Math.floor(cellNum / 2); // 2 = box + label
                var chartData = plot.getData();
                var serieName = plot.legendItems[seriesIdx].label;
                // On peut avoir deux séries avec le même nom (dans le cas d'une spline, qui utilise en réalité 2 séries).
                $.each(chartData, function (serie_num, serie) {
                    if (serie.label === serieName) {
                        if (chartData[serie_num].type === "column") {
                            chartData[serie_num].bars.show = !chartData[serie_num].bars.show;
                            chartData[serie_num].valueLabels.show = chartData[serie_num].bars.show;
                        } else if (chartData[serie_num].type === "spline") {
                            chartData[serie_num].lines.show = !chartData[serie_num].lines.show;
                        } else if (chartData[serie_num].type === "points") {
                            chartData[serie_num].points.show = !chartData[serie_num].points.show;
                        } else {
                            chartData[serie_num].lines.show = !chartData[serie_num].lines.show;
                            chartData[serie_num].points.show = chartData[serie_num].lines.show;
                        }
                    }
                });
                plot.setData(chartData);
                plot.draw();
            }

            function bindEvents(plot) {
                // Toggle serie display in legend
                plot.getPlaceholder().on('click', '.legendColorBox, .legendLabel', function (event) {
                    legendClick(event, plot);
                });
            }

            function shutdown(plot) {
                plot.getPlaceholder().unbind("click", legendClick);
            }

            plot.hooks.processOffset.push(processOffset);
            plot.hooks.bindEvents.push(bindEvents);
            plot.hooks.shutdown.push(shutdown);
        }

        $.plot.plugins.push({
            init: init,
            options: options,
            name: "legend toggle click",
            version: "1.0"
        });
    }(jQuery));

    // Plugin de reorganisation des span de la gauge dans flot-text
    // https://github.com/flot/flot/blob/master/PLUGINS.md
    (function ($) {
        var options = {
        };

        function init(plot, classes) {
            function draw(plot, canvascontext) {
                if (plot.getOptions().series.gauges.show) {
                    // Déplace les éléments de la gauge de type <span> dans le <div class="flot-text">
                    // Pour être plus cohérent avec la structure habituelle de flotcharts
                    var p = plot.getPlaceholder();

                    if (p.children(".flot-text").length === 0) {
                        $("<div class='flot-text'></div>").prependTo(p);
                    }

                    var layer = p.children(".flot-text");

                    $.each(p.children("span"), function (child_num, child) {
                        // Gague instead of Gauge. Typo error ?
                        if (child.id.substring(0, 9) === "flotGague") {
                            $(child).detach().appendTo(layer);
                        }
                    });
                }
            }
            plot.hooks.draw.push(draw);
        }

        $.plot.plugins.push({
            init: init,
            options: options,
            name: "gauge flot-text",
            version: "1.0"
        });
    }(jQuery));

    function init_chart0(data, serie) {
        // Préparation des séries à afficher (nombre de gauges)
        var serieNames = [];
        if (serie !== undefined) {
            serieNames.push(serie);
        } else {
            $.each(data.series, function (serie_name, serie_title) {
                serieNames.push(serie_name);
            });
        }

        // Préparation des séries de données
        var plotBands = [];
        var graphSeries = [];

        // Période courante
        $.each(serieNames, function (serie_num, serie_name) {
            // Seuils des gauges
            plotBands = []; // RAZ
            $.each(data.bands[serie_name], function (band_max, band_color) {
                plotBands.push({
                    value: Math.min(band_max, data.seuils[serie_name].max),
                    color: band_color
                });
            });

            graphSeries.push({
                label : data.series[serie_name],
                data : [[0, data.data[serie_name]]],
                gauges: {
                    gauge: {
                        min: data.seuils[serie_name].min,
                        max: data.seuils[serie_name].max
                    },
                    threshold: {
                        values: plotBands
                    }
                }
            });
        });

        var graphOptions = $.extend(true, {}, defOptions, {
            title: data.title,
            hooks: {
                //processOptions: [function (plot, options) {
                bindEvents: [function (plot, eventHolder) {
                    // Nothing to bind, just display loading time
                    chart_loaded(plot.getPlaceholder().selector, data.subtitle);
                }]
            },
            tooltip: {
                content: function (label, xval, yval, flotItem) {
                    return tooltip_chart0(flotItem.seriesIndex, flotItem.dataIndex);
                }
            },
            series: {
                gauges: {
                    debug: {
                        log: false,
                        layout: false
                    },
                    show: true,
                    frame: { // Cadre général
                        show: false
                    },
                    cell: { // Cadre de chaque gauge
                        background: {
                            color: null
                        },
                        border: {
                            show: false,
                            color: "transparent",
                            width: 1
                        },
                        margin: 5,
                        vAlign: "middle" // 'top' or 'middle' or 'bottom'
                    },
                    gauge: {
                        width: "auto", // a specified number, or 'auto'
                        //startAngle: 1, // 0 - 2 factor of the radians
                        //endAngle: 2, // 0 - 2 factor of the radians
                        background: {
                            color: defCSS.gauge.backgroundColor
                        },
                        border: {
                            color: defCSS.gauge.borderColor,
                            width: 2
                        },
                        shadow: {
                            show: true,
                            blur: 5
                        }
                    },
                    label: { // Titre de chaque gauge
                        show: false
                    },
                    value: { // Valeur centrale
                        show: true,
                        margin: "auto",
                        background: {
                            color: null,
                            opacity: 0
                        },
                        font: {
                            size: defCSS.gauge.value.fontSize,
                            family: defCSS.gauge.value.fontFamily
                        },
                        color: defCSS.gauge.value.fontColor,
                        formatter: function (label, value) {
                            return parseInt(value, 0) + " " + label;
                        }
                    },
                    threshold: {
                        show: true,
                        width: "auto",
                        label: {
                            show: true,
                            margin: "auto",
                            background: {
                                color: null,
                                opacity: 0
                            },
                            font: {
                                size: defCSS.gauge.threshold.fontSize,
                                family: defCSS.gauge.threshold.fontFamily
                            },
                            color: defCSS.gauge.threshold.fontColor,
                            formatter: function (value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        if ($("#chart0").height() <= 0) {
            $("#chart0").height(defCSS.chart.height);
        }
        return $.plot("#chart0", graphSeries, graphOptions);
    }

    function init_chart1(data) {
        $('#chart1').on({
            plotselected: function (event, ranges) {
                // Gestion du zoom
                $.each(chart_elec1.getXAxes(), function (axis_num, axis) {
                    var opts = axis.options;
                    opts.min = ranges.xaxis.from;
                    opts.max = ranges.xaxis.to;
                });

                chart_elec1.setupGrid();
                chart_elec1.draw();
                chart_elec1.clearSelection();
            },
            dblclick: function (event) {
                // RAZ du zoom
                $.each(chart_elec1.getXAxes(), function (axis_num, axis) {
                    var opts = axis.options;
                    opts.min = axis.datamin;
                    opts.max = axis.datamax;
                });

                chart_elec1.setupGrid();
                chart_elec1.draw();
                chart_elec1.clearSelection();
            }
        });

        // Préparation des séries de données
        var graphSeries = [];

        // Période courante
        var showSerie;
        $.each(data.series, function (serie_name, serie_title) {
            showSerie = data[serie_name + "_data"].reduce(function (a, b) { return a + b[1]; }, 0) !== 0;

            graphSeries.push({
                label : data[serie_name + "_name"],
                data : data[serie_name + "_data"],
                color : data[serie_name + "_color"],
                type: 'spline', // Special : keep type for serie toggling
                bars: {
                    show: false
                },
                lines: {
                    show: showSerie,
                    fill: true,
                    lineWidth: 2
                },
                points: {
                    show: false
                },
                xaxis: 1,
                valueLabels: {
                    show: false
                },
                show: showSerie,
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
            label : data.PREC_name,
            data : data.PREC_data,
            color : data.PREC_color,
            type: 'spline', // Special : keep type for serie toggling
            bars: {
                show: false
            },
            lines: {
                show: true,
                lineWidth: 2
            },
            points: {
                show: false
            },
            xaxis: 2,
            valueLabels: {
                show: false
            },
            show: (data.PREC_data.reduce(function (a, b) { return a + b; }, 0) !== 0),
            showInLegend: (data.PREC_data.reduce(function (a, b) { return a + b; }, 0) !== 0)
        });

        // Min / Max
        graphSeries.push({
            grow: {
                active: false
            },
            label : "min. " + data.seuils.min + "w",
            data : [[data.navigator[0][0], data.seuils.min], [data.navigator[data.navigator.length - 1][0], data.seuils.min]],
            color : data.MIN_color,
            type: 'dashes', // Special : keep type for serie toggling
            dashes: {
                show: true,
                lineWidth: 1
            },
            xaxis: 1,
            valueLabels: {
                labelFormatter: function (v) {
                    return 'min. ' + v + 'w';
                },
                decimals: 0,
                align: 'center',
                valign: 'below',
                showLastValue: true,
                fontcolor: data.MIN_color,
                useBackground: false
            },
            show: true,
            showInLegend: false
        }, {
            grow: {
                active: false
            },
            label : "max. " + data.seuils.max + "w",
            data : [[data.navigator[0][0], data.seuils.max], [data.navigator[data.navigator.length - 1][0], data.seuils.max]],
            color : data.MAX_color,
            type: 'dashes', // Special : keep type for serie toggling
            dashes: {
                show: true,
                lineWidth: 1
            },
            xaxis: 1,
            valueLabels: {
                labelFormatter: function (v) {
                    return 'max. ' + v + 'w';
                },
                decimals: 0,
                align: 'center',
                valign: 'above',
                showLastValue: true,
                fontcolor: data.MAX_color,
                useBackground: false
            },
            show: true,
            showInLegend: false
        });

        var graphOptions = $.extend(true, {}, defOptions, {
            title: data.title,
            hooks: {
                //processOptions: [function (plot, options) {
                bindEvents: [function (plot, eventHolder) {
                    // Nothing to bind, just display loading time
                    chart_loaded(plot.getPlaceholder().selector, data.subtitle);
                }]
            },
            tooltip: {
                content: function (label, xval, yval, flotItem) {
                    return tooltip_chart1(flotItem.seriesIndex, flotItem.dataIndex);
                }
            },
            xaxes: [{
                show: true,
                position: "bottom",
                mode: "time",
                timeformat: "%H:%M",
                min: data.navigator[0][0],
                max: data.navigator[data.navigator.length - 1][0]
            }, {
                show: false,
                position: "top",
                mode: "time",
                timeformat: "%H:%M",
                min: data.navigator[0][0],
                max: data.navigator[data.navigator.length - 1][0]
            }],
            yaxes: [{
                axisLabel: "Watt"
            }],
            selection: {
                mode: "x"
            }
        });

        if ($("#chart1").height() <= 0) {
            $("#chart1").height(defCSS.chart.height);
        }
        return $.plot("#chart1", graphSeries, graphOptions);
    }

    function init_chart2(data) {
        // Préparation des séries de données
        var graphSeries = [];

        // Période courante
        var axisTicks = data.categories.map(function (a, b, c) { return a === null ? [b, 0] : [b, a]; }, 0);
        var showSerie;
        $.each(data.series, function (serie_name, serie_title) {
            showSerie = data[serie_name + "_data"].reduce(function (a, b) { return a + b; }, 0) !== 0;

            graphSeries.push({
                label : serie_title,
                data : data[serie_name + "_data"].map(function (a, b, c) { return a === null ? [b, 0] : [b, a]; }, 0),
                color : data[serie_name + "_color"],
                type: data[serie_name + "_type"], // Special : keep type for serie toggling
                bars: {
                    show: (data[serie_name + "_type"] === 'column') && showSerie
                },
                stack: true, // 'histo',
                /*events: {
                    click: function (e) {
                        var newdate = new Date();
                        newdate.setTime(data.debut);
                        newdate.setDate(newdate.getDate() + e.point.x);
                        console.log(newdate);
                    }
                },*/
                xaxis: 1,
                valueLabels: {
                    show: true
                },
                showInLegend: (data[serie_name + "_data"].reduce(function (a, b) { return a + b; }, 0) !== 0)
            });
        });

        // Période précédente
        var axisPREC = data.PREC_data.map(function (a, b, c) { return a === null ? [b, 0] : [b, a[0]]; }, 0);
        showSerie = data.PREC_data.reduce(function (a, b) { return a + b; }, 0) !== 0;
        graphSeries.push({
            label : data.PREC_name,
            data: data.PREC_data.map(function (a, b, c) { return a === null ? [b, 0] : [b, a[1]]; }, 0),
            color : data.PREC_color,
            type: data.PREC_type, // Special : keep type for serie toggling
            bars: {
                show: (data.PREC_type === 'column') && showSerie
            },
            lines: {
                show: (data.PREC_type !== 'column') && showSerie,
                lineWidth: 3
            },
            curvedLines: {
                apply: (data.PREC_type === 'spline') && showSerie,
                tension: 0.5
            },
            points: {
                show: (data.PREC_type === 'line') && showSerie,
                fillColor: data.PREC_color
            },
            hoverable: (data.PREC_type !== 'spline'),
            stack: false, // 'previous',
            xaxis: 2,
            valueLabels: {
                show: false
            },
            showInLegend: (data.PREC_data.reduce(function (a, b) { return a + b; }, 0) !== 0)
        });
        if ((data.PREC_type === 'spline') && showSerie) {
            graphSeries.push({
                label : data.PREC_name,
                data: data.PREC_data.map(function (a, b, c) { return a === null ? [b, 0] : [b, a[1]]; }, 0),
                type: data.PREC_type === 'spline' ? 'points' : data.PREC_type, // Special : keep type for serie toggling
                color : data.PREC_color,
                points: {
                    show: true,
                    fillColor: data.PREC_color
                },
                xaxis: 2,
                valueLabels: {
                    show: false
                },
                showInLegend: false
            });
        }

        var graphOptions = $.extend(true, {}, defOptions, {
            title: data.title,
            hooks: {
                //processOptions: [function (plot, options) {
                bindEvents: [function (plot, eventHolder) {
                    // Nothing to bind, just display loading time
                    chart_loaded(plot.getPlaceholder().selector, data.subtitle);
                }]
            },
            tooltip: {
                content: function (label, xval, yval, flotItem) {
                    return tooltip_chart2(flotItem.seriesIndex, flotItem.dataIndex);
                }
            },
            xaxes: [{
                show: true,
                position: "bottom",
                ticks: axisTicks
                //autoscaleMargin: .02
            }, {
                show: false,
                position: "top",
                ticks: axisPREC,
                alignTicksWithAxis: 1,
                // Align with axis 1, using min and max values
                min: -0.3,
                max: axisPREC.length - 0.7
            }],
            yaxes: [{
                axisLabel: "kWh"
            }]
        });

        if ($("#chart2").height() <= 0) {
            $("#chart2").height(defCSS.chart.height);
        }
        return $.plot("#chart2", graphSeries, graphOptions);
    }

    return {
        init_chart0: init_chart0,
        init_chart1: init_chart1,
        init_chart2: init_chart2
    };
}());