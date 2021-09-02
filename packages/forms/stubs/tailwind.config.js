const colors = require('tailwindcss/colors')

module.exports = {
    mode: 'jit',
    purge: [
        './resources/**/*.blade.php',
        './vendor/filament/forms/resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                danger: colors.rose,
                primary: colors.yellow,
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
