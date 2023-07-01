import defaultPreset from '../support/tailwind.config.preset.js'
import defaultTheme from 'tailwindcss/defaultTheme'

defaultPreset.darkMode = 'class'
defaultPreset.theme.extend.fontFamily = {
    sans: ['var(--font-family)', ...defaultTheme.fontFamily.sans],
}

export default defaultPreset
