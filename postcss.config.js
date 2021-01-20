module.exports = {
    plugins: [
        require('postcss-import'),
        require('postcss-nested'),
        require('tailwindcss')('./resources/css/tailwind.config.js'),
    ],
}
