const mix = require('laravel-mix');

mix
  .js('resources/js/app.js', 'public/js')
  .vue()
  .postCss('resources/css/app.css', 'public/css', [
    // Usamos la versión correcta de Tailwind
    require('tailwindcss'),
    require('autoprefixer'),
  ])
  .babelConfig({
    presets: [
      [
        '@babel/preset-env',
        {
          targets: 'last 2 versions',
          useBuiltIns: 'usage',
          corejs: 3,
        },
      ],
    ],
  });
