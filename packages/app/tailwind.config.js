const preset = require('./tailwind.config.preset')

module.exports = {
    presets: [preset],
    content: ['./packages/**/*.blade.php'],
}
