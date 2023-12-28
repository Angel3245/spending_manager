class SpendingsModel extends Fronty.Model {

    constructor() {
      super('SpendingsModel');
  
      // model attributes
      this.spendings = [];
    }
  
    setSelectedSpending(sp) {
      this.set((self) => {
        self.selectedSpending = sp;
      });
    }
  
    setSpendings(spendings) {
      this.set( (self) => {
        self.spendings = spendings;
      });
    }

    getSpendings(){
      return this.spendings;
    }
}
  