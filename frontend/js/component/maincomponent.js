class MainComponent extends Fronty.RouterComponent {
  constructor() {
    super('frontyapp', Handlebars.templates.main, 'maincontent');

    // models instantiation
    // we can instantiate models at any place
    this.userModel = new UserModel();
    this.spendingsModel = new SpendingsModel();
    this.graphModel = new GraphModel();
    // services instantiation
    this.userService = new UserService();
    this.spendingService = new SpendingsService();

    super.setRouterConfig({
      'spendings': {
        component: new SpendingsComponent(this.spendingsModel, this.userModel, this, 
          ['css/showall_spendings.css','css/navbar.css','css/navbarMQs.css']),
        title: I18n.translate('Spendings')
      },
      'analysis': {
        component: new AnalysisComponent(this.graphModel, this.userModel, this, 
          ['css/navbar.css','css/navbarMQs.css','css/analysis_panel.css','css/graphs.css','css/searchMQs.css']),
        title: I18n.translate('Analysis Panel')
      },
      'add-spending': {
        component: new SpendingAddComponent(this.spendingsModel, this.userModel, this,
          ['css/navbar.css','css/navbarMQs.css','css/formCRUD.css'], "form_add_spending"),
        title: I18n.translate('Add Spending')
      },
      'edit-spending': {
        component: new SpendingEditComponent(this.spendingsModel, this.userModel, this,
          ['css/navbar.css','css/navbarMQs.css','css/formCRUD.css'], "form_edit_spending"),
        title: I18n.translate('Edit Spending')
      },
      'login': {
        component: new LoginComponent(this.userModel, this, 
          ['css/login.css']),
        title: I18n.translate('Login')
      },
      'register': {
        component: new RegisterComponent(this.userModel, this,
          ['css/register.css']),
        title: I18n.translate('Register')
      },
      'edit-user': {
        component: new UserEditComponent(this.userModel, this,
          ['css/navbar.css','css/navbarMQs.css','css/edit-user.css']),
        title: I18n.translate('User Edit')
      },
      defaultRoute: 'login'
    });

    Handlebars.registerHelper('currentPage', () => {
          return super.getCurrentPage();
    });

    
    this.addChildComponent(this._createUserBarComponent());
    this.addChildComponent(this._createFooter());
    
  }

  start() {
    // override the start() function in order to first check if there is a logged user
    // in sessionStorage, so we try to do a relogin and start the main component
    // only when login is checked
    this.userService.loginWithSessionData()
      .then((obj) => {
        if (obj != null && obj.login != null) {
          this.userModel.setLoggeduser(obj.login,obj.role);
          super.start(); // now we can call start
        } else{
          // check if there is a logged user in cookies
          this.userService.loginWithCookies()
            .then((obj) => {
              if (obj != null && obj.login != null) {
                this.userModel.setLoggeduser(obj.login,obj.role);
              }
              super.start(); // now we can call start
            })
        }
        
        
      })
    
  
    }


  _createLanguageComponent() {
    var languageComponent = new Fronty.ModelComponent(Handlebars.templates.language, this.getRouterModel(), 'languagechooser');

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
  
  _createFooter() {
    var footer = new Fronty.ModelComponent(Handlebars.templates.footer, this.getRouterModel(), 'main_footer');

    return footer;
  }

  _createUserBarComponent() {
    var userbar = new Fronty.ModelComponent(Handlebars.templates.menu, this.userModel, 'header_navbar');
    userbar.addModel('router', this.getRouterModel());
    userbar.addChildComponent(this._createLanguageComponent());
    userbar.addEventListener('click', '#logoutbutton', () => {
      this.userModel.logout();
      this.userService.logout();

      this.goToPage('login');
    });
    userbar.addEventListener('click', '#currentuser', () => {
      this.goToPage('edit-user');
    });

    return userbar;
  }


}
