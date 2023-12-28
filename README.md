# Spending manager
A "single-page" application for managing spendings based on:
- [fronty.js](https://github.com/lipido/fronty.js), a simple library for building Component-based Web user interfaces
- [jQuery.js](https://jquery.com/)
- [Handlebars](http://handlebarsjs.com/)

**Note: This is an educational project.**

The front-end is responsive, uses JavaScript and AJAX and interacts with the backend
via its REST API.

**Screenshots:**

<img src="docs/screenshots/login.PNG?raw=true" width="500" height="250">
<img src="docs/screenshots/spendings.PNG?raw=true" width="500" height="250">
<img src="docs/screenshots/analysis_panel.PNG?raw=true" width="500" height="500">

## Requirements
1. PHP.
2. MySQL.
3. A PHP-capable HTTP Server.

## Architecture overview

The base architecture is defined by
[fronty.js](https://github.com/lipido/fronty.js). In this sense, the main
artifacts are:

- **Models**, which are JavaScript objects containing application state, like
  `PostsModel` and `UserModel`.
- **Components**, which are JavaScript objects in charge of rendering different
  parts of the application.
- **Renderers** in [Handlebars](http://handlebarsjs.com/) language containing the
  HTML fragments separated from JavaScript.

In addition, this application includes a library for Internationalization (I18n)
in `js/i18n` folder.

## Installation

A quick installation process could be:

1. Download [spending_manager](https://github.com/Angel3245/spending_manager).
2. Import the SQL file **moneyspendings.sql** into your MySQL DB (it will create a DB called moneyspendings with a user admin).
3. Start your server and access it: http://localhost/spending_manager/frontend/index.html.

## Contributors
- [Jose Ángel Pérez Garrido](https://github.com/Angel3245)
- [Miguel José Da Silva Araujo](https://github.com/Enmiguelado)
- [Juan Yuri Díaz Sánchez](https://github.com/juanyuri)

Based on mvcblog from [lipido](https://github.com/lipido)