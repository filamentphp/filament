const defaultTheme = require('tailwindcss/defaultTheme')
const colors = require('tailwindcss/colors')

module.exports = {
  purge: {
    layers: ['utilities'],
    content: [
      './src/**/*.php',
      './resources/views/**/*.php',
      './resources/js/**/*.js',
      './packages/forms/src/**/*.php',
      './packages/forms/resources/views/**/*.php',
      './packages/tables/src/**/*.php',
      './packages/tables/resources/views/**/*.php',
    ],
  },
  theme: {
    extend: {
      fontFamily: {
        sans: ['Commissioner', ...defaultTheme.fontFamily.sans],
        mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
      },
      colors: {
        primary: {
          DEFAULT: '#C35547',
          50: '#FBF5F4',
          100: '#F5E3E1',
          200: '#E9C0BA',
          300: '#DC9C94',
          400: '#D0796D',
          500: '#C35547',
          600: '#A24135',
          700: '#7C3228',
          800: '#55221C',
          900: '#2F130F',
        },
        secondary: {
          DEFAULT: '#154A5F',
          50: '#78C3E1',
          100: '#63BADD',
          200: '#3AA8D3',
          300: '#278BB3',
          400: '#1E6B89',
          500: '#154A5F',
          600: '#113D4E',
          700: '#0E303E',
          800: '#0A232D',
          900: '#06161C',
        },
        success: colors.green,
        danger: colors.rose,
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
  },
  variants: {
    animation: ['responsive', 'motion-safe', 'motion-reduce'],
  },
  plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
}
