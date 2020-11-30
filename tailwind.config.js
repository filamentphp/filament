// const defaultTheme = require('tailwindcss/defaultTheme')
const colors = require('tailwindcss/colors')

module.exports = {
  purge: ['./resources/views/**/*.php', './resources/js/**/*.js'],
  theme: {
    extend: {
      colors: {
        red: colors.rose,
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
      animation: {
        shake: 'shake 0.82s cubic-bezier(.36, .07, .19, .97) both',
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
