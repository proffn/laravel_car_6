const mix = require('laravel-mix');

// Отключаем все PostCSS плагины
mix.options({
    postCss: [],
    processCssUrls: false
});

mix.js('resources/js/app.js', 'public/js')
   .sass('resources/sass/app.scss', 'public/css')
   .copyDirectory('resources/images', 'public/images');