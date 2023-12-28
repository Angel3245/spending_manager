class UserEditComponent extends SuperModelComponent {
  constructor(userModel, router, listaCss) {
    super(Handlebars.templates.useredit, userModel, null , listaCss);
    this.userModel = userModel;
    this.userService = new UserService();
    this.router = router;

    this.addEventListener('click', '#updatebutton', () => {
      this.userService.updateUser({
          email: $('#registeremail').val(),
          username: $('#registerusername').val(),
          password: $('#registerpassword').val()
        })
        .then(() => {
          alert(I18n.translate('User updated! Please login'));
          this.userModel.set((model) => {
            model.registerErrors = {};
            this.router.goToPage('spendings');
          });
          this.userModel.logout();
          this.userService.logout();
          this.router.goToPage('login');
        })
        .fail((xhr, errorThrown, statusText) => {
          if (xhr.status == 400) {
            this.userModel.set(() => {
              this.userModel.registerErrors = xhr.responseJSON;
            });
          } else {
            alert('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
          }
        });
    });

    this.addEventListener('click', '#deletebutton', () => {
      var result = confirm(I18n.translate("Do you really want to delete your account?"));
      if (result) {
        var result = confirm(I18n.translate("This action cannot be undone. Are you sure? We will miss you ;("));
        if (result) {
          this.userService.deleteUser(this.userModel.currentUser)
            .then(() => {
              alert(I18n.translate('User deleted...'));
              this.userModel.set((model) => {
                model.registerErrors = {};
                this.userModel.logout();
                this.userService.logout();
                this.router.goToPage('register');
              });
            })
            .fail((xhr, errorThrown, statusText) => {
              alert('an error has occurred during request: ' + statusText + '.' + xhr.responseText);
            });
        }
      }
    });

  }

  /* AFTER RENDER hoook */
  afterRender(){
    this.import_css(this.listaCss)
  }

  /* STOP hook */
  onStop(){
    this.remove(this.listaCss)
  }

  /* START hook */
  onStart() {
    this.getUserInfo();
  }

  getUserInfo() {
    this.userService.findByUsername(this.userModel.currentUser).then((data) => {
      this.userModel.setUserinfo(data[0]);
    });
  }

}