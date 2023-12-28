class UserModel extends Fronty.Model {
  constructor() {
    super('UserModel');
    this.isLogged = false;
  }

  setLoggeduser(loggedUser,role) {
    this.set((self) => {
      self.currentUser = loggedUser;
      self.role = role;
      self.isLogged = true;
    });
  }

  logout() {
    this.set((self) => {
      delete self.currentUser;
      self.isLogged = false;
    });
  }

  setUserinfo(user) {
    this.set( (self) => {
      self.userInfo = user;
    });
  }
}
