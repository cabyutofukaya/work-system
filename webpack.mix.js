const mix = require('laravel-mix');

require('vuetifyjs-mix-extension');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
    .vuetify(
        'vuetify-loader',
        'resources/sass/vuetify/variables.scss'
    )
    .vue({ version: 2 })
    .postCss('resources/css/app.css', 'public/css', [])
    .sass('resources/sass/app.scss', 'public/css')
    .version()
    .sourceMaps(true, 'source-map');

mix.webpackConfig({
    resolve: {
        extensions: ['.js', '.vue', '.json', '*']
    }
});
