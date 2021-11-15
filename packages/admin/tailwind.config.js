const colors = require('tailwindcss/colors')
const defaultTheme = require('tailwindcss/defaultTheme')

module.exports = {
    mode: 'jit',
    purge: [
        './packages/admin/resources/**/*.blade.php',
        './packages/forms/resources/**/*.blade.php',
        './packages/tables/resources/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                danger: colors.rose,
                gray: colors.warmGray,
                primary: colors.yellow,
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
