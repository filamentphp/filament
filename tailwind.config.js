const defaultTheme = require('tailwindcss/defaultTheme')
const colors = require('tailwindcss/colors')

module.exports = {
  purge: {
    layers: ['utilities'],
    content: [
      './src/**/*.php',
      './resources/views/**/*.php',
      './resources/js/**/*.js',
    ],
    options: {
      safelist: {
        deep: [/grid/, /col/, /link/],
      },
    },
  },
  theme: {
    extend: {
      colors: {
        red: colors.rose,
        orange: colors.orange,
      },
      keyframes: {
        shake: {
          '10%, 90%': {
            transform: 'translate3d(-1px, 0, 0)',
          },
          '20%, 80%': {
            transform: 'translate3d(2px, 0, 0)',
          },
          '30%, 50%, 70%': {
            transform: 'translate3d(-4px, 0, 0)',
          },
          '40%, 60%': {
            transform: 'translate3d(4px, 0, 0)',
          },
        },
      },
    },
    container: {
      center: true,
    },
  },
  variants: {
    animation: ['responsive', 'motion-safe', 'motion-reduce'],
  },
  plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
}
