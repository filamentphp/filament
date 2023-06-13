import colors from 'tailwindcss/colors'
import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'

export default {
    content: ['./resources/**/*.blade.php', './vendor/filament/**/*.blade.php'],
    theme: {
        extend: {
            colors: {
                danger: colors.rose,
                info: colors.blue,
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
    plugins: [forms, typography],
}
