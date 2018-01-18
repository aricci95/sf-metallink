var Encore = require('@symfony/webpack-encore');

Encore
    .enableReactPreset()
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // will output as app/Resources/webpack/server-bundle.js
    .addEntry('server-bundle', ['babel-polyfill', './assets/js/react/entryPoint.js']
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning(Encore.isProduction())

    // uncomment to define the assets of the project
    .addEntry('js/app', './assets/js/app.js')
    .addEntry('js/user_search', './assets/js/user_search.js')
    .addEntry('js/message_search', './assets/js/message_search.js')
    .addEntry('js/view_search', './assets/js/view_search.js')
    .addEntry('js/link_search', './assets/js/link_search.js')
    .addEntry('js/picture', './assets/js/picture.js')
    .addEntry('css/app', './assets/css/app.css')
    // .addStyleEntry('css/app', './assets/css/app.scss')

    // uncomment if you use Sass/SCSS files
    // .enableSassLoader()

    // uncomment for legacy applications that require $/jQuery as a global variable
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
