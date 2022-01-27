export default () => ({
    theme: localStorage.getItem('theme') || localStorage.setItem('theme', 'system'),

    init() {
        this.$watch('theme', value => {
            localStorage.setItem('theme', value)
        })
    }
})
