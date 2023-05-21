const colors = require('tailwindcss/colors')

module.exports = {
    content: ['./resources/**/*.blade.php', './vendor/filament/**/*.blade.php'],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                danger: colors.red,
                info: colors.blue,
                primary: colors.amber,
                secondary: colors.gray,
                success: colors.green,
                warning: colors.amber,
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
