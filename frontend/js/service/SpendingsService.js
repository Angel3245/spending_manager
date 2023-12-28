class SpendingsService {
    constructor() {
    }
  
    /* FIND SPENDINGS */
    findAllSpendings() {
      return $.get(AppConfig.backendServer+'/rest/spending');
    }
  
    findSpending(id) {
      return $.get(AppConfig.backendServer+'/rest/spending/' + id);
    }
  
    /* ADD SPENDING */
    addSpending(spending) {
        return $.ajax({
          url: AppConfig.backendServer+'/rest/spending',
          method: 'POST',
          data: JSON.stringify(spending),
          contentType: 'application/json'
        });
      }
  
    /* UPDATE SPENDING */
    saveSpending(spending) {
      return $.ajax({
        url: AppConfig.backendServer+'/rest/spending/' + spending.id,
        method: 'PUT',
        data: JSON.stringify(spending),
        contentType: 'application/json'
      });
    }

    /* DELETE SPENDING */
    deleteSpending(id) {
        return $.ajax({
          url: AppConfig.backendServer+'/rest/spending/' + id,
          method: 'DELETE'
        });
      }
  
    /* DOWNLOAD CSV */
    downloadCSVFile(table_id, role) {
 
      // Variable to store the final csv data
      var csv_data = [];

      // HTML table to convert
      var table = document.getElementById(table_id);
 
      // Get each row data
      var rows = table.getElementsByTagName('tr');
      for (var i = 0; i < rows.length; i++) {

          // Get each column data
          var cols = rows[i].querySelectorAll('td,th');

          // Stores each csv row data
          var csvrow = [];
          for (var j = 0; j < cols.length; j++) {

              // Remove unnecesary cols (Owner, Actions)
              if(j < 6 || (j < 7 && role == "admin") ){
                
                  // Get the text data of each cell
                  // of a row and push it to csvrow
                  csvrow.push($(cols[j]).text());
              }
          }

          // Combine each column value with comma
          csv_data.push(csvrow.join(","));
      }

      // Combine each row data with new line character
      csv_data = csv_data.join('\n');

      // Create CSV file object and feed
      // our csv_data into it
      var CSVFile = new Blob([csv_data], {
          type: "text/csv"
      });

      // Create to temporary link to initiate
      // download process
      var temp_link = document.createElement('a');

      // Download csv file
      temp_link.download = "Spendings.csv";
      var url = window.URL.createObjectURL(CSVFile);
      temp_link.href = url;

      // This link should not be displayed
      temp_link.style.display = "none";
      document.body.appendChild(temp_link);

      // Automatically click the link to
      // trigger download
      temp_link.click();
      document.body.removeChild(temp_link);
    }

    /* DOWNLOAD SPENDING FILE */
    downloadFile(spending_id) {
 
      return $.get(AppConfig.backendServer+'/rest/spending/'+spending_id+'/file');
      
    }

    plotLastYear() {
      return $.get(AppConfig.backendServer+'/rest/spending/graphic');
    }

    plot(date1,date2,food_chk,fuel_chk,comm_chk,supp_chk,ft_chk) {
      return $.ajax({
        url: AppConfig.backendServer+'/rest/spending/graphic?date1='+date1+'&date2='+date2
        +'&food_chk='+food_chk+'&fuel_chk='+fuel_chk+'&comm_chk='+comm_chk+'&supp_chk='+supp_chk
        +'&ft_chk='+ft_chk,
        method: 'GET'
      });
    }

}
  