class SpendingEditComponent extends SuperModelComponent {
    constructor(spendingsModel, userModel, router, listaCss, form_id) {
        super(Handlebars.templates.spendingedit, spendingsModel, listaCss);
        
        this.spendingsModel = spendingsModel; // spendings
        this.userModel = userModel; // global
        this.addModel('user', userModel);
        this.router = router;
        this.listaCss = listaCss;
        this.spendingsService = new SpendingsService();
        this.form_id = form_id;

        this.currentFile = null;

        this.addEventListener('change', '#doc_name_spending', (e) => {
            var fichero =  e.target.files[0];
            document.getElementById("fileText").textContent = fichero.name;
            var reader = new FileReader();
            reader.readAsDataURL(fichero);
            
            reader.onload = ()  => {
                this.currentFile = reader.result;
            };
        });

        this.addEventListener('click', '#edit_button', (e) => {

            //TODO: Mejor aprovechar la variable selectedSpending en vez de crear otra como en el ejemplo con los Posts (Github Lipido)
            var newSpending = {};
            newSpending.id = spendingsModel.selectedSpending.id_spending;
            newSpending.type_spending = $('#type_spending').val();
            newSpending.date_spending = $('#date_spending').val();
            newSpending.qty_spending = $('#qty_spending').val();
            newSpending.description_spending = $('#description_spending').val();
            if (document.getElementById("fileText").textContent !== I18n.translate('Upload File')) {
                newSpending.doc_name_spending = document.getElementById("fileText").textContent;
                newSpending.doc_data = this.currentFile;
            }
            newSpending.owner = this.userModel.currentUser;
            
            


            this.spendingsService.saveSpending(newSpending).then(() => {
                this.router.goToPage('spendings');
            })
            .fail((xhr, errorThrown, statusText) => {
                if (xhr.status == 400) {
                    this.spendingsModel.set( () => {
                        this.spendingsModel.errors = xhr.responseJSON;
                    });
                } else {
                    console.log('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
                }
            });
        });

        this.addEventListener('click','#back_button', () => {
            this.router.goToPage('spendings');
        });
    }


    /* Function that allows to reset all inputs from form and erase label errors */
    _resetForm(){
        var variable = $("#" + this.form_id)[0];
        variable.reset();

        this.spendingsModel.errors = {};
    }

    
    /* AFTER RENDER hook */
    afterRender(){
        this.import_css(this.listaCss)
    }

    /* STOP hook */
    onStop(){
        this.remove(this.listaCss);
        /* this._resetForm(); */
    }
    
    /* START hook */
    onStart() {
        this._resetForm();
        
        var selectedId = this.router.getRouteQueryParam('id');
        if (selectedId != null) {
            this.spendingsService.findSpending(selectedId)
                .then((data) => {
                    this.spendingsModel.setSelectedSpending(data);
                });
        }
    }
  }
  