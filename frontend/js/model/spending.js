class SpendingModel extends Fronty.Model{

    constructor(id_spending, type_spending, date_spending, qty_spending, description_spending, doc_name_spending ,owner){
        super('SpendingModel');
        
        if(id_spending){
            this.id_spending = id_spending;
        }

        if(type_spending){
            this.type_spending = type_spending;
        }

        if(date_spending){
            this.date_spending = date_spending;
        }

        if(qty_spending){
            this.qty_spending = qty_spending;
        }

        if(description_spending){
            this.description_spending = description_spending;
        }
       
        if(doc_name_spending){
            this.doc_name_spending = doc_name_spending;
        }

        if(owner){
            this.owner = owner;
        }
    }

    getType(){
        return this.type;
    }

    setType(type_spending) {
        this.set( (self) => {
            self.type_spending = type_spending;
        });
    }

    setDate(date_spending) {
        this.set( (self) => {
            self.date_spending = date_spending;
        });
    }
    
    setQty(qty_spending) {
        this.set( (self) => {
            self.qty_spending = qty_spending;
        });
    }

    setDesc(description_spending) {
        this.set( (self) => {
            self.description_spending = description_spending;
        });
    }

    setDocName(doc_name_spending){
        this.set( (self) => {
            self.doc_name_spending = doc_name_spending;
        });
    }
}

