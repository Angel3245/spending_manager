class GraphModel extends Fronty.Model {

    constructor(months,data) {
        super('GraphModel');

        // model attributes
        if (months) 
            this.months = months;
        
        if(data)
            this.data = data;
    }
  
    /* setMonths(months) {
        this.set( (self) => {
            self.months = months;
        });
    } */

    getMonths(){
        return this.months;
    }

    /* setData(data) {
        this.set( (self) => {
            self.data = data;
        });
    } */
  
    getData(){
        return this.data;
    }

    setAll(months,data) {
        this.set( (self) => {
            self.months = months;
            self.data = data;
        });
    }

    calcularPorcentajes(){
        var mapa = [];
        var total = 0;

        var tipos_gasto = [];

        this.getData().forEach((elem) => {
            tipos_gasto.push(elem["name"])
        });

        for (var i = 0; i< tipos_gasto.length; i++){
            total += this.sum(this.getData()[i]['data']);
        }
    
        var porcentages = [];
        for (var j = 0; j< tipos_gasto.length; j++){
            var sum_temp_2 = ((this.sum(this.getData()[j]['data'])/total)*100).toFixed(1);
            porcentages.push( sum_temp_2 );
            mapa.push({
                "name" : tipos_gasto[j],
                "y" : parseFloat(sum_temp_2)
            });
        }
        
        return mapa;
    }
    
    sum(array) {
        let sum = 0;
        for (let i = 0; i < array.length; i++) {
            sum += array[i];
        }
        return sum;
    }

}
  