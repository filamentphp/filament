import defaultPreset from '../support/tailwind.config.preset.js'
import defaultTheme from 'tailwindcss/defaultTheme'

defaultPreset.theme.extend.fontFamily = {
    mono: ['var(--mono-font-family)', ...defaultTheme.fontFamily.mono],
    sans: ['var(--font-family)', ...defaultTheme.fontFamily.sans],
    serif: ['var(--serif-font-family)', ...defaultTheme.fontFamily.serif],
}

export default defaultPreset
