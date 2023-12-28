class LoginComponent extends SuperModelComponent {
  constructor(userModel, router, listaCss) {
    super(Handlebars.templates.login, userModel, null , listaCss);
    this.userModel = userModel;
    this.userService = new UserService();
    this.router = router;
    this.listaCss = listaCss;

    this.addEventListener('click', '#loginbutton', (event) => {
      this.userService.login($('#login').val(), $('#password').val(), document.getElementById('rememberUser').checked)
        .then(() => {
          this.router.goToPage('spendings');
          this.userModel.setLoggeduser($('#login').val(),window.sessionStorage.getItem('role'));
        })
        .catch((error) => {
          this.userModel.set((model) => {
            model.loginError = error.responseText;
          });
          this.userModel.logout();
        });
    });

    this.addEventListener('click', '#registerlink', () => {
      this.userModel.set(() => {
        this.router.goToPage('register');
      });
    });

    this.addEventListener('click', '#logoutbutton', () => {
      this.userModel.logout();
      this.userService.logout();

      this.router.goToPage('login');
    });

    this.addChildComponent(this._createLanguageComponent());
  }

  _createLanguageComponent() {
    var languageComponent = new Fronty.ModelComponent(Handlebars.templates.language, this.router.getRouterModel(), 'login_languagechooser');
    // language change links
    languageComponent.addEventListener('click', '#englishlink', () => {
      I18n.changeLanguage('default');
      document.location.reload();
    });

    languageComponent.addEventListener('click', '#spanishlink', () => {
      I18n.changeLanguage('es');
      document.location.reload();
    });

    return languageComponent;
  }

  /* AFTER RENDER hook */
  afterRender(){
    this.import_css(this.listaCss)
  }

  /* STOP hook */
  onStop(){
    this.remove(this.listaCss)
  }
}