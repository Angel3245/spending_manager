class SpendingAddComponent extends SuperModelComponent {
    constructor(spendingsModel, userModel, router, listaCss, form_id) {
        super(Handlebars.templates.spendingadd, spendingsModel, listaCss);
        
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

        this.addEventListener('click', '#add_button', (e) => {
            var newSpending = {};
            newSpending.type_spending = $('#type_spending').val();
            newSpending.date_spending = $('#date_spending').val();
            newSpending.qty_spending = $('#qty_spending').val();
            newSpending.description_spending = $('#description_spending').val();
            if (document.getElementById("fileText").textContent !== I18n.translate('Upload File')) {
                newSpending.doc_name_spending = document.getElementById("fileText").textContent;
                newSpending.doc_data = this.currentFile;
            }

            if(this.userModel.role == "admin"){
                newSpending.owner_spending = $('#owner_spending').val();
            }

            this.spendingsService.addSpending(newSpending).then(() => {
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
    }
    
    /* START hook */
    onStart() {
        this._resetForm();
        this.spendingsModel.setSelectedSpending(new SpendingModel());
    }
  }
  