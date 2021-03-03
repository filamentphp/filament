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
          50: '#F4FDFC',
          100: '#DBF7F5',
          200: '#A8ECE8',
          300: '#76E2DB',
          400: '#43D7CE',
          500: '#27B6AD',
          600: '#20948D',
          700: '#19736D',
          800: '#11514D',
          900: '#0A302D',
        },
        secondary: {
          50: '#F5F9FE',
          100: '#E1ECFB',
          200: '#B8D3F5',
          300: '#90BAF0',
          400: '#67A0EA',
          500: '#3F87E5',
          600: '#1D6CD4',
          700: '#1755A7',
          800: '#113E7A',
          900: '#0A284E',
        },
        success: colors.emerald,
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
      typography: (theme) => ({
        DEFAULT: {
          css: {
            a: {
              color: theme('colors.secondary.700'),
              '&:hover': {
                color: theme('colors.secondary.500'),
              },
            },
          },
        },
      }),
    },
  },
  variants: {
    animation: ['responsive', 'motion-safe', 'motion-reduce'],
  },
  plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
}
