const mix = require("laravel-mix");

mix.js("resources/js/app.js", "public/js")
    .vue()
    .postCss("resources/css/app.css", "public/css", [
     require('postcss-import'),
     // require('tailwindcss'), // descomenta si usas Tailwind
   ])
   .sourceMaps();

if (mix.inProduction()) {
    mix.version();
} else {
    mix.sourceMaps();
}

// const mix = require("laravel-mix");

// mix.js('resources/js/app.js', 'public/js')
//    .vue({ version: 3 }) // usa 2 si tu proyecto es Vue 2
//    .postCss('resources/css/app.css', 'public/css', [
//        require('postcss-import'),
//        require('tailwindcss'),
//        require('autoprefixer'),
//    ]);

// if (mix.inProduction()) {
//   mix.version();
// } else {
//   mix.sourceMaps();
// }
