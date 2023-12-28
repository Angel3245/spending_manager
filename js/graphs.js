
window.docReady(() => {
    let tipos_gasto = [];
    highchart_array["gastos"].forEach((elem) => {
        tipos_gasto.push(elem["name"])
    });
    
    let gastos = highchart_array["gastos"];
    let mapTypesAndPcts = calcularPorcentajes(gastos,tipos_gasto);
    let meses = highchart_array["months"];

    createAndAddLineChart(meses, gastos);
    createAndAddPieChart(mapTypesAndPcts);
});

/** Creates the Line Chart with data passed as parameter */
function createAndAddLineChart(meses, data) {
    
    // Configure and put the chart in the HTML document
    Highcharts.chart('container-grafico-lineas', {
        chart: {
            events: {
                load: function () {
                    var label = this.renderer.label('Cargando...', 1170, 20)
                        .attr({
                            fill: Highcharts.getOptions().colors[1],
                            padding: 15,
                            r: 5,
                            zIndex: 8,
                            /* fontSize:'15px', */
                            fontWeight:'bold'
                        })
                        .css({
                            color: '#FFFFFF',
                        })
                        .add();
    
                    setTimeout(function () {
                        label.fadeOut();
                    }, 1250);
                }
            },
            

            height: (4.5 / 9 * 100) + '%',

            borderRadius: 5,

            shadow: {
                color : "#006eed",
                opacity: 0.35,
                width:5,
                borderRadius:10
            },

            margin: [90, 90, 90, 90],

        },
        title: {
            text: '' // Gastos en los últimos 12 meses
        },

        yAxis: {
            title: {
                text: 'Euros'
            }
        },

        xAxis: {
            categories: meses
        },

        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'top',
            x:-100,
            y:50
        },

        plotOptions: {
            series: {
                label: {
                    connectorAllowed: false
                },
            }
            
        },

        series: data,

        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                },
                chartOptions: {
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom'
                    }
                }
            }]
        },

        /* Predefined color scheme to use by highcharts */
        colors: ['#142459','#176BA0','#19AADE','#1AC9E6','#1BD4D4','#1DE4BD','#6DF0D2'],

        /* Disabling the module will hide the context button */
        exporting: {
            enabled: false,
        },

        /* Hides the credits label that appears in the lower right corner of the chart */
        credits: {
            enabled: false,
        }

    });
}

function createAndAddPieChart(mapTypesAndPcts){
    
    Highcharts.chart('container-grafico-tarta', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie',
            events: {
                load: function () {
                    var label = this.renderer.label('Cargando...', 670, 0)
                        .attr({
                            fill: Highcharts.getOptions().colors[1],
                            padding: 15,
                            r: 5,
                            zIndex: 8,
                            /* fontSize:'15px', */
                            fontWeight:'bold'
                        })
                        .css({
                            color: '#FFFFFF',
                        })
                        .add();
    
                    setTimeout(function () {
                        label.fadeOut();
                    }, 1250);
                }
            },

            height: (4.5 / 9 * 100) + '%',

            borderRadius: 5,

            shadow:true,

            margin: [90, 90, 90, 90],

            shadow: {
                color : "#006eed",
                opacity: 0.35,
                width:5,
                borderRadius:10
            }
        },
        title: {
            text: '' // Gastos en los últimos 12 meses
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        accessibility: {
            point: {
                valueSuffix: '%'
            }
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                }
            }
        },
        series: [{
            name: 'Tipo de Gasto',
            colorByPoint: true,
            data: mapTypesAndPcts
        }],

        /* Predefined color scheme to use by highcharts */
        colors: ['#3974FF','#5B8BFF','#7DA3FF','#9FBBFF','#C1D3FF','#E3EBFF','#EFEFEF'],

        /* Disabling the module will hide the context button */
        exporting: {
            enabled: false,
        },

        /* Hides the credits label that appears in the lower right corner of the chart */
        credits: {
            enabled: false,
        },



    });
}
