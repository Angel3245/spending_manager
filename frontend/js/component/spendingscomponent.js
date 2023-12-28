class SpendingsComponent extends SuperModelComponent {
  constructor(spendingsModel, userModel, router, listaCss) {
    super(Handlebars.templates.spendingstable, spendingsModel, null, listaCss);
    
    this.spendingsModel = spendingsModel;
    this.userModel = userModel;
    this.addModel('user', userModel);
    this.router = router;
    this.sortingAsc = false;

    this.spendingsService = new SpendingsService();
    this.listaCss = listaCss;


    /* EXPORT TO CSV event listener */
    this.addEventListener('click', '#export_to_csv', (event) => {
      this.spendingsService.downloadCSVFile("showall",userModel.role);
    });

    /* DOWNLOAD FILE event listener */
    this.addEventListener('click', '.file_spending', (event) => {
      var id_spending = event.target.getAttribute("id").split("_")[2]
      
      this.spendingsService.downloadFile(id_spending).then((data) => {
        var link = document.createElement('a');
        
        link.href = data["doc_data"];
        link.download = event.target.getInnerHTML();
        link.click();

        URL.revokeObjectURL(link.href);
        document.body.removeChild(link);
      });
    });


    /* DELETE SPENDING event listener */
    this.addEventListener('click','.delete_sp', (e) => {
      var result = confirm(I18n.translate("Do you really want to delete this spending?"));
      if (result) {
        var id_spending = e.target.getAttribute("id").split("_")[2];

        this.spendingsService.deleteSpending(id_spending).then( () => {
          this.updateSpendings();
        });
      }
    });

    /* EDIT SPENDING event listener */
    this.addEventListener('click','.edit_sp', (e) => {
      var id_spending = e.target.getAttribute("id").split("_")[2];
      
      this.router.goToPage('edit-spending?id=' + id_spending);
    });


    /* SORT BY TYPE event listener */
    this.addEventListener('click', '#sort_type', (e) => {
      this.sortingAsc = !this.sortingAsc;
      this._sortSpendingsByTypeOfSpending(this.sortingAsc);
    });

    /* SORT BY DATE event listener */
    this.addEventListener('click', '#sort_date', (e) => {
      this.sortingAsc = !this.sortingAsc;
      this._sortSpendingsByDate(this.sortingAsc);
    });

    /* SORT BY QUANTITY event listener */
    this.addEventListener('click', '#sort_quantity', (e) => {
      this.sortingAsc = !this.sortingAsc;
      this._sortSpendingByQuantity(this.sortingAsc);
    });

  }

  /* AFTER RENDER hook */
  afterRender(){
    this.import_css(this.listaCss);
  }

  /* STOP hook */
  onStop(){
    this.remove(this.listaCss)
  }

  /* START hook */
  onStart() {
    this.updateSpendings();
  }

  updateSpendings() {
    this.spendingsService.findAllSpendings().then((data) => {

      var datos = data.map(
        (item) => new SpendingModel(item.id_spending, item.type_spending, item.date_spending,
        item.qty_spending, item.description_spending, item.doc_name_spending, item.owner)
      )

      this.spendingsModel.setSpendings(
        // create a Fronty.Model for each item retrieved from the backend
        datos
      );

      this._sortSpendingsByDate(this.sortingAsc);

    });
  }



  _sortSpendingsByDate(sortingAsc){
    var arraySpendings = this.spendingsModel.getSpendings();

    var sortable = [];
    arraySpendings.forEach( (e) => {
      sortable.push([e, e.date_spending])
    })
    
    sortable.sort( (a,b) => {
      return new Date( a[0].date_spending) - new Date(b[0].date_spending);
    });

    var newSortable = []
    sortable.forEach( (e) => {
      newSortable.push(e[0]);
    });

    if(!sortingAsc){
      newSortable.reverse();
    }

    this.spendingsModel.setSpendings(newSortable);
  }





  _sortSpendingsByTypeOfSpending(sortingAsc){
    var arraySpendings = this.spendingsModel.getSpendings();

    var sortable = [];
    arraySpendings.forEach( (e) => {
      sortable.push([e,e.type_spending])
    })
    
    sortable.sort();

    var newSortable = []
    sortable.forEach( (e) => {
      newSortable.push(e[0]);
    });

    if(!sortingAsc){
      newSortable.reverse();
    }

    this.spendingsModel.setSpendings(newSortable);
  }

  _sortSpendingByQuantity(sortingAsc){
    var arraySpendings = this.spendingsModel.getSpendings();

    var sortable = [];
    arraySpendings.forEach( (e) => {
      sortable.push([e,e.qty_spending])
    })
    
    sortable.sort( (a,b) => {
      return a[0].qty_spending - b[0].qty_spending;
    });

    var newSortable = []
    sortable.forEach( (e) => {
      newSortable.push(e[0]);
    });

    if(!sortingAsc){
      newSortable.reverse();
    }

    this.spendingsModel.setSpendings(newSortable);
  }

}