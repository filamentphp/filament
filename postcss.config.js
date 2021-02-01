module.exports = {
    plugins: [
        require("postcss-import"),
        require("postcss-nested"),
        require("tailwindcss")("./tailwind.config.js"),
        require("autoprefixer"),
    ],
};
