function calcularPorcentajes(gastos, tipos){
    let mapa = [];
    let total = 0;
    for (let i = 0; i< tipos.length; i++){
        total += sum(gastos[i]['data']);
    }

    let porcentages = [];
    for (let j = 0; j< tipos.length; j++){
        let sum_temp_2 = ((sum(gastos[j]['data'])/total)*100).toFixed(1);
        porcentages.push( sum_temp_2 );
        mapa.push({
            "name" : tipos[j],
            "y" : parseFloat(sum_temp_2)
        });
    }
    
    return mapa;
}

function sum(array){
    let sum = 0;
    for (let i = 0; i < array.length; i++) {
        sum += array[i];
    }
    return sum;
}