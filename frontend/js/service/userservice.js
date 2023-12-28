class UserService {
  constructor() {

  }

  loginWithSessionData() {
    var self = this;
    return new Promise((resolve, reject) => {
      if (window.sessionStorage.getItem('login') &&
        window.sessionStorage.getItem('pass')) {
        self.login(window.sessionStorage.getItem('login'), window.sessionStorage.getItem('pass'))
          .then(() => {
            var role = window.sessionStorage.getItem('role');
            resolve({login:window.sessionStorage.getItem('login'),role:role});
          })
          .catch(() => {
            reject();
          });
      } else {
        resolve(null);
      }
    });
  }

  loginWithCookies() {
    var self = this;
    var alias = this.getCookie("alias");
    var pass = this.getCookie("contrasena");

    return new Promise((resolve, reject) => {
      if (alias && pass) {
        self.login(alias, pass)
          .then(() => {
            var role = window.sessionStorage.getItem('role');
            resolve({login:alias,role:role});
          })
          .catch(() => {
            reject();
          });
      } else {
        resolve(null);
      }
    });
  }

  login(login, pass, rememberUser=false) {
    return new Promise((resolve, reject) => {

      $.get({
          url: AppConfig.backendServer+'/rest/user/' + login,
          beforeSend: function(xhr) {
            // get password hash
            var hashVal = 0;
            if (pass.length != 0){
              for (var i = 0; i < pass.length; i++) {
                var char = pass.charCodeAt(i);
                var hashVal = ((hashVal << 5) - hashVal) + char;
                hashVal = hashVal & hashVal;
              }
            }
            xhr.setRequestHeader("Authorization", "Basic " + btoa(login + ":" + hashVal));
          }
        })
        .then((role) => {
          //keep this authentication forever
          window.sessionStorage.setItem('login', login);
          window.sessionStorage.setItem('pass', pass);
          window.sessionStorage.setItem('role', role);

          //set cookie for 30 days
          if(rememberUser){
            this.generateLoginCookies(login,pass);
          }

          // get password hash
          var hashVal = 0;
          if (pass.length != 0){
            for (var i = 0; i < pass.length; i++) {
              var char = pass.charCodeAt(i);
              var hashVal = ((hashVal << 5) - hashVal) + char;
              hashVal = hashVal & hashVal;
            }
          }

          $.ajaxSetup({
            beforeSend: (xhr) => {
              xhr.setRequestHeader("Authorization", "Basic " + btoa(login + ":" + hashVal));
            }
          });

          resolve();
        })
        .fail((error) => {
          window.sessionStorage.removeItem('login');
          window.sessionStorage.removeItem('pass');
          window.sessionStorage.removeItem('role');
          this.deleteLoginCookies();
          $.ajaxSetup({
            beforeSend: (xhr) => {}
          });
          reject(error);
        });
    });
  }

  logout() {
    window.sessionStorage.removeItem('login');
    window.sessionStorage.removeItem('pass');

    this.deleteLoginCookies();

    $.ajaxSetup({
      beforeSend: (xhr) => {}
    });
  }

  register(user) {
    // get password hash
    var hashVal = 0;

    if (user.password.length == 0) 
      user.password = hashVal;
    else {
      for (var i = 0; i < user.password.length; i++) {
        var char = user.password.charCodeAt(i);
        var hashVal = ((hashVal << 5) - hashVal) + char;
        hashVal = hashVal & hashVal;
      }
      
      user.password = hashVal;
    }

    return $.ajax({
      url: AppConfig.backendServer+'/rest/user',
      method: 'POST',
      data: JSON.stringify(user),
      contentType: 'application/json'
    });
  }

  /* UPDATE USER */
  updateUser(user) {
    // get password hash
    var hashVal = 0;

    if (user.password.length == 0) 
      user.password = hashVal;
    else {
      for (var i = 0; i < user.password.length; i++) {
        var char = user.password.charCodeAt(i);
        var hashVal = ((hashVal << 5) - hashVal) + char;
        hashVal = hashVal & hashVal;
      }
      
      user.password = hashVal;
    }

    return $.ajax({
      url: AppConfig.backendServer+'/rest/user/' + user.username,
      method: 'PUT',
      data: JSON.stringify(user),
      contentType: 'application/json'
    });
  }

  /* DELETE USER */
  deleteUser(username) {
    return $.ajax({
      url: AppConfig.backendServer+'/rest/user/' + username,
      method: 'DELETE'
    });
  }

  /* GET USER */
  findByUsername(username) {
    return $.get(AppConfig.backendServer+'/rest/user/' + username + '/info');
  }
  
  /* Función para establecer el valor de la cookie */
  setCookie(name, value, days) {
    let expires = "";

    if (days) {
        let date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }

    document.cookie = name + "=" + (value || "") + expires + "; path=/";
  }

  /* Función para obtener el valor de la cookie */
  getCookie(cname) {
    var name = cname + '=';
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return '';
  }

  /** Función para eliminar una cookie */
  deleteCookie(cname) {
    document.cookie = cname + '=;expires=Thu, 01 Jan 1970 00:00:01 GMT;path=/';
  }

  deleteLoginCookies() {
    this.deleteCookie("alias");
    this.deleteCookie("contrasena");
  }

  /** Función para generar una cookie que almacene alias y contraseña */
  generateLoginCookies(valor_alias,valor_contrasena) {
    this.setCookie("alias", valor_alias, 30);
    this.setCookie("contrasena", valor_contrasena, 30);
  }

}
