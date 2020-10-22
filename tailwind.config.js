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
        gray: {
          default: '#D4D8DD',
          100: '#FFFFFF',
          200: '#F6F7F8',
          300: '#EBEDEF',
          400: '#E0E3E6',
          500: '#D4D8DD',
          600: '#A4ACB7',
          700: '#738191',
          800: '#4C5661',
          900: '#262B31',
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
    customForms: (theme) => ({
      default: {
        input: {
          borderColor: theme('colors.gray.400'),
          boxShadow: theme('boxShadow.sm'),
        },
        checkbox: {
          borderColor: theme('colors.gray.400'),
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
