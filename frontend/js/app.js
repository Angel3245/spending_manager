/* Main mvcblog-front script */

//load external resources
function loadTextFile(url) {
  return new Promise((resolve, reject) => {
    $.get({
      url: url,
      cache: true,
      beforeSend: function( xhr ) {
        xhr.overrideMimeType( "text/plain" );
      }
    }).then((source) => {
      resolve(source);
    }).fail(() => reject());
  });
}


// Configuration
var AppConfig = {
  backendServer: 'http://localhost/spending_manager'
  //backendServer: '/mvcblog'
}

Handlebars.templates = {};
Promise.all([
    I18n.initializeCurrentLanguage('js/i18n'),
    loadTextFile('templates/components/main.hbs').then((source) =>
      Handlebars.templates.main = Handlebars.compile(source)),
    loadTextFile('templates/components/language.hbs').then((source) =>
      Handlebars.templates.language = Handlebars.compile(source)),
    loadTextFile('templates/components/menu.hbs').then((source) =>
      Handlebars.templates.menu = Handlebars.compile(source)),
    loadTextFile('templates/components/footer.hbs').then((source) =>
      Handlebars.templates.footer = Handlebars.compile(source)),
    loadTextFile('templates/components/login.hbs').then((source) =>
      Handlebars.templates.login = Handlebars.compile(source)),
    loadTextFile('templates/components/register.hbs').then((source) =>
      Handlebars.templates.register = Handlebars.compile(source)),
    loadTextFile('templates/components/user-edit.hbs').then((source) =>
      Handlebars.templates.useredit = Handlebars.compile(source)),
    loadTextFile('templates/components/spendings-table.hbs').then((source) =>
      Handlebars.templates.spendingstable = Handlebars.compile(source)),
    loadTextFile('templates/components/spending-add.hbs').then((source) =>
      Handlebars.templates.spendingadd = Handlebars.compile(source)),
    loadTextFile('templates/components/spending-edit.hbs').then((source) =>
      Handlebars.templates.spendingedit = Handlebars.compile(source)),
    loadTextFile('templates/components/spendings-analysis.hbs').then((source) =>
      Handlebars.templates.spendingsanalysis = Handlebars.compile(source))
  ])
  .then(() => {
    $(() => {
      new MainComponent().start();
    });
  }).catch((err) => {
    alert('FATAL: could not start app ' + err);
  });
