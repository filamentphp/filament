// tailwind.config.js
const { colors } = require('tailwindcss/defaultTheme')

module.exports = {
  future: {
    removeDeprecatedGapUtilities: true,
    purgeLayersByDefault: true,
  },
  purge: ['./resources/views/**/*.php', './resources/js/**/*.js'],
  theme: {
    extend: {
      colors: {
        gray: {
          ...colors.gray,
          100: '#F8FAFC',
        },
        blue: {
          ...colors.blue,
          800: '#0C1D50',
          700: '#173673',
        },
      },
    },
  },
  variants: {},
  plugins: [],
}
