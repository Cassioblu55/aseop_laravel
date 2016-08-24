const elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.copy('node_modules/angular-ui-grid/ui-grid.ttf', 'public/build/css/');
    mix.copy('node_modules/angular-ui-grid/ui-grid.woff', 'public/build/css/');

    mix.browserify(['app.js'], 'public/js/app.js');

    mix.styles(['node_modules/angular-ui-grid/ui-grid.css',
        "node_modules/bootstrap/dist/css/bootstrap.css",
        "node_modules/bootstrap/dist/css/bootstrap-theme.css"
    ], 'public/css/app.css', './')

    mix.version(['css/app.css', 'js/app.js']);


});
