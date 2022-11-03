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
                danger: colors.rose,
                primary: colors.amber,
                secondary: colors.gray,
                success: colors.green,
                warning: colors.amber,
            },
            fontFamily: {
                sans: ['DM Sans', ...defaultTheme.fontFamily.sans],
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
