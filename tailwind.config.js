// tailwind.config.js
// const { colors,  } = require('tailwindcss/defaultTheme')

module.exports = {
  future: {
    removeDeprecatedGapUtilities: true,
    purgeLayersByDefault: true,
  },
  purge: ['./resources/views/**/*.php', './resources/js/**/*.js'],
  theme: {
    extend: {
      colors: {
        blue: {
          default: '#5487DE',
          100: '#FFFFFF',
          200: '#D4E1F7',
          300: '#A9C3EF',
          400: '#7EA5E7',
          500: '#5487DE',
          600: '#2968D6',
          700: '#2154AB',
          800: '#183F81',
          900: '#102A56',
        },
        green: {
          default: '#4E7E76',
          100: '#D9E8E5',
          200: '#B3D0CC',
          300: '#8DB9B2',
          400: '#67A298',
          500: '#4E7E76',
          600: '#406862',
          700: '#32524D',
          800: '#253C38',
          900: '#172623',
        },
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
    customForms: (theme) => ({
      default: {
        input: {
          borderColor: theme('colors.gray.400'),
          boxShadow: theme('boxShadow.sm'),
        },
        checkbox: {
          borderColor: theme('colors.gray.500'),
        },
      },
    }),
  },
  variants: {
    animation: ['responsive', 'motion-safe', 'motion-reduce'],
  },
  plugins: [
    require('@tailwindcss/custom-forms'),
    require('@tailwindcss/typography'),
  ],
}
