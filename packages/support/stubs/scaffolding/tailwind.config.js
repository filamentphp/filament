import colors from 'tailwindcss/colors'
import forms from '@tailwindcss/forms'
import typography from '@tailwindcss/typography'

export default {
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
    plugins: [forms, typography],
}
