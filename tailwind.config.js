// tailwind.config.js
// const { colors, ... } = require('tailwindcss/defaultTheme')

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
      },
    },
  },
  variants: {},
  plugins: [
    require('@tailwindcss/custom-forms'),
    require('@tailwindcss/typography'),
  ],
}
