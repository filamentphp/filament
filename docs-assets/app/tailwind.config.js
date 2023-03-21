const colors = require('tailwindcss/colors')

module.exports = {
    content: ['./resources/**/*.blade.php', './vendor/filament/**/*.blade.php'],
    theme: {
        extend: {
            colors: {
                danger: colors.rose,
                primary: colors.blue,
                secondary: colors.gray,
                success: colors.green,
                warning: colors.yellow,
            },
            fontFamily: {
                sans: ['Be Vietnam Pro', 'sans-serif'],
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
