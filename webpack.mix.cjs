const mix = require('laravel-mix');
const path = require('path');

mix
  .js('resources/js/app.js', 'public/js')
  .vue({ version: 3 })
  .postCss('resources/css/app.css', 'public/css', [
    require('autoprefixer'),
  ])
  .webpackConfig({
    resolve: {
      alias: {
        '@': path.resolve('resources/js'),
      }
    },
    plugins: [
      new (require('webpack')).DefinePlugin({
        __VUE_OPTIONS_API__: true,
        __VUE_PROD_DEVTOOLS__: false,
        __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: false
      })
    ]
  })
  .sourceMaps(false);

if (mix.inProduction()) {
  mix.version();
}
  // .babelConfig({
  //   presets: [
  //     [
  //       '@babel/preset-env',
  //       {
  //         targets: 'last 2 versions',
  //         useBuiltIns: 'usage',
  //         corejs: 3,
  //       },
  //     ],
  //   ],
  