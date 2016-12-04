// JSLint options
/*global $:false, jQuery:false, Chart:false, Color:false*/
/*jslint indent:4, todo:true, vars:true, unparam:true, newcap: true, nomen: true */

/* Plugins utilisés :
    zoom
        https://github.com/chartjs/chartjs-plugin-zoom
    lignes : annotations
        https://github.com/chartjs/chartjs-plugin-annotation

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

var modChartJS = (function () {
    "use strict";

    /*var defNoCSS = {
        title: {
            fontColor: '#333333',
            fontWeight:'bold',
            fontSize: 1
        },
        tooltip: {
            opacity: 0.7,
            backgroundColor: '#C0C0C0',
            titleFontSize: 12,
            bodyFontSize: 12,
            footerFontSize: 12,
            titleFontColor: '#333333',
            bodyFontColor: '#333333',
            footerFontColor: '#333333'
        },
        gauge: {
            value: {
                fontColor: '#666666',
                fontFamily: 'Verdana, sans-serif',
                fontStyle: 'normal',
                fontSize: null
            }
        },
        label: {
            fontColor: '#FFFFFF',
            fontStyle: 'normal',
            fontFamily: 'Verdana, sans-serif',
            fontSize: 11
        }
    };
    var defCSS = {
        title: {
            fontColor: $("<div class='chartjs-title' />").css("color"),
            fontWeight: $("<div class='chartjs-title' />").css("font-weight"),
            fontSize: parseInt($("<div class='chartjs-title' />").css("font-size")) // 20
        },
        tooltip: {
            opacity: 0.7,
            backgroundColor: $("<div class='chartjs-tooltip' />").css("background-color"),
            titleFontSize: parseInt($("<div class='chartjs-tooltip' />").css("font-size")), // 12
            bodyFontSize: parseInt($("<div class='chartjs-tooltip' />").css("font-size")), // 12
            footerFontSize: parseInt($("<div class='chartjs-tooltip' />").css("font-size")), // 12
            titleFontColor: $("<div class='chartjs-tooltip' />").css("color"),
            bodyFontColor: $("<div class='chartjs-tooltip' />").css("color"),
            footerFontColor: $("<div class='chartjs-tooltip' />").css("color"),
        },
        gauge: {
            value: {
                fontColor: $("<div class='chartjs-gauge' />").css("color"),
                fontFamily: $("<div class='chartjs-gauge' />").css("font-family"),
                fontStyle: $("<div class='chartjs-gauge' />").css("font-style"),
                fontSize: parseInt($("<div class='chartjs-gauge' />").css("font-size")) // null
            }
        },
        label: {
            fontColor: $("<div class='chartjs-label' />").css("color"),
            fontStyle: $("<div class='chartjs-label' />").css("font-style"),
            fontFamily: $("<div class='chartjs-label' />").css("font-family"),
            fontSize: parseInt($("<div class='chartjs-label' />").css("font-size")) // 11
        }
    };*/

    var defCSS;
    var defOptions;

    // Affichage du chrono et des informations complémentaires (subtitle) lors du chargement
    var chartLoadedPlugin = {
        /*  ...
            options: {
                subtitle: 'caption'
            }
        */
        //beforeUpdate: function (chart) {
        afterUpdate: function (chart) {
        //afterDraw: function (chart) {
            var subtitle = chart.config.options.subtitle;
            var chartID = '#' + chart.chart.canvas.parentNode.id;

            chart_loaded(chartID, subtitle);
            //chart.config.options.animation.onComplete = function (animation) {
            //    chart_loaded(chartID, subtitle);
            //}

        }
    };

    // Affiche la valeur au centre du doughnut
    var doughnutCenterValuePlugin = {
        // https://github.com/chartjs/Chart.js/issues/78#issuecomment-220829079
        /*  ...
            options: {
                elements: {
                    center: {
                        // the longest text that could appear in the center
                        maxText: '100%',
                        text: '90%',
                        fontColor: '#36A2EB',
                        fontFamily: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
                        fontStyle: 'normal',
                        // fontSize: 12,
                        // if a fontSize is NOT specified, we will scale (within the below limits) maxText to take up the maximum space in the center
                        // if these are not specified either, we default to 1 and 256
                        minFontSize: 1,
                        maxFontSize: 256,
                    }
                }
            }
        }; */
        afterUpdate: function (chart) {
            if ((chart.config.type === 'doughnut') && (chart.config.options.elements.center)) {
                var helpers = Chart.helpers;
                var centerConfig = chart.config.options.elements.center;
                var globalConfig = Chart.defaults.global;
                var ctx = chart.chart.ctx;

                var fontStyle = helpers.getValueOrDefault(centerConfig.fontStyle, globalConfig.defaultFontStyle);
                var fontFamily = helpers.getValueOrDefault(centerConfig.fontFamily, globalConfig.defaultFontFamily);
                var fontSize;

                if (centerConfig.fontSize) {
                    fontSize = centerConfig.fontSize;
                } else {
                    // figure out the best font size, if one is not specified
                    ctx.save();
                    fontSize = helpers.getValueOrDefault(centerConfig.minFontSize, 1);
                    var maxFontSize = helpers.getValueOrDefault(centerConfig.maxFontSize, 256);
                    var maxText = helpers.getValueOrDefault(centerConfig.maxText, centerConfig.text);
                    var textWidth;
                    /*do {
                        ctx.font = helpers.fontString(fontSize, fontStyle, fontFamily);
                        textWidth = ctx.measureText(maxText).width;

                        // check if it fits, is within configured limits and that we are not simply toggling back and forth
                        if (textWidth < chart.innerRadius * 2 && fontSize < maxFontSize) {
                            fontSize += 1;
                        } else {
                            // reverse last step
                            fontSize -= 1;
                            break;
                        }
                    } while (true);*/
                    do {
                        ctx.font = helpers.fontString(fontSize, fontStyle, fontFamily);
                        textWidth = ctx.measureText(maxText).width;

                        fontSize += 1;
                    // check if it fits, is within configured limits and that we are not simply toggling back and forth
                    } while (textWidth < chart.innerRadius * 2 && fontSize < maxFontSize);
                    // reverse last step
                    fontSize -= 1;
                    ctx.restore();
                }

                // save properties
                chart.center = {
                    font: helpers.fontString(fontSize, fontStyle, fontFamily),
                    fillStyle: helpers.getValueOrDefault(centerConfig.fontColor, globalConfig.defaultFontColor)
                };
            }
        },
        afterDraw: function (chart) {
            if (chart.center) {
                var centerConfig = chart.config.options.elements.center;
                var ctx = chart.chart.ctx;

                ctx.save(); // Save drawing state of the context (strokeStyle, lineWidth, etc...)
                ctx.font = chart.center.font;
                ctx.fillStyle = chart.center.fillStyle;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                var centerX = (chart.chartArea.left + chart.chartArea.right) / 2;
                var centerY = (chart.chartArea.top + chart.chartArea.bottom) / 2;
                //ctx.fillText(centerConfig.text, centerX, centerY);
                $.each(centerConfig.text.split('\n'), function (key, line) {
                    ctx.fillText(line, centerX, centerY);
                    centerY += ctx.measureText("m").width; // 48;
                });
                ctx.restore(); // Restore drawing state of the context (strokeStyle, lineWidth, etc...)
            }
        }
    };

    // Affiche les valeurs sur les barres d'histogramme
    function barLabelPlugin() {
        // Surcharge de Chart.elements.Rectangle.draw

        // https://github.com/chartjs/Chart.js/issues/2321
        // https://github.com/chartjs/Chart.js/issues/327
        /*  ...
            options: {
                valueLabels: {
                    show: true,
                    decimals: 1,
                    padding: 10,
                    align: 'center',
                    valign: 'top',
                    hideZero: true,
                    fontColor: '#36A2EB',
                    fontSize: '16px',
                    fontStyle: 'normal',
                    fontFamily: 'Verdana, sans-serif',
                }
            }
        }; */

        function drawLabel() {
            if ((this._chart.config.type === 'bar') && (this._chart.config.options.valueLabels)) {
                var labelsConfig = this._chart.config.options.valueLabels;
                if (labelsConfig.show) {
                    var text = this._chart.config.data.datasets[this._datasetIndex].data[this._index];
                    if ((text !== 0) || (!labelsConfig.hideZero)) {
                        var globalConfig = Chart.defaults.global;
                        var helpers = Chart.helpers;
                        var ctx = this._chart.ctx;
                        var vm = this._view;

                        //console.log('View #' + this._index + ' x:' + vm.x + ' y:' + vm.y +' w:' + vm.width + ' base:' + vm.base);

                        var fontColor = helpers.getValueOrDefault(labelsConfig.fontColor, globalConfig.defaultFontColor);
                        var fontSize = helpers.getValueOrDefault(labelsConfig.fontSize, globalConfig.defaultfontSize);
                        var fontStyle = helpers.getValueOrDefault(labelsConfig.fontStyle, globalConfig.defaultFontStyle);
                        var fontFamily = helpers.getValueOrDefault(labelsConfig.fontFamily, globalConfig.defaultFontFamily);
                        var labelFont = helpers.fontString(fontSize, fontStyle, fontFamily);
                        var padding = helpers.getValueOrDefault(labelsConfig.padding, 10);
                        var textAlign = helpers.getValueOrDefault(labelsConfig.align, "center");
                        var textVAlign = helpers.getValueOrDefault(labelsConfig.valign, "top");
                        var decimals = helpers.getValueOrDefault(labelsConfig.decimals, null);

                        var xPos = vm.x;
                        if (textAlign === "left") {
                            xPos -= vm.x - (vm.width / 2 - padding);
                        } else if (textAlign === "right") {
                            xPos += vm.x - (vm.width / 2 - padding);
                        } else { //if (textAlign === "center")
                            xPos = vm.x;
                        }

                        var yPos;
                        if (textVAlign === "middle") {
                            yPos = (vm.base + vm.y) / 2;
                        } else if (textVAlign === "bottom") {
                            yPos = vm.base - padding;
                        } else { //if (textVAlign === "top")
                            yPos = vm.y + padding;
                        }

                        if (decimals !== null) {
                            text = text.toFixed(decimals);
                        }

                        ctx.save();
                        ctx.fillStyle = fontColor;
                        ctx.font = labelFont;
                        ctx.textAlign = textAlign;
                        ctx.textBaseline = textVAlign;
                        ctx.fillText(text, xPos, yPos);
                        ctx.restore();
                    }
                }
            }
        }

        return {
            beforeInit: function (chart) {
                Chart.elements.RectangleWithLabel = Chart.elements.Rectangle.extend({
                    draw: function () {
                        Chart.elements.Rectangle.prototype.draw.apply(this, arguments);
                        drawLabel.apply(this, arguments);
                    }
                });
                Chart.controllers.bar = Chart.controllers.bar.extend({
                    dataElementType: Chart.elements.RectangleWithLabel
                });
            }
        };
    }

    jQuery(function ($) {
        // Do something here

        // Create dummy containers
        $('<div id="chart-css"></div>').css({
            position: "absolute",
            display: "none"
        }).appendTo("body");
        $("<div class='chartjs-title' />").appendTo("#chart-css");
        $("<div class='chartjs-tooltip' />").appendTo("#chart-css");
        $("<div class='chartjs-gauge' />").appendTo("#chart-css");
        $("<div class='chartjs-label' />").appendTo("#chart-css");

        // Get CSS properties
        defCSS = {
            title: {
                fontColor: $(".chartjs-title").css("color"),
                fontWeight: $(".chartjs-title").css("font-weight"),
                fontSize: parseInt($(".chartjs-title").css("font-size")) // 20
            },
            tooltip: {
                opacity: 0.7,
                backgroundColor: $(".chartjs-tooltip").css("background-color"),
                titleFontSize: parseInt($(".chartjs-tooltip").css("font-size")), // 12
                bodyFontSize: parseInt($(".chartjs-tooltip").css("font-size")), // 12
                footerFontSize: parseInt($(".chartjs-tooltip").css("font-size")), // 12
                titleFontColor: $(".chartjs-tooltip").css("color"),
                bodyFontColor: $(".chartjs-tooltip").css("color"),
                footerFontColor: $(".chartjs-tooltip").css("color"),
            },
            gauge: {
                value: {
                    fontColor: $(".chartjs-gauge").css("color"),
                    fontFamily: $(".chartjs-gauge").css("font-family"),
                    fontStyle: $(".chartjs-gauge").css("font-style"),
                    fontSize: parseInt($(".chartjs-gauge").css("font-size")) // null
                }
            },
            label: {
                fontColor: $(".chartjs-label").css("color"),
                fontStyle: $(".chartjs-label").css("font-style"),
                fontFamily: $(".chartjs-label").css("font-family"),
                fontSize: parseInt($(".chartjs-label").css("font-size")) // 11
            }
        };

        // Set default properties, using defCSS when required
        defOptions = {
            responsive: true,
            maintainAspectRatio: true,
            title: {
                display: true,
                fontSize: defCSS.title.fontSize,
                fontStyle: defCSS.title.fontWeight, // Weight and/or Style
                fontColor: defCSS.title.fontColor,
                position: 'top'
            },
            layout: {
                padding: {
                    left: 10,
                    right: 10
                }
            },
            animation: {
                animateScale: true,
                animateRotate: true,
                duration: 1000
            },
            hover: {
                mode: 'nearest'
            },
            tooltips: {
                //mode: 'x-axis',
                mode: 'nearest',
                position: 'average',
                displayColors: false,
                enabled: false, // Custom tooltip
                backgroundColor: defCSS.tooltip.backgroundColor,
                titleFontSize: defCSS.tooltip.titleFontSize,
                bodyFontSize: defCSS.tooltip.bodyFontSize,
                footerFontSize: defCSS.tooltip.footerFontSize,
                titleFontColor: defCSS.tooltip.titleFontColor,
                bodyFontColor: defCSS.tooltip.bodyFontColor,
                footerFontColor: defCSS.tooltip.footerFontColor,
                //CornerRadius: 4,
                //CaretSize: 6,
                xAlign: 'center',
                yAlign: 'top'
            },
            legend: {
                display: true,
                position: 'bottom'
            }
        };

        // Delete dummy containers
        $('#chart-css').remove();

        Chart.pluginService.register(chartLoadedPlugin);
        Chart.pluginService.register(doughnutCenterValuePlugin);
        Chart.pluginService.register(barLabelPlugin());

        $.extend(true, Chart.defaults.global, defOptions);
    });

    function init_chart0(data, serie) {
        // Add Tooltip
        if ($('.chartjs-tooltip').length === 0) {
            $('<div id="chartjs-tooltip" class="chartjs-tooltip"></div>').css({
                position: "absolute",
                display: "none"
            }).appendTo("body");
        }

        var chart0_gauges = [];
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
            $('#chart0').prepend('<div class="chartjs-title" id="chart0_title"></div>');
        }
        $('#chart0_title').html(data.title);

        // Eléments du graphique
        var plotBandsInt = [];
        var plotBandsCol = [];
        var band_min;
        var bandMaxValue;
        var bandPrevValue;
        var bandMinColor;
        var gradient = [];
        /*data.data['W'] = 5000;
        data.data['I'] = 21;
        data.seuils['W'].max = 5000;
        data.seuils['I'].max = 25;*/

        /*data.data['W'] = 1131;
        data.data['I'] = 5;
        data.seuils['W'].max = 5000;
        data.seuils['I'].max = 25;*/

        $.each(serieNames, function (serie_num, serie_name) {
            // Ajoute un "canvas" pour chacune des gauges
            if ($('#chart0_canvas' + serie_num).length === 0) {
                $('#chart0').append('<canvas class="chart_gauge' + (serie_num % 2) + '" id="chart0_canvas' + serie_num + '"></canvas>');
            }

            // Seuils des gauges
            plotBandsInt = []; // RAZ
            plotBandsCol.backgroundColor = []; // RAZ
            plotBandsCol.hoverBackgroundColor = []; // RAZ
            plotBandsCol.borderColor = []; // RAZ
            plotBandsCol.hoverBorderColor = []; // RAZ
            plotBandsCol.borderWidth = []; // RAZ
            plotBandsCol.hoverBorderWidth = []; // RAZ
            band_min = 0;
            bandPrevValue = 0;
            bandMinColor = 0;
            $.each(data.bands[serie_name], function (band_max, band_color) {
                bandMaxValue = Math.min(band_max, data.seuils[serie_name].max);
                if (bandMinColor === 0) {
                    bandMinColor = band_color;
                }
                if ((band_min < data.data[serie_name]) && (bandMaxValue >= data.data[serie_name])) {
                    // Série à afficher

                    // Gradient color
                    var ctx = $('#chart0_canvas' + serie_num).get(0).getContext('2d');
                    gradient[serie_num] = ctx.createLinearGradient(0, 0, 300, 0);
                    //gradient[serie_num] = ctx.createRadialGradient(0, 400, 1, 0, 400, 200);
                    gradient[serie_num].addColorStop(0, Color(bandMinColor).alpha(1).rgbString());
                    gradient[serie_num].addColorStop(0.05, Color(bandMinColor).alpha(0.75).rgbString());
                    gradient[serie_num].addColorStop(1, Color(band_color).alpha(0.75).rgbString());

                    plotBandsInt.push(
                        data.data[serie_name]
                    );
                    bandPrevValue = data.data[serie_name];
                    plotBandsCol.backgroundColor.push(gradient[serie_num]);
                    plotBandsCol.hoverBackgroundColor.push(gradient[serie_num]);
                    plotBandsCol.borderColor.push(Color(band_color).alpha(0.75).rgbString());
                    plotBandsCol.hoverBorderColor.push(Color(band_color).alpha(1).rgbString());
                    plotBandsCol.borderWidth.push(7);
                    plotBandsCol.hoverBorderWidth.push(10);
                    band_min = data.data[serie_name];
                }
                if (band_min >= data.data[serie_name]) {
                    // Marqueurs suivants
                    plotBandsInt.push(
                        bandMaxValue - bandPrevValue
                    );
                    bandPrevValue = bandMaxValue;
                    plotBandsCol.backgroundColor.push(Color(band_color).alpha(0.5).rgbString());
                    plotBandsCol.hoverBackgroundColor.push(Color(band_color).alpha(0.5).rgbString());
                    plotBandsCol.borderColor.push(Color(band_color).alpha(0.5).rgbString());
                    plotBandsCol.hoverBorderColor.push(Color(band_color).alpha(0.5).rgbString());
                    plotBandsCol.borderWidth.push(0);
                    plotBandsCol.hoverBorderWidth.push(0);
                }
                band_min = band_max;
            });

            graphOptions = $.extend(true, {}, {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: plotBandsInt,
                        backgroundColor: plotBandsCol.backgroundColor,
                        borderColor: plotBandsCol.borderColor,
                        hoverBackgroundColor: plotBandsCol.hoverBackgroundColor,
                        hoverBorderColor: plotBandsCol.hoverBorderColor,
                        borderWidth: plotBandsCol.borderWidth,
                        hoverBorderWidth: plotBandsCol.hoverBorderWidth
                    }]
                },
                options: {
                    /*title: { // Remplacé par un DIV centré
                        text: serie_num === 0 ? data.title : '',
                        //fontSize: 36,
                    },*/
                    subtitle: data.subtitle, // Plugin chartLoadedPlugin
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    },
                    cutoutPercentage: 66,
                    circumference: Math.PI * 1.5,
                    rotation: -Math.PI * 1.25,
                    elements: {
                        center: {
                            maxText: "  " + data.seuils[serie_name].max + " "  + data.series[serie_name] + "  ",
                            text: data.data[serie_name] + "\n"  + data.series[serie_name],
                            fontColor: defCSS.gauge.value.fontColor,
                            fontFamily: defCSS.gauge.value.fontFamily,
                            fontStyle: defCSS.gauge.value.fontStyle,
                            fontSize: defCSS.gauge.value.fontSize
                        }
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, data) {
                                this._chart.config.options.tooltipItem = tooltipItem;
                                return tooltipItem.yLabel;
                            }
                        },
                        custom: function (tooltip, data) {
                            if (tooltip.body) {
                                // On a conservé toltipItem
                                //var thisSerieNum = this._chart.config.options.tooltipItem.datasetIndex;
                                var thisPtX = this._chart.config.options.tooltipItem.index;
                                var innerHtml = tooltip_chart0(serie_num, thisPtX);

                                $(".chartjs-tooltip").html(innerHtml)
                                    .css({
                                        opacity: defCSS.tooltip.height,
                                        top: this._chart.canvas.offsetTop /*+ tooltip.y*/ + 'px',
                                        left: this._chart.canvas.offsetLeft + this._chart.canvas.offsetWidth / 2 + 'px',
                                        borderColor: this._data.datasets[0].borderColor[0]
                                    })
                                    .removeClass('above below')
                                    .addClass(tooltip.yAlign)
                                    .show();
                                    //.fadeIn(200);
                            } else {
                                $(".chartjs-tooltip").hide();
                            }
                        }
                    }
                }
            });

            chart0_gauges.push(
                new Chart($('#chart0_canvas' + serie_num), graphOptions)
            );
        });

        return chart0_gauges;
    }

    function init_chart1(data) {
        // Add Tooltip
        if ($('.chartjs-tooltip').length === 0) {
            $('<div id="chartjs-tooltip" class="chartjs-tooltip"></div>').css({
                position: "absolute",
                display: "none"
            }).appendTo("body");
        }

        $('#chart1').on({
            dblclick: function (event) {
                // RAZ du zoom
                chart_elec1.resetZoom();
            }
        });

        // Préparation des séries de données
        var dataSerie;
        var graphSeries = [];

        // Période courante
        $.each(data.series, function (serie_name, serie_title) {
            dataSerie = data[serie_name + "_data"].map(function (a) { return a === null ? {x: null, y: null} : {x: a[0], y: a[1]}; }, 0);
            graphSeries.push({
                label: data[serie_name + "_name"],
                data: dataSerie, // data[serie_name + "_data"],
                type: 'line',
                lineTension: 0,
                pointRadius: 0,
                backgroundColor: Color(data[serie_name + "_color"]).alpha(0.3).rgbString(),
                //hoverBackgroundColor: Color(data[serie_name + "_color"]).alpha(0.7).rgbString(),
                borderColor: data[serie_name + "_color"],
                borderWidth: 2,
                /*events: {
                    click: function (e) {
                        var newdate = new Date();
                        newdate.setTime(data.debut);
                        newdate.setDate(newdate.getDate() + e.point.x);
                        console.log(newdate);
                    }
                },*/
                xAxisID: 'x-axis-1',
                yAxisID: 'y-axis-1',
                showLabels: true, // Custom parameter
                hidden: !(data[serie_name + "_data"].reduce(function (a, b) { return a + b; }, 0) !== 0)
                //showInLegend: (data[serie_name + "_data"].reduce(function (a, b) { return a + b; }, 0) !== 0)
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
        var dataPREC = data.PREC_data.map(function (a) { return a === null ? {x: null, y: null} : {x: a[0], y: a[1]}; }, 0);
        graphSeries.push({
            label: data.PREC_name,
            data: dataPREC,
            type: 'line',
            lineTension: 0,
            pointRadius: 0,
            backgroundColor: 'transparent',
            borderColor: data.PREC_color,
            borderWidth: 2,
            xAxisID: 'x-axis-1',
            yAxisID: 'y-axis-1',
            showLabels: false, // Custom property
            hidden: !(data.PREC_data.reduce(function (a, b) { return a + b; }, 0) !== 0)
            //showInLegend: (data.PREC_data.reduce(function (a, b) { return a + b; }, 0) !== 0)
        });

        var graphOptions = $.extend(true, {}, {
            type: 'line', // When creating mixed chart types, you must set the overall type as bar.
            data: {
                labels: data.navigator,
                datasets: graphSeries
            },
            options: {
                title: {
                    text: data.title
                },
                subtitle: data.subtitle, // Plugin chartLoadedPlugin
                /*pan: {
                    enabled: true,
                    mode: 'x'
                },*/
                zoom: {
                    enabled: true,
                    //drag: true,
                    mode: 'x'
                },
                scales: {
                    xAxes: [{ // xAxes 0
                        display: true,
                        position: 'bottom',
                        type: "time",
                        id: 'x-axis-1',
                        ticks: {
                            callback: function (dataLabel, index) {
                                return index % 3 === 0 ? dataLabel : null;
                            }
                        },
                        time: {
                            min: data.navigator[0][0],
                            max: data.navigator[data.navigator.length - 1][0],
                            displayFormats: {
                                hour: 'HH:mm',
                                minute: 'HH:mm',
                                second: 'HH:mm'
                            }
                        }
                    }],
                    yAxes: [{
                        display: true,
                        position: 'left',
                        stacked: false,
                        id: 'y-axis-1',
                        ticks: {
                            beginAtZero: true
                            //max: yMax
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Watt'
                        }
                    }]
                },
                tooltips: {
                    mode: 'x-axis',
                    callbacks: {
                        label: function (tooltipItem, data) {
                            this._chart.config.options.tooltipItem = tooltipItem;
                            return tooltipItem.yLabel;
                        }
                    },
                    custom: function (tooltip, data) {
                        if (tooltip.body) {
                            // On a conservé toltipItem
                            var thisSerieNum = this._chart.config.options.tooltipItem.datasetIndex;
                            var thisPtX = this._chart.config.options.tooltipItem.index;
                            if (this._data.datasets[this._data.datasets.length - 1].data.length > thisPtX) {
                                var innerHtml = tooltip_chart1(thisSerieNum, thisPtX);

                                $(".chartjs-tooltip").html(innerHtml)
                                    .css({
                                        opacity: defCSS.tooltip.height,
                                        top: this._chart.canvas.offsetTop + tooltip.y + 'px',
                                        left: this._chart.canvas.offsetLeft + tooltip.x + 'px',
                                        borderColor: '' // default CSS color
                                    })
                                    .removeClass('above below')
                                    .addClass(tooltip.yAlign)
                                    .show();
                                    //.fadeIn(200);
                            } else {
                                $(".chartjs-tooltip").hide();
                            }
                        } else {
                            $(".chartjs-tooltip").hide();
                        }
                    }
                },
                annotation: {
                    annotations: [{
                        type: 'line', // Min
                        borderDash: [5, 5],
                        mode: 'horizontal',
                        scaleID: 'y-axis-1',
                        value: data.seuils.min,
                        borderColor: data.MIN_color,
                        borderWidth: 2
                    }, {
                        type: 'line', // Max
                        borderDash: [5, 5],
                        mode: 'horizontal',
                        scaleID: 'y-axis-1',
                        value: data.seuils.max,
                        borderColor: data.MAX_color,
                        borderWidth: 2
                    }]
                }
            }
        });

        if ($('#chart1_canvas').length === 0) {
            $('#chart1').append('<canvas id="chart1_canvas"></canvas>');
        }

        return new Chart($('#chart1_canvas'), graphOptions);
    }

    function init_chart2(data) {
        // Add Tooltip
        if ($('.chartjs-tooltip').length === 0) {
            $('<div id="chartjs-tooltip" class="chartjs-tooltip"></div>').css({
                position: "absolute",
                display: "none"
            }).appendTo("body");
        }

        // Préparation des séries de données
        var graphSeries = [];

        // Période courante
        $.each(data.series, function (serie_name, serie_title) {
            graphSeries.push({
                label: serie_title,
                data: data[serie_name + "_data"],
                type: data[serie_name + "_type"] === 'column' ? 'bar' : data[serie_name + "_type"],
                backgroundColor: Color(data[serie_name + "_color"]).alpha(0.3).rgbString(),
                hoverBackgroundColor: Color(data[serie_name + "_color"]).alpha(0.7).rgbString(),
                borderColor: data[serie_name + "_color"],
                borderWidth: 1,
                /*events: {
                    click: function (e) {
                        var newdate = new Date();
                        newdate.setTime(data.debut);
                        newdate.setDate(newdate.getDate() + e.point.x);
                        console.log(newdate);
                    }
                },*/
                xAxisID: 'x-axis-1',
                yAxisID: 'y-axis-1',
                showLabels: true, // Custom property
                hidden: !(data[serie_name + "_data"].reduce(function (a, b) { return a + b; }, 0) !== 0)
                //showInLegend: (data[serie_name + "_data"].reduce(function (a, b) { return a + b; }, 0) !== 0)
            });
        });

        // Période précédente
        var dataPREC = data.PREC_data.map(function (a) { return a === null ? 0 : a[1]; }, 0);
        graphSeries.push({
            label: data.PREC_name,
            data: dataPREC,
            type: data.PREC_type === 'spline' ? 'line' : data.PREC_type,
            lineTension: 0.25,
            backgroundColor: 'transparent',
            borderColor: data.PREC_color,
            pointBackgroundColor: data.PREC_color,
            xAxisID: 'x-axis-1',
            yAxisID: 'y-axis-1',
            showLabels: false, // Custom parameter
            hidden: !(data.PREC_data.reduce(function (a, b) { return a + b; }, 0) !== 0)
            //showInLegend: (data.PREC_data.reduce(function (a, b) { return a + b; }, 0) !== 0)
        });

        var graphOptions = $.extend(true, {}, {
            type: 'bar', // When creating mixed chart types, you must set the overall type as bar.
            data: {
                labels: data.categories,
                datasets: graphSeries
            },
            options: {
                valueLabels: {
                    show: true,
                    decimals: 1,
                    align: 'center',
                    valign: 'top',
                    padding: 10,
                    hideZero: true,
                    fontColor: defCSS.label.fontColor,
                    fontSize: defCSS.label.fontSize,
                    fontStyle: defCSS.label.fontStyle,
                    fontFamily: defCSS.label.fontFamily
                },
                title: {
                    text: data.title
                },
                subtitle: data.subtitle, // Plugin chartLoadedPlugin
                scales: {
                    xAxes: [{ // xAxes 0
                        display: true,
                        position: 'bottom',
                        type: 'category',
                        id: 'x-axis-1',
                        stacked: true
                    }],
                    yAxes: [{
                        display: true,
                        position: 'left',
                        stacked: true, // 'histo',
                        id: 'y-axis-1',
                        ticks: {
                            beginAtZero: true
                            //max: yMax
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'kWh'
                        }
                    }]
                },
                tooltips: {
                    callbacks: {
                        label: function (tooltipItem, data) {
                            this._chart.config.options.tooltipItem = tooltipItem;
                            return tooltipItem.yLabel;
                        }
                    },
                    custom: function (tooltip, data) {
                        if (tooltip.body) {
                            // On a conservé toltipItem
                            var thisSerieNum = this._chart.config.options.tooltipItem.datasetIndex;
                            var thisPtX = this._chart.config.options.tooltipItem.index;
                            var innerHtml = tooltip_chart2(thisSerieNum, thisPtX);

                            $(".chartjs-tooltip").html(innerHtml)
                                .css({
                                    opacity: defCSS.tooltip.height,
                                    top: this._chart.canvas.offsetTop + tooltip.y + 'px',
                                    left: this._chart.canvas.offsetLeft + tooltip.x + 'px',
                                    borderColor: this._data.datasets[thisSerieNum].borderColor
                                })
                                .removeClass('above below')
                                .addClass(tooltip.yAlign)
                                .show();
                                //.fadeIn(200);
                        } else {
                            $(".chartjs-tooltip").hide();
                        }
                    }
                }
            }
        });

        if ($('#chart2_canvas').length === 0) {
            $('#chart2').append('<canvas id="chart2_canvas"></canvas>');
        }

        return new Chart($('#chart2_canvas'), graphOptions);
    }

    return {
        init_chart0: init_chart0,
        init_chart1: init_chart1,
        init_chart2: init_chart2
    };
}());