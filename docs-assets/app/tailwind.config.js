import preset from './vendor/filament/support/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/**/*.php',
        './resources/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
            },
            fontWeight: {
                normal: 300,
                medium: 400,
                semibold: 500,
                bold: 600,
            },
        },
    },
}
