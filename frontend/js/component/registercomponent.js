class RegisterComponent extends SuperModelComponent {
  constructor(userModel, router, listaCss) {
    super(Handlebars.templates.register, userModel, null , listaCss);
    this.userModel = userModel;
    this.userService = new UserService();
    this.router = router;

    this.addEventListener('click', '#loginlink', () => {
      this.userModel.set(() => {
        this.router.goToPage('login');
      });
    });

    this.addEventListener('click', '#registerbutton', () => {
      this.userService.register({
          email: $('#registeremail').val(),
          username: $('#registerusername').val(),
          password: $('#registerpassword').val()
        })
        .then(() => {
          alert(I18n.translate('User registered! Please login'));
          this.userModel.set((model) => {
            model.registerErrors = {};
            this.router.goToPage('login');
          });
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

    this.addEventListener('click', '#logoutbutton', () => {
      this.userModel.logout();
      this.userService.logout();

      this.router.goToPage('login');
    });

    this.addChildComponent(this._createLanguageComponent());
  }

  _createLanguageComponent() {
    var languageComponent = new Fronty.ModelComponent(Handlebars.templates.language, this.router.getRouterModel(), 'register_languagechooser');
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

  /* AFTER RENDER hoook */
  afterRender(){
    this.import_css(this.listaCss)
  }

  /* STOP hook */
  onStop(){
    this.remove(this.listaCss)
  }
}