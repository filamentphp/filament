module.exports = {
    plugins: [
        require("postcss-import"),
        require("postcss-nested"),
        require("@tailwindcss/jit"),
        require("autoprefixer"),
    ],
};
