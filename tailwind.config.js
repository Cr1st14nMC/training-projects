/** @type {import('tailwindcss').Config} */
module.exports = {
    prefix: 'tw-',
    content: [
      './resources/**/*.{vue,js,ts,jsx,tsx,html,php,blade.php}',
    ],
    theme: {
      extend: {
        colors: {
          'purple': '#6C63FF',
          'primary': '#F47B21',
          'secondary': '#00BF63'
        },
        textColor: {
          'purple': '#6C63FF',
          'primary': '#F47B21',
          'secondary': '#00BF63'
        }
      },
    },
    plugins: [],
  }
  