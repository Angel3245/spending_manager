class AnalysisComponent extends SuperModelComponent {
    constructor(graphModel, userModel, router, listaCss) {
      super(Handlebars.templates.spendingsanalysis, graphModel, null, listaCss);
      
      this.graphModel = graphModel;
      this.userModel = userModel;
      this.addModel('user', userModel);
      this.router = router;
  
      this.spendingsService = new SpendingsService();

      this.addEventListener('click', '#submitAnalisys', () => {
        //update the model
        var date1 = document.getElementById("date1").value;
        var date2 = document.getElementById("date2").value;
        var food_chk = document.getElementById("food_chk").checked ? "on" : "off";
        var fuel_chk = document.getElementById("fuel_chk").checked ? "on" : "off";
        var comm_chk = document.getElementById("comm_chk").checked ? "on" : "off";
        var supp_chk = document.getElementById("supp_chk").checked ? "on" : "off";
        var ft_chk = document.getElementById("ft_chk").checked ? "on" : "off";

        this.spendingsService.plot(date1,date2,food_chk,fuel_chk,comm_chk,supp_chk,ft_chk).then((response) => {

          var obj = JSON.parse(response);
          var meses = [];
          
          // translate months
          obj.months.forEach(month => {
            var aux = month.split(" ");
            meses.push(this.translate(aux[0])+" "+aux[1]);
          });

          //translate spending types
          obj.gastos.forEach(gasto => {
            gasto.name = this.translate(gasto.name);
          });

          this.graphModel.setAll(meses,obj.gastos);

          this._clearErrors();

        })
        .fail((xhr, errorThrown, statusText) => {
          if (xhr.status == 400) {
              this.graphModel.set( () => {
                  this.graphModel.errors = xhr.responseJSON;
              });
          } else {
              console.log('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
          }
      });

      });


    }

  onStart(){

    this.spendingsService.plotLastYear().then((response) => {

      var obj = JSON.parse(response);
      var meses = [];

      // translate months
      obj.months.forEach(month => {
        var aux = month.split(" ");
        meses.push(this.translate(aux[0])+" "+aux[1]);
      });

      //translate spending types
      obj.gastos.forEach(gasto => {
        gasto.name = this.translate(gasto.name);
      });

      this.graphModel.setAll(meses,obj.gastos);

      this.line_chart =
      // Configure and put the chart in the HTML document
      Highcharts.chart('container-grafico-lineas', {
        chart: {
            events: {
                load: function () {
                    var label = this.renderer.label(I18n.translate('Loading...'), 1170, 20)
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
                text: this.translate('Euros')
            }
        },

        xAxis: {
            categories: this.graphModel.getMonths()
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

        series: this.graphModel.getData(),

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

      this.pie_chart = Highcharts.chart('container-grafico-tarta', {
        chart: {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie',
            events: {
                load: function () {
                    var label = this.renderer.label(I18n.translate('Loading...'), 670, 0)
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
            name: this.translate('Type of Spending'),
            colorByPoint: true,
            data: this.graphModel.calcularPorcentajes()
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
        }

      });

    });

  }

  /* AFTER RENDER hook */
  //No se toca, funciona!!1!
  afterRender(){

    this.import_css(this.listaCss)

    if (this.line_chart) { //check if this.line_chart exists, because the first afterRender is called before onStart
      // use "setData" in order to update the chart, but not create the chart again

      for (var i=0; i < this.graphModel.getData().length; i++) {
        this.line_chart.series[i].setData(
          this.graphModel.getData()[i].data
        );
        this.line_chart.series[i].setName(
          this.graphModel.getData()[i].name
        );
        this.line_chart.series[i].options.showInLegend = true;
      }

      for (var i = this.graphModel.getData().length; i < this.line_chart.series.length; i++) {  
        this.line_chart.series[i].setData(
          []
        );
        this.line_chart.series[i].setName(
          ""
        );

        this.line_chart.series[i].update({ showInLegend: false });

      }
    
      this.line_chart.xAxis[0].setCategories(
        this.graphModel.getMonths()
      );

    }

    if (this.pie_chart) { //check if this.pie_chart exists, because the first afterRender is called before onStart
      // use "setData" in order to update the chart, but not create the chart again

        this.pie_chart.series[0].setData(
          this.graphModel.calcularPorcentajes()
        );

    }

  }

  /* Function that allows to erase label errors */
  _clearErrors(){
    var errorLabels = document.getElementsByClassName("error");
    for(let label of errorLabels){
        label.innerHTML = ""
    }
  }

  /* STOP hook */
  onStop(){
    this.remove(this.listaCss)
  }

  translate(string){
    return I18n.translate(string);
  }
}