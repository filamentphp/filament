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
        mono: ['Space Mono', ...defaultTheme.fontFamily.mono],
      },
      colors: {
        primary: {
            100: 'var(--f-primary-100)',
            200: 'var(--f-primary-200)',
            300: 'var(--f-primary-300)',
            400: 'var(--f-primary-400)',
            500: 'var(--f-primary-500)',
            600: 'var(--f-primary-600)',
            700: 'var(--f-primary-700)',
            800: 'var(--f-primary-800)',
            900: 'var(--f-primary-900)',
        },
        success: {
            100: 'var(--f-success-100)',
            200: 'var(--f-success-200)',
            300: 'var(--f-success-300)',
            400: 'var(--f-success-400)',
            500: 'var(--f-success-500)',
            600: 'var(--f-success-600)',
            700: 'var(--f-success-700)',
            800: 'var(--f-success-800)',
            900: 'var(--f-success-900)',
        },
        danger: {
            100: 'var(--f-danger-100)',
            200: 'var(--f-danger-200)',
            300: 'var(--f-danger-300)',
            400: 'var(--f-danger-400)',
            500: 'var(--f-danger-500)',
            600: 'var(--f-danger-600)',
            700: 'var(--f-danger-700)',
            800: 'var(--f-danger-800)',
            900: 'var(--f-danger-900)',
        },
        gray: {
            100: 'var(--f-gray-100)',
            200: 'var(--f-gray-200)',
            300: 'var(--f-gray-300)',
            400: 'var(--f-gray-400)',
            500: 'var(--f-gray-500)',
            600: 'var(--f-gray-600)',
            700: 'var(--f-gray-700)',
            800: 'var(--f-gray-800)',
            900: 'var(--f-gray-900)',
        },
        blue: {
            100: 'var(--f-blue-100)',
            200: 'var(--f-blue-200)',
            300: 'var(--f-blue-300)',
            400: 'var(--f-blue-400)',
            500: 'var(--f-blue-500)',
            600: 'var(--f-blue-600)',
            700: 'var(--f-blue-700)',
            800: 'var(--f-blue-800)',
            900: 'var(--f-blue-900)',
        },
        white: 'var(--f-white)',
        defaultPrimary: colors.lightBlue,
        defaultSuccess: colors.emerald,
        defaultDanger: colors.rose,
        defaultGray: colors.coolGray,
        defaultBlue: colors.blue,
        defaultWhite: colors.white
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
    extend: {
      padding: ['direction'],
      translate: ['direction'],
    }
  },
  plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography'), require('tailwindcss-dir')()],
}
