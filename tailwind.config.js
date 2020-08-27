const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
  future: {
    removeDeprecatedGapUtilities: true,
  },
  purge: [
    './config/*.php',
    './src/**/*.php',
    './resources/views/**/*.php',
    './resources/js/**/*.js',
    '../field-file/resources/views/**/*.php', // used for locally developing sibling packages
  ],
  theme: {
    extend: {
      colors: {
        current: 'currentColor',
      },
      fontFamily: {
        sans: ['Inter var', ...defaultTheme.fontFamily.sans],
      },
    },
  },
  variants: {
    backgroundColor: [
      'responsive',
      'hover',
      'group-hover',
      'focus',
      'active',
      'even',
      'odd',
      'dark',
      'dark-hover',
      'dark-group-hover',
      'dark-even',
      'dark-odd',
    ],
    borderColor: [
      'focus',
      'focus-within',
      'dark',
      'dark-focus',
      'dark-focus-within',
    ],
    textColor: ['hover', 'active', 'dark', 'dark-hover', 'dark-active'],
  },
  plugins: [
    require('tailwindcss-dark-mode')(),
    require('@tailwindcss/ui')({
      layout: 'sidebar',
    }),
  ],
}
