import defaultPreset from '../support/tailwind.config.preset.js'
import defaultTheme from 'tailwindcss/defaultTheme'

defaultPreset.theme.extend.fontFamily = {
    sans: ['var(--font-family)', ...defaultTheme.fontFamily.sans],
}

defaultPreset.theme.extend.fontWeight = {
    normal: 'var(--font-weight-normal)',
    medium: 'var(--font-weight-medium)',
    semibold: 'var(--font-weight-semibold)',
    bold: 'var(--font-weight-bold)',
}

export default defaultPreset
