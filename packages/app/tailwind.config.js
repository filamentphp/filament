const colors = require('tailwindcss/colors')
const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
    content: [
        './packages/actions/resources/**/*.blade.php',
        './packages/app/resources/**/*.blade.php',
        './packages/forms/resources/**/*.blade.php',
        './packages/navigation/resources/**/*.blade.php',
        './packages/notifications/resources/**/*.blade.php',
        './packages/support/resources/**/*.blade.php',
        './packages/tables/resources/**/*.blade.php',
        './packages/widgets/resources/**/*.blade.php',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                danger: {
                    50: ({ opacityValue }) =>
                        `rgba(var(--danger-color-50), ${opacityValue})`,
                    100: ({ opacityValue }) =>
                        `rgba(var(--danger-color-100), ${opacityValue})`,
                    200: ({ opacityValue }) =>
                        `rgba(var(--danger-color-200), ${opacityValue})`,
                    300: ({ opacityValue }) =>
                        `rgba(var(--danger-color-300), ${opacityValue})`,
                    400: ({ opacityValue }) =>
                        `rgba(var(--danger-color-400), ${opacityValue})`,
                    500: ({ opacityValue }) =>
                        `rgba(var(--danger-color-500), ${opacityValue})`,
                    600: ({ opacityValue }) =>
                        `rgba(var(--danger-color-600), ${opacityValue})`,
                    700: ({ opacityValue }) =>
                        `rgba(var(--danger-color-700), ${opacityValue})`,
                    800: ({ opacityValue }) =>
                        `rgba(var(--danger-color-800), ${opacityValue})`,
                    900: ({ opacityValue }) =>
                        `rgba(var(--danger-color-900), ${opacityValue})`,
                },
                primary: {
                    50: ({ opacityValue }) =>
                        `rgba(var(--primary-color-50), ${opacityValue})`,
                    100: ({ opacityValue }) =>
                        `rgba(var(--primary-color-100), ${opacityValue})`,
                    200: ({ opacityValue }) =>
                        `rgba(var(--primary-color-200), ${opacityValue})`,
                    300: ({ opacityValue }) =>
                        `rgba(var(--primary-color-300), ${opacityValue})`,
                    400: ({ opacityValue }) =>
                        `rgba(var(--primary-color-400), ${opacityValue})`,
                    500: ({ opacityValue }) =>
                        `rgba(var(--primary-color-500), ${opacityValue})`,
                    600: ({ opacityValue }) =>
                        `rgba(var(--primary-color-600), ${opacityValue})`,
                    700: ({ opacityValue }) =>
                        `rgba(var(--primary-color-700), ${opacityValue})`,
                    800: ({ opacityValue }) =>
                        `rgba(var(--primary-color-800), ${opacityValue})`,
                    900: ({ opacityValue }) =>
                        `rgba(var(--primary-color-900), ${opacityValue})`,
                },
                secondary: {
                    50: ({ opacityValue }) =>
                        `rgba(var(--secondary-color-50), ${opacityValue})`,
                    100: ({ opacityValue }) =>
                        `rgba(var(--secondary-color-100), ${opacityValue})`,
                    200: ({ opacityValue }) =>
                        `rgba(var(--secondary-color-200), ${opacityValue})`,
                    300: ({ opacityValue }) =>
                        `rgba(var(--secondary-color-300), ${opacityValue})`,
                    400: ({ opacityValue }) =>
                        `rgba(var(--secondary-color-400), ${opacityValue})`,
                    500: ({ opacityValue }) =>
                        `rgba(var(--secondary-color-500), ${opacityValue})`,
                    600: ({ opacityValue }) =>
                        `rgba(var(--secondary-color-600), ${opacityValue})`,
                    700: ({ opacityValue }) =>
                        `rgba(var(--secondary-color-700), ${opacityValue})`,
                    800: ({ opacityValue }) =>
                        `rgba(var(--secondary-color-800), ${opacityValue})`,
                    900: ({ opacityValue }) =>
                        `rgba(var(--secondary-color-900), ${opacityValue})`,
                },
                success: {
                    50: ({ opacityValue }) =>
                        `rgba(var(--success-color-50), ${opacityValue})`,
                    100: ({ opacityValue }) =>
                        `rgba(var(--success-color-100), ${opacityValue})`,
                    200: ({ opacityValue }) =>
                        `rgba(var(--success-color-200), ${opacityValue})`,
                    300: ({ opacityValue }) =>
                        `rgba(var(--success-color-300), ${opacityValue})`,
                    400: ({ opacityValue }) =>
                        `rgba(var(--success-color-400), ${opacityValue})`,
                    500: ({ opacityValue }) =>
                        `rgba(var(--success-color-500), ${opacityValue})`,
                    600: ({ opacityValue }) =>
                        `rgba(var(--success-color-600), ${opacityValue})`,
                    700: ({ opacityValue }) =>
                        `rgba(var(--success-color-700), ${opacityValue})`,
                    800: ({ opacityValue }) =>
                        `rgba(var(--success-color-800), ${opacityValue})`,
                    900: ({ opacityValue }) =>
                        `rgba(var(--success-color-900), ${opacityValue})`,
                },
                warning: {
                    50: ({ opacityValue }) =>
                        `rgba(var(--warning-color-50), ${opacityValue})`,
                    100: ({ opacityValue }) =>
                        `rgba(var(--warning-color-100), ${opacityValue})`,
                    200: ({ opacityValue }) =>
                        `rgba(var(--warning-color-200), ${opacityValue})`,
                    300: ({ opacityValue }) =>
                        `rgba(var(--warning-color-300), ${opacityValue})`,
                    400: ({ opacityValue }) =>
                        `rgba(var(--warning-color-400), ${opacityValue})`,
                    500: ({ opacityValue }) =>
                        `rgba(var(--warning-color-500), ${opacityValue})`,
                    600: ({ opacityValue }) =>
                        `rgba(var(--warning-color-600), ${opacityValue})`,
                    700: ({ opacityValue }) =>
                        `rgba(var(--warning-color-700), ${opacityValue})`,
                    800: ({ opacityValue }) =>
                        `rgba(var(--warning-color-800), ${opacityValue})`,
                    900: ({ opacityValue }) =>
                        `rgba(var(--warning-color-900), ${opacityValue})`,
                },
            },
            fontFamily: {
                sans: 'var(--font-family)',
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
